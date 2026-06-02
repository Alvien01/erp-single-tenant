<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Account;
use App\Models\Journal;
use App\Models\Asset;
use App\Models\AssetDepreciation;
use App\Models\ActivityLog;

class AccountingManager extends Component
{
    use WithPagination;

    public $search = '';
    public $activeTab = 'coa';
    public $journal_date;
    public $description;
    public $debit_account_id;
    public $credit_account_id;
    public $amount = 0;
    public $isOpen = false;
    public $isOpenAssetModal = false;
    public $asset_code;
    public $asset_name;
    public $asset_category = 'Equipment';
    public $asset_purchase_date;
    public $asset_purchase_price;
    public $asset_useful_life = 48; 
    public $asset_residual_value = 0;

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetJournalFields();
    }

    public function resetJournalFields()
    {
        $this->journal_date = now()->format('Y-m-d');
        $this->description = '';
        $this->debit_account_id = '';
        $this->credit_account_id = '';
        $this->amount = 0;
    }

    public function createJournal()
    {
        $this->resetJournalFields();
        $this->openModal();
    }

    public function storeJournal()
    {
        $this->validate([
            'journal_date' => 'required|date',
            'description' => 'required|string',
            'debit_account_id' => 'required|exists:accounts,id|different:credit_account_id',
            'credit_account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:1000',
        ]);
        
        // Generate robust, collision-free unique journal reference number
        $todayPrefix = 'JV-' . now()->format('Ymd') . '-';
        $lastJournal = Journal::where('reference_number', 'like', $todayPrefix . '%')
            ->orderBy('reference_number', 'desc')
            ->first();

        if ($lastJournal) {
            $parts = explode('-', $lastJournal->reference_number);
            $lastSeq = intval(end($parts));
            $seq = $lastSeq + 1;
        } else {
            $seq = 1;
        }

        $ref = $todayPrefix . sprintf('%04d', $seq);

        Journal::create([
            'account_id' => $this->debit_account_id,
            'transaction_date' => $this->journal_date,
            'description' => $this->description,
            'reference_number' => $ref,
            'amount' => $this->amount,
            'type' => 'debit',
        ]);

        Journal::create([
            'account_id' => $this->credit_account_id,
            'transaction_date' => $this->journal_date,
            'description' => $this->description,
            'reference_number' => $ref,
            'amount' => $this->amount,
            'type' => 'credit',
        ]);

        $debitAcc = Account::find($this->debit_account_id);
        if ($debitAcc->type === 'asset' || $debitAcc->type === 'expense') {
            $debitAcc->increment('balance', $this->amount);
        } else {
            $debitAcc->decrement('balance', $this->amount);
        }

        $creditAcc = Account::find($this->credit_account_id);
        if ($creditAcc->type === 'liability' || $creditAcc->type === 'equity' || $creditAcc->type === 'revenue') {
            $creditAcc->increment('balance', $this->amount);
        } else {
            $creditAcc->decrement('balance', $this->amount);
        }

        ActivityLog::create([
            'user_id' => auth()->id() ?? 1,
            'module' => 'Accounting',
            'action' => 'Create Journal Entry',
            'description' => 'Journal entry ' . $ref . ' posted: Debit ' . $debitAcc->name . ', Credit ' . $creditAcc->name . ' for Rp ' . number_format($this->amount, 0, ',', '.')
        ]);

        session()->flash('success', 'Journal entry posted successfully.');
        $this->closeModal();
    }

    // Asset methods
    public function createAsset()
    {
        $this->asset_code = 'AST-' . now()->format('Y') . '-' . sprintf('%04d', Asset::count() + 1);
        $this->asset_name = '';
        $this->asset_category = 'Equipment';
        $this->asset_purchase_date = now()->format('Y-m-d');
        $this->asset_purchase_price = 0;
        $this->asset_useful_life = 48;
        $this->asset_residual_value = 0;
        $this->isOpenAssetModal = true;
    }

    public function storeAsset()
    {
        $this->validate([
            'asset_code' => 'required|string|unique:assets,asset_code',
            'asset_name' => 'required|string|max:255',
            'asset_category' => 'required|string',
            'asset_purchase_date' => 'required|date',
            'asset_purchase_price' => 'required|numeric|min:100000',
            'asset_useful_life' => 'required|integer|min:1',
            'asset_residual_value' => 'required|numeric|min:0|max:' . ($this->asset_purchase_price ?? 999999999),
        ]);

        Asset::create([
            'asset_code' => $this->asset_code,
            'asset_name' => $this->asset_name,
            'category' => $this->asset_category,
            'purchase_date' => $this->asset_purchase_date,
            'purchase_price' => $this->asset_purchase_price,
            'useful_life_months' => $this->asset_useful_life,
            'residual_value' => $this->asset_residual_value,
        ]);

        ActivityLog::create([
            'user_id' => auth()->id() ?? 1,
            'module' => 'Accounting',
            'action' => 'Register Asset',
            'description' => 'Fixed Asset registered: ' . $this->asset_name . ' (' . $this->asset_code . ')'
        ]);

        session()->flash('success', 'Asset registered successfully.');
        $this->isOpenAssetModal = false;
    }

    public function runDepreciation()
    {
        $assets = Asset::all();
        $processedCount = 0;

        // Ensure accounts for depreciation exist
        $depExpenseAcc = Account::firstOrCreate(
            ['code' => '5299'],
            ['name' => 'Depreciation Expense', 'type' => 'expense', 'balance' => 0]
        );

        $accumDepAcc = Account::firstOrCreate(
            ['code' => '1299'],
            ['name' => 'Accumulated Depreciation', 'type' => 'asset', 'balance' => 0]
        );

        foreach ($assets as $asset) {
            $existingMonths = AssetDepreciation::where('asset_id', $asset->id)->count();

            if ($existingMonths >= $asset->useful_life_months) {
                continue; // Fully depreciated
            }

            // Straight-line monthly calculation
            $monthlyAmount = ($asset->purchase_price - $asset->residual_value) / $asset->useful_life_months;

            // Calculate next depreciation date
            $purchaseTime = strtotime($asset->purchase_date);
            $nextMonthTime = strtotime("+" . ($existingMonths + 1) . " months", $purchaseTime);
            $depreciationDate = date('Y-m-d', $nextMonthTime);

            // Avoid calculating future dates past today
            if (strtotime($depreciationDate) > time()) {
                continue;
            }

            $accumulated = AssetDepreciation::where('asset_id', $asset->id)->sum('amount') + $monthlyAmount;
            $bookValue = $asset->purchase_price - $accumulated;

            AssetDepreciation::create([
                'asset_id' => $asset->id,
                'depreciation_date' => $depreciationDate,
                'amount' => $monthlyAmount,
                'accumulated' => $accumulated,
                'book_value' => $bookValue,
            ]);

            // Create Journal Entry
            $ref = 'DEP-' . now()->format('Ymd') . '-' . sprintf('%04d', $asset->id);

            Journal::create([
                'account_id' => $depExpenseAcc->id,
                'transaction_date' => $depreciationDate,
                'description' => "Depreciation of {$asset->asset_name} - Month " . ($existingMonths + 1),
                'reference_number' => $ref,
                'amount' => $monthlyAmount,
                'type' => 'debit',
            ]);

            Journal::create([
                'account_id' => $accumDepAcc->id,
                'transaction_date' => $depreciationDate,
                'description' => "Accum. Depreciation of {$asset->asset_name} - Month " . ($existingMonths + 1),
                'reference_number' => $ref,
                'amount' => $monthlyAmount,
                'type' => 'credit',
            ]);

            $depExpenseAcc->increment('balance', $monthlyAmount);
            $accumDepAcc->decrement('balance', $monthlyAmount); // AccDep is a contra-asset, reducing total asset value

            $processedCount++;
        }

        if ($processedCount > 0) {
            ActivityLog::create([
                'user_id' => auth()->id() ?? 1,
                'module' => 'Accounting',
                'action' => 'Run Depreciation',
                'description' => "Calculated and posted depreciation for {$processedCount} assets."
            ]);
            session()->flash('success', "Depreciation runs successfully executed for {$processedCount} asset(s).");
        } else {
            session()->flash('info', 'No assets require depreciation run at this time.');
        }
    }

    public function render()
    {
        $coaQuery = Account::query()->orderBy('code');
        if ($this->search) {
            $coaQuery->where('name', 'like', '%' . $this->search . '%')
                     ->orWhere('code', 'like', '%' . $this->search . '%');
        }

        $journals = Journal::with('account')->orderBy('transaction_date', 'desc')->orderBy('reference_number', 'desc')->paginate(15);
        $assets = Asset::with('depreciations')->paginate(10);

        return view('livewire.accounting-manager', [
            'accounts' => $coaQuery->get(),
            'journals' => $journals,
            'assets' => $assets,
        ]);
    }
}
