<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\BankStatement;
use App\Models\ActivityLog;
use App\Models\Account;
use Illuminate\Support\Facades\Auth;

class BankReconciliationManager extends Component
{
    use WithPagination;

    public $search = '';
    public $isOpen = false;
    public $modalType = ''; // 'statement'

    // Form fields
    public $statement_id;
    public $date;
    public $description;
    public $amount = 0;
    public $reference;
    public $is_reconciled = false;

    // Reconciliation target details
    public $selectedStatement;
    public $match_account_id;

    public function mount()
    {
        $this->date = now()->format('Y-m-d');
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
        $this->resetStatementFields();
    }

    public function resetStatementFields()
    {
        $this->statement_id = null;
        $this->date = now()->format('Y-m-d');
        $this->description = '';
        $this->amount = 0;
        $this->reference = '';
        $this->is_reconciled = false;
    }

    public function createStatement()
    {
        $this->resetStatementFields();
        $this->openModal('statement');
    }

    public function saveStatement()
    {
        $this->validate([
            'date' => 'required|date',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'reference' => 'nullable|string|max:100',
        ]);

        BankStatement::query()->updateOrCreate(
            ['id' => $this->statement_id],
            [
                'date' => $this->date,
                'description' => $this->description,
                'amount' => $this->amount,
                'reference' => $this->reference,
                'is_reconciled' => $this->is_reconciled,
            ]
        );

        ActivityLog::query()->create([
            'user_id' => Auth::id() ?? 1,
            'module' => 'Bank Reconciliation',
            'action' => $this->statement_id ? 'Update Statement Line' : 'Create Statement Line',
            'description' => 'Saved statement line: ' . $this->description . ' of amount ' . $this->amount
        ]);

        session()->flash('success', 'Bank statement line saved successfully.');
        $this->closeModal();
    }

    public function startReconciliation($id)
    {
        $this->selectedStatement = BankStatement::query()->findOrFail($id);
        $this->match_account_id = Account::query()->where('type', 'income')->first()?->id;
        $this->openModal('reconcile');
    }

    public function processReconciliation()
    {
        $this->validate([
            'match_account_id' => 'required|exists:accounts,id',
        ]);

        if ($this->selectedStatement) {
            $this->selectedStatement->is_reconciled = true;
            $this->selectedStatement->save();

            // Simulate Journal Entry or posting
            ActivityLog::query()->create([
                'user_id' => Auth::id() ?? 1,
                'module' => 'Bank Reconciliation',
                'action' => 'Reconcile Mutation',
                'description' => 'Reconciled Bank Mutation ID ' . $this->selectedStatement->id . ' against Account ID ' . $this->match_account_id
            ]);

            session()->flash('success', 'Mutation reconciled successfully.');
            $this->closeModal();
            $this->selectedStatement = null;
        }
    }

    public function deleteStatement($id)
    {
        BankStatement::query()->findOrFail($id)->delete();
        session()->flash('success', 'Statement line deleted.');
    }

    public function render()
    {
        $query = BankStatement::query();

        if ($this->search) {
            $query->where('description', 'like', '%' . $this->search . '%')
                  ->orWhere('reference', 'like', '%' . $this->search . '%');
        }

        return view('livewire.bank-reconciliation-manager', [
            'statements' => $query->orderBy('date', 'desc')->paginate(10),
            'accounts' => Account::all()
        ]);
    }
}
