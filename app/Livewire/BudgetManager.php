<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Budget;
use App\Models\BudgetLine;
use App\Models\Department;
use App\Models\Account;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class BudgetManager extends Component
{
    use WithPagination;

    public $search = '';
    public $isOpen = false;
    public $modalType = ''; // 'budget', 'budget_line'

    // Budget Form Fields
    public $budget_id;
    public $name;
    public $department_id;
    public $start_date;
    public $end_date;
    public $status = 'draft';

    // Budget Line Form Fields
    public $selected_budget_id;
    public $account_id;
    public $planned_amount = 0;

    public function mount()
    {
        $this->start_date = now()->startOfYear()->format('Y-m-d');
        $this->end_date = now()->endOfYear()->format('Y-m-d');
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
        $this->resetBudgetFields();
        $this->resetBudgetLineFields();
    }

    public function resetBudgetFields()
    {
        $this->budget_id = null;
        $this->name = '';
        $this->department_id = null;
        $this->start_date = now()->startOfYear()->format('Y-m-d');
        $this->end_date = now()->endOfYear()->format('Y-m-d');
        $this->status = 'draft';
    }

    public function resetBudgetLineFields()
    {
        $this->account_id = null;
        $this->planned_amount = 0;
    }

    public function createBudget()
    {
        $this->resetBudgetFields();
        $this->openModal('budget');
    }

    public function editBudget($id)
    {
        $b = Budget::query()->findOrFail($id);
        $this->budget_id = $b->id;
        $this->name = $b->name;
        $this->department_id = $b->department_id;
        $this->start_date = $b->start_date->format('Y-m-d');
        $this->end_date = $b->end_date->format('Y-m-d');
        $this->status = $b->status;

        $this->openModal('budget');
    }

    public function saveBudget()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        Budget::query()->updateOrCreate(
            ['id' => $this->budget_id],
            [
                'name' => $this->name,
                'department_id' => $this->department_id ?: null,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'status' => $this->status ?: 'draft',
            ]
        );

        ActivityLog::query()->create([
            'user_id' => Auth::id() ?? 1,
            'module' => 'Budgets',
            'action' => $this->budget_id ? 'Update Budget Plan' : 'Create Budget Plan',
            'description' => 'Saved budget plan: ' . $this->name
        ]);

        session()->flash('success', 'Budget plan saved successfully.');
        $this->closeModal();
    }

    public function addBudgetLine($budgetId)
    {
        $this->selected_budget_id = $budgetId;
        $this->resetBudgetLineFields();
        $this->openModal('budget_line');
    }

    public function saveBudgetLine()
    {
        $this->validate([
            'selected_budget_id' => 'required|exists:budgets,id',
            'account_id' => 'required|exists:accounts,id',
            'planned_amount' => 'required|numeric|min:1',
        ]);

        BudgetLine::query()->updateOrCreate(
            [
                'budget_id' => $this->selected_budget_id,
                'account_id' => $this->account_id
            ],
            [
                'planned_amount' => $this->planned_amount,
                'actual_amount' => rand(0, (int)$this->planned_amount * 0.8) // simulate actual spending
            ]
        );

        session()->flash('success', 'Budget position added successfully.');
        $this->closeModal();
    }

    public function deleteBudgetLine($id)
    {
        BudgetLine::query()->findOrFail($id)->delete();
        session()->flash('success', 'Budget position removed.');
    }

    public function approveBudget($id)
    {
        $b = Budget::query()->findOrFail($id);
        $b->status = 'approved';
        $b->save();

        session()->flash('success', 'Budget plan approved.');
    }

    public function deleteBudget($id)
    {
        Budget::query()->findOrFail($id)->delete();
        session()->flash('success', 'Budget plan deleted.');
    }

    public function render()
    {
        $query = Budget::with(['department', 'lines.account']);

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        return view('livewire.budget-manager', [
            'budgets' => $query->orderBy('start_date', 'desc')->paginate(10),
            'departments' => Department::all(),
            'accounts' => Account::all()
        ]);
    }
}
