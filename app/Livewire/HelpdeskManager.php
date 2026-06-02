<?php

namespace App\Livewire;

use Livewire\Component;

use App\Models\Ticket;
use App\Models\Customer;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class HelpdeskManager extends Component
{
    public $search = '';
    public $isOpen = false;
    public $isEditMode = false;

    // Form fields
    public $ticket_id;
    public $subject;
    public $customer_id;
    public $description;
    public $status = 'open';
    public $priority = 'medium';
    public $assigned_to;

    protected $rules = [
        'subject' => 'required|string|max:255',
        'customer_id' => 'nullable|exists:customers,id',
        'description' => 'required|string',
        'status' => 'required|in:open,in_progress,resolved,closed',
        'priority' => 'required|in:low,medium,high,urgent',
        'assigned_to' => 'nullable|exists:users,id',
    ];

    public function resetInputFields()
    {
        $this->ticket_id = null;
        $this->subject = '';
        $this->customer_id = '';
        $this->description = '';
        $this->status = 'open';
        $this->priority = 'medium';
        $this->assigned_to = null;
        $this->isEditMode = false;
    }

    public function create()
    {
        $this->resetInputFields();
        $this->isOpen = true;
    }

    public function store()
    {
        $this->validate();

        $data = [
            'subject' => $this->subject,
            'customer_id' => $this->customer_id ?: null,
            'description' => $this->description,
            'status' => $this->status,
            'priority' => $this->priority,
            'assigned_to' => $this->assigned_to ?: null,
            'resolved_at' => ($this->status === 'resolved' || $this->status === 'closed') ? now() : null,
        ];

        Ticket::query()->updateOrCreate(['id' => $this->ticket_id], $data);

        ActivityLog::query()->create([
            'user_id' => Auth::id() ?? 1,
            'module' => 'Helpdesk',
            'action' => $this->isEditMode ? 'Update Ticket' : 'Create Ticket',
            'description' => 'Ticket "' . $this->subject . '" saved.'
        ]);

        session()->flash('success', 'Ticket saved successfully.');
        $this->isOpen = false;
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $ticket = Ticket::query()->findOrFail($id);
        $this->ticket_id = $ticket->id;
        $this->subject = $ticket->subject;
        $this->customer_id = $ticket->customer_id;
        $this->description = $ticket->description;
        $this->status = $ticket->status;
        $this->priority = $ticket->priority;
        $this->assigned_to = $ticket->assigned_to;

        $this->isEditMode = true;
        $this->isOpen = true;
    }

    public function delete($id)
    {
        Ticket::query()->findOrFail($id)->delete();
        session()->flash('success', 'Ticket deleted successfully.');
    }

    public function render()
    {
        $query = Ticket::query()->with(['customer', 'assignee']);

        if ($this->search) {
            $query->where('subject', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
        }

        return view('livewire.helpdesk-manager', [
            'tickets' => $query->orderBy('created_at', 'desc')->paginate(10),
            'customers' => Customer::all(),
            'users' => User::all(),
        ]);
    }
}
