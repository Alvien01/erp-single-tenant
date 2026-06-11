<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\BankAccount;
use App\Models\CashTransaction;
use App\Models\CashTransfer;
use App\Models\Account;
use App\Models\Journal;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class CashBankManager extends Component
{
    use WithPagination;

    public $search = '';
    public $activeTab = 'accounts'; // accounts, transactions, transfers

    public $isOpen = false;
    public $modalType = ''; // bank_account, transaction, transfer

    // Bank Account fields
    public $bank_account_id;
    public $bank_code;
    public $bank_name;
    public $account_name;
    public $account_number;
    public $initial_balance = 0;

    // Transaction fields
    public $transaction_id;
    public $transaction_date;
    public $transaction_amount = 0;
    public $transaction_type = 'in'; // in, out
    public $transaction_reference;
    public $transaction_description;
    public $transaction_account_id; // COA account
    public $transaction_bank_account_id; // Bank account

    // Transfer fields
    public $transfer_date;
    public $transfer_from_account_id;
    public $transfer_to_account_id;
    public $transfer_amount = 0;
    public $transfer_reference;
    public $transfer_description;

    public function mount()
    {
        $this->transaction_date = now()->format('Y-m-d');
        $this->transfer_date = now()->format('Y-m-d');
    }

    public function openModal($type)
    {
        $this->modalType = $type;
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->modalType = '';
        $this->resetFields();
    }

    public function resetFields()
    {
        $this->bank_account_id = null;
        $this->bank_code = '';
        $this->bank_name = '';
        $this->account_name = '';
        $this->account_number = '';
        $this->initial_balance = 0;

        $this->transaction_id = null;
        $this->transaction_date = now()->format('Y-m-d');
        $this->transaction_amount = 0;
        $this->transaction_type = 'in';
        $this->transaction_reference = '';
        $this->transaction_description = '';
        $this->transaction_account_id = null;
        $this->transaction_bank_account_id = null;

        $this->transfer_date = now()->format('Y-m-d');
        $this->transfer_from_account_id = null;
        $this->transfer_to_account_id = null;
        $this->transfer_amount = 0;
        $this->transfer_reference = '';
        $this->transfer_description = '';
    }

    public function createBankAccount()
    {
        $this->resetFields();
        $this->openModal('bank_account');
    }

    public function saveBankAccount()
    {
        $this->validate([
            'bank_code' => 'required|string|unique:bank_accounts,code,' . $this->bank_account_id,
            'account_name' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:100',
            'initial_balance' => 'required|numeric|min:0',
        ]);

        $bank = BankAccount::updateOrCreate(
            ['id' => $this->bank_account_id],
            [
                'code' => $this->bank_code,
                'name' => $this->account_name,
                'bank_name' => $this->bank_name,
                'account_number' => $this->account_number,
                'balance' => $this->initial_balance,
            ]
        );

        // Auto create corresponding COA asset account if it doesn't exist
        $coaCode = '11' . sprintf('%02d', $bank->id);
        Account::firstOrCreate(
            ['code' => $coaCode],
            [
                'name' => $this->bank_name . ' - ' . $this->account_name,
                'type' => 'asset',
                'balance' => $this->initial_balance
            ]
        );

        ActivityLog::create([
            'user_id' => Auth::id() ?? 1,
            'module' => 'Cash & Bank',
            'action' => $this->bank_account_id ? 'Update Bank Account' : 'Create Bank Account',
            'description' => 'Saved bank account: ' . $this->account_name . ' (' . $this->bank_name . ')'
        ]);

        session()->flash('success', 'Bank account saved successfully.');
        $this->closeModal();
    }

    public function createTransaction()
    {
        $this->resetFields();
        $this->openModal('transaction');
    }

    public function saveTransaction()
    {
        $this->validate([
            'transaction_date' => 'required|date',
            'transaction_amount' => 'required|numeric|min:1',
            'transaction_type' => 'required|in:in,out',
            'transaction_description' => 'required|string|max:255',
            'transaction_account_id' => 'required|exists:accounts,id',
            'transaction_bank_account_id' => 'required|exists:bank_accounts,id',
        ]);

        // Find standard Cash/Bank account matching bank account ID
        $bank = BankAccount::find($this->transaction_bank_account_id);
        $coaCode = '11' . sprintf('%02d', $bank->id);
        $bankCoa = Account::where('code', $coaCode)->first();

        if (!$bankCoa) {
            $bankCoa = Account::create([
                'code' => $coaCode,
                'name' => $bank->bank_name . ' - ' . $bank->name,
                'type' => 'asset',
                'balance' => $bank->balance
            ]);
        }

        // Post Journal Entry
        $ref = $this->transaction_reference ?: 'TX-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

        if ($this->transaction_type === 'in') {
            // Money incoming: Debit Cash/Bank COA, Credit Target COA (e.g. Income/Revenue)
            Journal::create([
                'account_id' => $bankCoa->id,
                'transaction_date' => $this->transaction_date,
                'description' => $this->transaction_description,
                'reference_number' => $ref,
                'amount' => $this->transaction_amount,
                'type' => 'debit',
            ]);

            Journal::create([
                'account_id' => $this->transaction_account_id,
                'transaction_date' => $this->transaction_date,
                'description' => $this->transaction_description,
                'reference_number' => $ref,
                'amount' => $this->transaction_amount,
                'type' => 'credit',
            ]);

            $bank->increment('balance', $this->transaction_amount);
            $bankCoa->increment('balance', $this->transaction_amount);

            $targetCoa = Account::find($this->transaction_account_id);
            if ($targetCoa->type === 'liability' || $targetCoa->type === 'equity' || $targetCoa->type === 'income') {
                $targetCoa->increment('balance', $this->transaction_amount);
            } else {
                $targetCoa->decrement('balance', $this->transaction_amount);
            }
        } else {
            // Money outgoing: Debit Target COA (e.g. Expense), Credit Cash/Bank COA
            Journal::create([
                'account_id' => $this->transaction_account_id,
                'transaction_date' => $this->transaction_date,
                'description' => $this->transaction_description,
                'reference_number' => $ref,
                'amount' => $this->transaction_amount,
                'type' => 'debit',
            ]);

            Journal::create([
                'account_id' => $bankCoa->id,
                'transaction_date' => $this->transaction_date,
                'description' => $this->transaction_description,
                'reference_number' => $ref,
                'amount' => $this->transaction_amount,
                'type' => 'credit',
            ]);

            $bank->decrement('balance', $this->transaction_amount);
            $bankCoa->decrement('balance', $this->transaction_amount);

            $targetCoa = Account::find($this->transaction_account_id);
            if ($targetCoa->type === 'asset' || $targetCoa->type === 'expense') {
                $targetCoa->increment('balance', $this->transaction_amount);
            } else {
                $targetCoa->decrement('balance', $this->transaction_amount);
            }
        }

        CashTransaction::create([
            'date' => $this->transaction_date,
            'amount' => $this->transaction_amount,
            'type' => $this->transaction_type,
            'reference' => $ref,
            'description' => $this->transaction_description,
            'account_id' => $this->transaction_account_id,
            'bank_account_id' => $this->transaction_bank_account_id,
        ]);

        ActivityLog::create([
            'user_id' => Auth::id() ?? 1,
            'module' => 'Cash & Bank',
            'action' => 'Create Cash Transaction',
            'description' => 'Logged ' . $this->transaction_type . ' transaction: ' . $this->transaction_description . ' of Rp ' . number_format($this->transaction_amount, 2)
        ]);

        session()->flash('success', 'Transaction logged and journal entry posted successfully.');
        $this->closeModal();
    }

    public function createTransfer()
    {
        $this->resetFields();
        $this->openModal('transfer');
    }

    public function saveTransfer()
    {
        $this->validate([
            'transfer_date' => 'required|date',
            'transfer_amount' => 'required|numeric|min:1',
            'transfer_from_account_id' => 'required|exists:bank_accounts,id|different:transfer_to_account_id',
            'transfer_to_account_id' => 'required|exists:bank_accounts,id',
            'transfer_description' => 'required|string|max:255',
        ]);

        $fromBank = BankAccount::find($this->transfer_from_account_id);
        $toBank = BankAccount::find($this->transfer_to_account_id);

        if ($fromBank->balance < $this->transfer_amount) {
            session()->flash('error', 'Insufficient balance in source bank account.');
            return;
        }

        $fromCoaCode = '11' . sprintf('%02d', $fromBank->id);
        $toCoaCode = '11' . sprintf('%02d', $toBank->id);

        $fromCoa = Account::where('code', $fromCoaCode)->first();
        $toCoa = Account::where('code', $toCoaCode)->first();

        // Create Journal Entry: Debit target, Credit source
        $ref = $this->transfer_reference ?: 'TF-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

        Journal::create([
            'account_id' => $toCoa->id,
            'transaction_date' => $this->transfer_date,
            'description' => $this->transfer_description,
            'reference_number' => $ref,
            'amount' => $this->transfer_amount,
            'type' => 'debit',
        ]);

        Journal::create([
            'account_id' => $fromCoa->id,
            'transaction_date' => $this->transfer_date,
            'description' => $this->transfer_description,
            'reference_number' => $ref,
            'amount' => $this->transfer_amount,
            'type' => 'credit',
        ]);

        $fromBank->decrement('balance', $this->transfer_amount);
        $fromCoa->decrement('balance', $this->transfer_amount);

        $toBank->increment('balance', $this->transfer_amount);
        $toCoa->increment('balance', $this->transfer_amount);

        CashTransfer::create([
            'date' => $this->transfer_date,
            'from_account_id' => $fromCoa->id,
            'to_account_id' => $toCoa->id,
            'amount' => $this->transfer_amount,
            'reference' => $ref,
            'description' => $this->transfer_description,
        ]);

        ActivityLog::create([
            'user_id' => Auth::id() ?? 1,
            'module' => 'Cash & Bank',
            'action' => 'Create Fund Transfer',
            'description' => 'Transferred Rp ' . number_format($this->transfer_amount, 2) . ' from ' . $fromBank->name . ' to ' . $toBank->name
        ]);

        session()->flash('success', 'Funds transferred and journal entries posted successfully.');
        $this->closeModal();
    }

    public function render()
    {
        $s = '%' . $this->search . '%';

        return view('livewire.cash-bank-manager', [
            'bankAccounts' => BankAccount::where('name', 'like', $s)->orWhere('bank_name', 'like', $s)->get(),
            'transactions' => CashTransaction::with(['account', 'bankAccount'])->where('description', 'like', $s)->orderBy('date', 'desc')->paginate(10),
            'transfers' => CashTransfer::with(['fromAccount', 'toAccount'])->where('description', 'like', $s)->orderBy('date', 'desc')->paginate(10),
            'accounts' => Account::orderBy('code')->get(),
        ])->layout('layouts.app');
    }
}
