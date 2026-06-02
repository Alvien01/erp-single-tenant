<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Expense;
use App\Models\Employee;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class ExpenseManager extends Component
{
    use WithPagination;

    public $search = '';
    public $isOpen = false;
    public $modalType = ''; // 'expense'

    // Expense Form Fields
    public $expense_id;
    public $employee_id;
    public $date;
    public $category = 'travel';
    public $amount = 0;
    public $description;
    public $status = 'draft';

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
        $this->resetExpenseFields();
    }

    public function resetExpenseFields()
    {
        $this->expense_id = null;
        $this->employee_id = null;
        $this->date = now()->format('Y-m-d');
        $this->category = 'travel';
        $this->amount = 0;
        $this->description = '';
        $this->status = 'draft';
    }

    public function createExpense()
    {
        $this->resetExpenseFields();
        $this->openModal('expense');
    }

    public function editExpense($id)
    {
        $exp = Expense::query()->findOrFail($id);
        $this->expense_id = $exp->id;
        $this->employee_id = $exp->employee_id;
        $this->date = $exp->date->format('Y-m-d');
        $this->category = $exp->category;
        $this->amount = $exp->amount;
        $this->description = $exp->description;
        $this->status = $exp->status;

        $this->openModal('expense');
    }

    public function saveExpense()
    {
        $this->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'category' => 'required|string',
            'amount' => 'required|numeric|min:1',
            'description' => 'nullable|string|max:500',
        ]);

        Expense::query()->updateOrCreate(
            ['id' => $this->expense_id],
            [
                'employee_id' => $this->employee_id,
                'date' => $this->date,
                'category' => $this->category,
                'amount' => $this->amount,
                'description' => $this->description,
                'status' => $this->status ?: 'draft',
            ]
        );

        ActivityLog::query()->create([
            'user_id' => Auth::id() ?? 1,
            'module' => 'Expenses',
            'action' => $this->expense_id ? 'Update Expense' : 'Create Expense',
            'description' => 'Saved expense claim for employee ID ' . $this->employee_id . ' with amount ' . $this->amount
        ]);

        session()->flash('success', 'Expense claim saved successfully.');
        $this->closeModal();
    }

    public function deleteExpense($id)
    {
        $exp = Expense::query()->findOrFail($id);
        $exp->delete();

        ActivityLog::query()->create([
            'user_id' => Auth::id() ?? 1,
            'module' => 'Expenses',
            'action' => 'Delete Expense',
            'description' => 'Deleted expense claim ID ' . $id
        ]);

        session()->flash('success', 'Expense claim deleted successfully.');
    }

    public function submitExpense($id)
    {
        $exp = Expense::query()->findOrFail($id);
        $exp->status = 'submitted';
        $exp->save();

        session()->flash('success', 'Expense claim submitted for approval.');
    }

    public function approveExpense($id)
    {
        $exp = Expense::query()->findOrFail($id);
        $exp->status = 'approved';
        $exp->approved_by = Auth::id() ?? 1;
        $exp->approved_at = now();
        $exp->save();

        session()->flash('success', 'Expense claim approved.');
    }

    public function rejectExpense($id)
    {
        $exp = Expense::query()->findOrFail($id);
        $exp->status = 'rejected';
        $exp->save();

        session()->flash('success', 'Expense claim rejected.');
    }

    public function payExpense($id)
    {
        $exp = Expense::query()->findOrFail($id);
        $exp->status = 'paid';
        $exp->paid_at = now();
        $exp->save();

        // Log payment in audit
        ActivityLog::query()->create([
            'user_id' => Auth::id() ?? 1,
            'module' => 'Expenses',
            'action' => 'Pay Expense Reimbursement',
            'description' => 'Paid reimbursement for expense ID ' . $exp->id . ' to ' . $exp->employee->name
        ]);

        session()->flash('success', 'Expense reimbursement paid successfully.');
    }

    public function render()
    {
        $query = Expense::with(['employee', 'approver']);

        if ($this->search) {
            $query->whereHas('employee', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            })->orWhere('category', 'like', '%' . $this->search . '%');
        }

        return view('livewire.expense-manager', [
            'expenses' => $query->orderBy('date', 'desc')->paginate(10),
            'employees' => Employee::all()
        ]);
    }
}
