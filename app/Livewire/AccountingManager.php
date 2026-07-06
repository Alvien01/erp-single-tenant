<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Account;
use App\Models\Journal;
use App\Models\Asset;
use App\Models\AssetDepreciation;
use App\Models\ActivityLog;
use App\Models\PeriodClosing;
use App\Models\BankAccount;
use App\Models\CashTransaction;
use App\Models\CashTransfer;
use App\Models\PaymentReceipt;
use App\Models\PaymentDisbursement;
use App\Models\PaymentSchedule;
use App\Models\TaxInvoice;
use App\Models\Budget;
use App\Models\BudgetLine;
use App\Models\Approval;
use Illuminate\Support\Facades\DB;

class AccountingManager extends Component
{
    use WithPagination;

    public $search = '';
    public $activeTab = 'dashboard'; // dashboard, coa, journals, ledger_detail, closing, assets
    
    // Journal Entry fields
    public $journal_date;
    public $description;
    public $debit_account_id;
    public $credit_account_id;
    public $amount = 0;
    public $journal_type = 'general'; // general, adjustment, closing
    public $isOpen = false;

    // Filter properties
    public $filter_journal_type = '';

    // Ledger Detail properties
    public $selected_account_id;
    public $ledger_start_date;
    public $ledger_end_date;

    // Closing Period properties
    public $closing_date;
    public $closing_note;

    // Asset fields
    public $isOpenAssetModal = false;
    public $asset_code;
    public $asset_name;
    public $asset_category = 'Equipment';
    public $asset_purchase_date;
    public $asset_purchase_price;
    public $asset_useful_life = 48; 
    public $asset_residual_value = 0;

