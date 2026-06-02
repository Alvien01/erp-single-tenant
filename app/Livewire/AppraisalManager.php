<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Appraisal;
use App\Models\Employee;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class AppraisalManager extends Component
{
    use WithPagination;

    public $search = '';

    // Form fields
    public $appraisal_id;
    public $employee_id;
    public $appraisal_date;
    public $manager_id;
    public $period = '2026 Annual';
    public $score = 3;
    public $notes;
    public $status = 'draft';

    public $isOpen = false;
    public $isEdit = false;

    protected $queryString = ['search'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openModal($id = null)
    {
        $this->isOpen = true;
        $this->isEdit = $id ? true : false;

        if ($id) {
            $app = Appraisal::findOrFail($id);
            $this->appraisal_id = $app->id;
            $this->employee_id = $app->employee_id;
            $this->appraisal_date = $app->appraisal_date ? $app->appraisal_date->format('Y-m-d') : null;
            $this->manager_id = $app->manager_id;
            $this->period = $app->period;
            $this->score = $app->score;
            $this->notes = $app->notes;
            $this->status = $app->status;
        } else {
            $this->resetFields();
        }
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->isEdit = false;
        $this->resetFields();
    }

    private function resetFields()
    {
        $this->appraisal_id = null;
        $this->employee_id = null;
        $this->appraisal_date = date('Y-m-d');
        $this->manager_id = Auth::id();
        $this->period = '2026 Annual';
        $this->score = 3;
        $this->notes = '';
        $this->status = 'draft';
    }

    public function save()
    {
        $this->validate([
            'employee_id' => 'required|exists:employees,id',
            'appraisal_date' => 'required|date',
            'manager_id' => 'required|exists:users,id',
            'period' => 'required|string|max:255',
            'score' => 'required|integer|min:1|max:5',
            'notes' => 'nullable|string',
            'status' => 'required|in:draft,confirmed',
        ]);

        $data = [
            'employee_id' => $this->employee_id,
            'appraisal_date' => $this->appraisal_date,
            'manager_id' => $this->manager_id,
            'period' => $this->period,
            'score' => $this->score,
            'notes' => $this->notes,
            'status' => $this->status,
        ];

        if ($this->isEdit) {
            $app = Appraisal::findOrFail($this->appraisal_id);
            if ($app->status === 'confirmed' && !Auth::user()->hasRole('admin')) {
                session()->flash('error', 'Only Admins can edit a confirmed performance appraisal!');
                return;
            }
            $app->update($data);
            $action = 'Updated performance evaluation for Employee ID: ' . $this->employee_id;
        } else {
            Appraisal::create($data);
            $action = 'Created performance evaluation for Employee ID: ' . $this->employee_id;
        }

        ActivityLog::create([
            'user_id' => Auth::id(),
            'module' => 'Appraisals',
            'action' => $this->isEdit ? 'update' : 'create',
            'description' => $action,
            'ip_address' => request()->ip(),
        ]);

        session()->flash('success', 'Performance Appraisal saved successfully!');
        $this->closeModal();
    }

    public function confirmAppraisal($id)
    {
        $app = Appraisal::findOrFail($id);
        $app->update(['status' => 'confirmed']);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'module' => 'Appraisals',
            'action' => 'update',
            'description' => 'Confirmed appraisal ID: ' . $id,
            'ip_address' => request()->ip(),
        ]);

        session()->flash('success', 'Appraisal status set to Confirmed!');
    }

    public function delete($id)
    {
        $app = Appraisal::findOrFail($id);
        $app->delete();

        session()->flash('success', 'Performance Appraisal deleted successfully!');
    }

    public function render()
    {
        $employees = Employee::orderBy('name')->get();
        $managers = User::orderBy('name')->get();

        $query = Appraisal::with(['employee', 'manager'])
            ->whereHas('employee', function($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            })->orWhere('period', 'like', '%' . $this->search . '%');

        // Statistics
        $avgScore = Appraisal::avg('score') ?: 0;
        $totalAppraisals = Appraisal::count();
        $confirmedCount = Appraisal::where('status', 'confirmed')->count();
        $draftCount = Appraisal::where('status', 'draft')->count();

        return view('livewire.appraisal-manager', [
            'appraisals' => $query->paginate(10),
            'employees' => $employees,
            'managers' => $managers,
            'stats' => [
                'avg_score' => round($avgScore, 1),
                'total' => $totalAppraisals,
                'confirmed' => $confirmedCount,
                'draft' => $draftCount,
            ]
        ])->layout('layouts.app');
    }
}
