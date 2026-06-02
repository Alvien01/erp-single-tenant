<?php

namespace App\Livewire;

use Livewire\Component;

use App\Models\Project;
use App\Models\Task;
use App\Models\Timesheet;
use App\Models\Customer;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class ProjectManager extends Component
{
    public $search = '';
    public $isOpen = false;
    public $isTaskOpen = false;
    public $isEditMode = false;
    
    public $activeTab = 'projects'; // projects, tasks, timesheets

    // Project Form
    public $project_id, $name, $description, $customer_id, $start_date, $end_date, $status = 'planned';
    
    // Task Form
    public $task_id, $selected_project_id, $task_name, $task_desc, $assigned_to, $task_status = 'todo', $priority = 'medium', $due_date;

    protected $rules = [
        'name' => 'required|string|max:255',
        'status' => 'required|in:planned,in_progress,completed,on_hold',
    ];

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
    }

    // PROJECT CRUD
    public function resetProjectForm()
    {
        $this->project_id = null; $this->name = ''; $this->description = ''; $this->customer_id = '';
        $this->start_date = ''; $this->end_date = ''; $this->status = 'planned'; $this->isEditMode = false;
    }

    public function createProject()
    {
        $this->resetProjectForm();
        $this->isOpen = true;
    }

    public function storeProject()
    {
        $this->validate();
        
        Project::query()->updateOrCreate(['id' => $this->project_id], [
            'name' => $this->name,
            'description' => $this->description,
            'customer_id' => $this->customer_id ?: null,
            'start_date' => $this->start_date ?: null,
            'end_date' => $this->end_date ?: null,
            'status' => $this->status,
        ]);

        session()->flash('success', 'Project saved successfully.');
        $this->isOpen = false;
    }

    public function editProject($id)
    {
        $p = Project::query()->findOrFail($id);
        $this->project_id = $p->id; $this->name = $p->name; $this->description = $p->description;
        $this->customer_id = $p->customer_id; $this->start_date = $p->start_date?->format('Y-m-d');
        $this->end_date = $p->end_date?->format('Y-m-d'); $this->status = $p->status;
        
        $this->isEditMode = true; $this->isOpen = true;
    }

    public function deleteProject($id)
    {
        Project::query()->findOrFail($id)->delete();
        session()->flash('success', 'Project deleted.');
    }

    // TASK CRUD
    public function resetTaskForm()
    {
        $this->task_id = null; $this->selected_project_id = ''; $this->task_name = '';
        $this->task_desc = ''; $this->assigned_to = ''; $this->task_status = 'todo';
        $this->priority = 'medium'; $this->due_date = ''; $this->isEditMode = false;
    }

    public function createTask()
    {
        $this->resetTaskForm();
        $this->isTaskOpen = true;
    }

    public function storeTask()
    {
        $this->validate([
            'selected_project_id' => 'required|exists:projects,id',
            'task_name' => 'required|string|max:255',
            'task_status' => 'required|in:todo,in_progress,done',
        ]);

        Task::query()->updateOrCreate(['id' => $this->task_id], [
            'project_id' => $this->selected_project_id,
            'name' => $this->task_name,
            'description' => $this->task_desc,
            'assigned_to' => $this->assigned_to ?: null,
            'status' => $this->task_status,
            'priority' => $this->priority,
            'due_date' => $this->due_date ?: null,
        ]);

        session()->flash('success', 'Task saved.');
        $this->isTaskOpen = false;
    }

    public function updateTaskStatus($taskId, $newStatus)
    {
        $task = Task::query()->findOrFail($taskId);
        $task->status = $newStatus;
        $task->save();

        ActivityLog::create([
            'user_id' => auth()->id() ?? 1,
            'module' => 'Projects',
            'action' => 'Drag & Drop Task Transition',
            'description' => "Task '{$task->name}' moved to stage: {$newStatus}."
        ]);

        session()->flash('success', "Task status updated to {$newStatus}.");
    }

    public function render()
    {
        return view('livewire.project-manager', [
            'projects' => Project::query()->with('customer')->orderByDesc('id')->get(),
            'tasks' => Task::query()->with(['project', 'assignee'])->orderByDesc('id')->get(),
            'customers' => Customer::all(),
            'users' => User::all(),
        ]);
    }
}