    public function mount()
    {
        $this->journal_date = now()->format('Y-m-d');
        $this->ledger_start_date = now()->startOfMonth()->format('Y-m-d');
        $this->ledger_end_date = now()->endOfMonth()->format('Y-m-d');
        $this->closing_date = now()->endOfMonth()->format('Y-m-d');
    }

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
        $this->journal_type = 'general';
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
            'journal_type' => 'required|in:general,adjustment,closing',
        ]);
        
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
            'journal_type' => $this->journal_type,
        ]);

        Journal::create([
            'account_id' => $this->credit_account_id,
            'transaction_date' => $this->journal_date,
            'description' => $this->description,
            'reference_number' => $ref,
            'amount' => $this->amount,
            'type' => 'credit',
            'journal_type' => $this->journal_type,
        ]);

        // Adjust Account Balances
        $debitAcc = Account::find($this->debit_account_id);
        if (in_array($debitAcc->type, ['asset', 'expense'])) {
            $debitAcc->increment('balance', $this->amount);
        } else {
            $debitAcc->decrement('balance', $this->amount);
        }

        $creditAcc = Account::find($this->credit_account_id);
        if (in_array($creditAcc->type, ['liability', 'equity', 'income'])) {
            $creditAcc->increment('balance', $this->amount);
        } else {
            $creditAcc->decrement('balance', $this->amount);
        }

        ActivityLog::create([
            'user_id' => auth()->id() ?? 1,
            'module' => 'Accounting',
            'action' => 'Create Journal Entry',
            'description' => 'Journal entry (' . $this->journal_type . ') ' . $ref . ' posted: Debit ' . $debitAcc->name . ', Credit ' . $creditAcc->name . ' for Rp ' . number_format($this->amount, 0, ',', '.')
        ]);

        session()->flash('success', 'Journal entry posted successfully.');
        $this->closeModal();
    }

    // Closing Period Action
    public function executeClosing()
    {
        $this->validate([
            'closing_date' => 'required|date',
            'closing_note' => 'nullable|string',
        ]);

        // Find or create retained earnings (Laba Ditahan) account
        $retainedEarningsAccount = Account::firstOrCreate(
            ['code' => '3200'],
            ['name' => 'Laba Ditahan', 'type' => 'equity', 'balance' => 0]
        );

        // Find or create Income Summary (Ikhtisar Laba Rugi) account
        $incomeSummaryAccount = Account::firstOrCreate(
            ['code' => '3999'],
            ['name' => 'Ikhtisar Laba Rugi', 'type' => 'equity', 'balance' => 0]
        );

        // Fetch nominal accounts (income & expense) with positive balances
        $nominalAccounts = Account::whereIn('type', ['income', 'expense'])->where('balance', '!=', 0)->get();

        if ($nominalAccounts->isEmpty()) {
            session()->flash('error', 'No active income or expense accounts to close.');
            return;
        }

        DB::transaction(function () use ($nominalAccounts, $incomeSummaryAccount, $retainedEarningsAccount) {
            $ref = 'CLS-' . date('Ymd', strtotime($this->closing_date)) . '-' . strtoupper(substr(uniqid(), -4));

            foreach ($nominalAccounts as $account) {
                $balance = $account->balance;
                if ($balance == 0) continue;

                if ($account->type === 'income') {
                    // Debit Income Account (to make it 0), Credit Income Summary
                    Journal::create([
                        'account_id' => $account->id,
                        'transaction_date' => $this->closing_date,
                        'description' => 'Closing entry for ' . $account->name,
                        'reference_number' => $ref,
                        'amount' => $balance,
                        'type' => 'debit',
                        'journal_type' => 'closing',
                    ]);

                    Journal::create([
                        'account_id' => $incomeSummaryAccount->id,
                        'transaction_date' => $this->closing_date,
                        'description' => 'Closing entry for ' . $account->name,
                        'reference_number' => $ref,
                        'amount' => $balance,
                        'type' => 'credit',
                        'journal_type' => 'closing',
                    ]);

                    $account->balance = 0;
                    $account->save();

                    $incomeSummaryAccount->increment('balance', $balance);
                } elseif ($account->type === 'expense') {
                    // Credit Expense Account (to make it 0), Debit Income Summary
                    Journal::create([
                        'account_id' => $account->id,
                        'transaction_date' => $this->closing_date,
                        'description' => 'Closing entry for ' . $account->name,
                        'reference_number' => $ref,
                        'amount' => $balance,
                        'type' => 'credit',
                        'journal_type' => 'closing',
                    ]);

                    Journal::create([
                        'account_id' => $incomeSummaryAccount->id,
                        'transaction_date' => $this->closing_date,
                        'description' => 'Closing entry for ' . $account->name,
                        'reference_number' => $ref,
                        'amount' => $balance,
                        'type' => 'debit',
                        'journal_type' => 'closing',
                    ]);

                    $account->balance = 0;
                    $account->save();

                    $incomeSummaryAccount->decrement('balance', $balance);
                }
            }

            // Transfer net income/loss from Income Summary to Retained Earnings
            $netIncome = $incomeSummaryAccount->balance;
            if ($netIncome != 0) {
                if ($netIncome > 0) {
                    // Debit Income Summary (to make it 0), Credit Retained Earnings
                    Journal::create([
                        'account_id' => $incomeSummaryAccount->id,
                        'transaction_date' => $this->closing_date,
                        'description' => 'Transfer Net Income to Retained Earnings',
                        'reference_number' => $ref,
                        'amount' => $netIncome,
                        'type' => 'debit',
                        'journal_type' => 'closing',
                    ]);

                    Journal::create([
                        'account_id' => $retainedEarningsAccount->id,
                        'transaction_date' => $this->closing_date,
                        'description' => 'Transfer Net Income to Retained Earnings',
                        'reference_number' => $ref,
                        'amount' => $netIncome,
                        'type' => 'credit',
                        'journal_type' => 'closing',
                    ]);

                    $retainedEarningsAccount->increment('balance', $netIncome);
                } else {
                    // Credit Income Summary (to make it 0), Debit Retained Earnings
                    $lossAmount = abs($netIncome);
                    Journal::create([
                        'account_id' => $incomeSummaryAccount->id,
                        'transaction_date' => $this->closing_date,
                        'description' => 'Transfer Net Loss to Retained Earnings',
                        'reference_number' => $ref,
                        'amount' => $lossAmount,
                        'type' => 'credit',
                        'journal_type' => 'closing',
                    ]);

                    Journal::create([
                        'account_id' => $retainedEarningsAccount->id,
                        'transaction_date' => $this->closing_date,
                        'description' => 'Transfer Net Loss to Retained Earnings',
                        'reference_number' => $ref,
                        'amount' => $lossAmount,
                        'type' => 'debit',
                        'journal_type' => 'closing',
                    ]);

                    $retainedEarningsAccount->decrement('balance', $lossAmount);
                }

                $incomeSummaryAccount->balance = 0;
                $incomeSummaryAccount->save();
            }

            // Save period closing log
            PeriodClosing::create([
                'closing_date' => $this->closing_date,
                'status' => 'closed',
                'closed_by' => auth()->id() ?? 1,
                'notes' => $this->closing_note ?: 'Period closing successfully compiled.',
            ]);

            ActivityLog::create([
                'user_id' => auth()->id() ?? 1,
                'module' => 'Accounting',
                'action' => 'Close Fiscal Period',
                'description' => 'Closed fiscal period up to ' . $this->closing_date
            ]);
        });

        session()->flash('success', 'Fiscal period closed successfully. All income and expense accounts have been reset to zero.');
        $this->activeTab = 'coa';
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
                continue; 
            }

            $monthlyAmount = ($asset->purchase_price - $asset->residual_value) / $asset->useful_life_months;

            $purchaseTime = strtotime($asset->purchase_date);
            $nextMonthTime = strtotime("+" . ($existingMonths + 1) . " months", $purchaseTime);
            $depreciationDate = date('Y-m-d', $nextMonthTime);

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

            $ref = 'DEP-' . now()->format('Ymd') . '-' . sprintf('%04d', $asset->id);

            Journal::create([
                'account_id' => $depExpenseAcc->id,
                'transaction_date' => $depreciationDate,
                'description' => "Depreciation of {$asset->asset_name} - Month " . ($existingMonths + 1),
                'reference_number' => $ref,
                'amount' => $monthlyAmount,
                'type' => 'debit',
                'journal_type' => 'adjustment',
            ]);

            Journal::create([
                'account_id' => $accumDepAcc->id,
                'transaction_date' => $depreciationDate,
                'description' => "Accum. Depreciation of {$asset->asset_name} - Month " . ($existingMonths + 1),
                'reference_number' => $ref,
                'amount' => $monthlyAmount,
                'type' => 'credit',
                'journal_type' => 'adjustment',
            ]);

            $depExpenseAcc->increment('balance', $monthlyAmount);
            $accumDepAcc->decrement('balance', $monthlyAmount); 

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

    public function getDashboardStats()
    {
        $accounts = Account::all();
        $totalAssets = $accounts->where('type', 'asset')->sum('balance');
        $totalLiabilities = $accounts->where('type', 'liability')->sum('balance');
        $totalEquity = $accounts->where('type', 'equity')->sum('balance');
        $totalIncome = $accounts->where('type', 'income')->sum('balance');
        $totalExpenses = $accounts->where('type', 'expense')->sum('balance');
        $netProfit = $totalIncome - $totalExpenses;

        $cashBankBalance = $accounts->where('type', 'asset')->filter(function($acc) {
            $nameLower = strtolower($acc->name);
            return str_contains($nameLower, 'kas') || str_contains($nameLower, 'bank') || str_contains($nameLower, 'cash');
        })->sum('balance');

        $receivablesBalance = $accounts->where('type', 'asset')->filter(function($acc) {
            $nameLower = strtolower($acc->name);
            return str_contains($nameLower, 'piutang') || str_contains($nameLower, 'receivable') || str_contains($nameLower, 'ar');
        })->sum('balance');

        $payablesBalance = $accounts->where('type', 'liability')->filter(function($acc) {
            $nameLower = strtolower($acc->name);
            return str_contains($nameLower, 'hutang') || str_contains($nameLower, 'payable') || str_contains($nameLower, 'ap');
        })->sum('balance');

        // Sub-module stats
        $cashTransactionsCount = \App\Models\CashTransaction::count();
        $cashTransactionsTotal = \App\Models\CashTransaction::sum('amount');
        $cashTransfersCount = \App\Models\CashTransfer::count();
        $bankAccountsCount = \App\Models\BankAccount::count();

        $receiptsCount = \App\Models\PaymentReceipt::count();
        $receiptsTotal = \App\Models\PaymentReceipt::sum('amount');

        $disbursementsCount = \App\Models\PaymentDisbursement::count();
        $disbursementsTotal = \App\Models\PaymentDisbursement::sum('amount');
        $pendingSchedulesCount = \App\Models\PaymentSchedule::where('status', 'pending')->count();
        $pendingSchedulesTotal = \App\Models\PaymentSchedule::where('status', 'pending')->sum('planned_amount');

        $taxInvoicesCount = \App\Models\TaxInvoice::count();
        $taxPPNMasukan = \App\Models\TaxInvoice::where('type', 'masukan')->sum('ppn');
        $taxPPNKeluaran = \App\Models\TaxInvoice::where('type', 'keluaran')->sum('ppn');

        $budgetsCount = \App\Models\Budget::count();
        $totalPlannedBudget = \App\Models\BudgetLine::sum('planned_amount');

        $pendingApprovalsCount = \App\Models\Approval::where('status', 'pending')->count();

        $assetsCount = \App\Models\Asset::count();
        $totalAssetCost = \App\Models\Asset::sum('purchase_price');
        $totalDepreciation = \App\Models\AssetDepreciation::sum('amount');

        $recentTransactions = Journal::with('account')
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->take(6)
            ->get();

        return [
            'totalAssets' => $totalAssets,
            'totalLiabilities' => $totalLiabilities,
            'totalEquity' => $totalEquity,
            'totalIncome' => $totalIncome,
            'totalExpenses' => $totalExpenses,
            'netProfit' => $netProfit,
            'cashBankBalance' => $cashBankBalance,
            'receivablesBalance' => $receivablesBalance,
            'payablesBalance' => $payablesBalance,
            'cashTransactionsCount' => $cashTransactionsCount,
            'cashTransactionsTotal' => $cashTransactionsTotal,
            'cashTransfersCount' => $cashTransfersCount,
            'bankAccountsCount' => $bankAccountsCount,
            'receiptsCount' => $receiptsCount,
            'receiptsTotal' => $receiptsTotal,
            'disbursementsCount' => $disbursementsCount,
            'disbursementsTotal' => $disbursementsTotal,
            'pendingSchedulesCount' => $pendingSchedulesCount,
            'pendingSchedulesTotal' => $pendingSchedulesTotal,
            'taxInvoicesCount' => $taxInvoicesCount,
            'taxPPNMasukan' => $taxPPNMasukan,
            'taxPPNKeluaran' => $taxPPNKeluaran,
            'budgetsCount' => $budgetsCount,
            'totalPlannedBudget' => $totalPlannedBudget,
            'pendingApprovalsCount' => $pendingApprovalsCount,
            'assetsCount' => $assetsCount,
            'totalAssetCost' => $totalAssetCost,
            'totalDepreciation' => $totalDepreciation,
            'recentTransactions' => $recentTransactions,
        ];
    }

    public function render()
    {
        $coaQuery = Account::query()->orderBy('code');
        if ($this->search) {
            $coaQuery->where('name', 'like', '%' . $this->search . '%')
                     ->orWhere('code', 'like', '%' . $this->search . '%');
        }

        // Journals filter
        $journalsQuery = Journal::with('account');
        if ($this->filter_journal_type) {
            $journalsQuery->where('journal_type', $this->filter_journal_type);
        }
        $journals = $journalsQuery->orderBy('transaction_date', 'desc')->orderBy('reference_number', 'desc')->paginate(15);

        // General Ledger Detail
        $ledgerEntries = [];
        $openingBalance = 0;
        $closingBalance = 0;
        if ($this->selected_account_id) {
            $account = Account::find($this->selected_account_id);
            if ($account) {
                // Calculate opening balance before start date
                $debitsBefore = Journal::where('account_id', $this->selected_account_id)
                    ->where('type', 'debit')
                    ->where('transaction_date', '<', $this->ledger_start_date)
                    ->sum('amount');
                $creditsBefore = Journal::where('account_id', $this->selected_account_id)
                    ->where('type', 'credit')
                    ->where('transaction_date', '<', $this->ledger_start_date)
                    ->sum('amount');

                if (in_array($account->type, ['asset', 'expense'])) {
                    $openingBalance = $debitsBefore - $creditsBefore;
                } else {
                    $openingBalance = $creditsBefore - $debitsBefore;
                }

                // Fetch ledger entries in date range
                $ledgerEntries = Journal::where('account_id', $this->selected_account_id)
                    ->whereBetween('transaction_date', [$this->ledger_start_date, $this->ledger_end_date])
                    ->orderBy('transaction_date')
                    ->orderBy('id')
                    ->get();

                // Compute running balances
                $currentTemp = $openingBalance;
                foreach ($ledgerEntries as $entry) {
                    if ($entry->type === 'debit') {
                        if (in_array($account->type, ['asset', 'expense'])) {
                            $currentTemp += $entry->amount;
                        } else {
                            $currentTemp -= $entry->amount;
                        }
                    } else { // credit
                        if (in_array($account->type, ['asset', 'expense'])) {
                            $currentTemp -= $entry->amount;
                        } else {
                            $currentTemp += $entry->amount;
                        }
                    }
                    $entry->running_balance = $currentTemp;
                }
                $closingBalance = $currentTemp;
            }
        }

        // Period Closings history
        $closings = PeriodClosing::orderBy('closing_date', 'desc')->paginate(10);

        return view('livewire.accounting-manager', [
            'accounts' => $coaQuery->get(),
            'journals' => $journals,
            'assets' => Asset::with('depreciations')->paginate(10),
            'allAccounts' => Account::orderBy('code')->get(),
            'ledgerEntries' => $ledgerEntries,
            'openingBalance' => $openingBalance,
            'closingBalance' => $closingBalance,
            'closings' => $closings,
            'stats' => $this->getDashboardStats(),
        ])->layout('layouts.app');
    }
}

