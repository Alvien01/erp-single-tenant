<?php

namespace App\Livewire;

use Livewire\Component;

use App\Models\Lead;
use App\Models\User;
use App\Models\Customer;
use App\Models\SalesQuotation;
use App\Models\SalesQuotationItem;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class CrmManager extends Component
{
    public $search = '';
    public $isOpen = false;
    public $isEditMode = false;

    // Form fields
    public $lead_id;
    public $title;
    public $contact_name;
    public $company_name;
    public $email;
    public $phone;
    public $expected_revenue = 0;
    public $probability = 10;
    public $status = 'new';
    public $user_id;
    public $notes;

    protected $rules = [
        'title' => 'required|string|max:255',
        'contact_name' => 'required|string|max:255',
        'company_name' => 'nullable|string|max:255',
        'email' => 'nullable|email|max:255',
        'phone' => 'nullable|string|max:50',
        'expected_revenue' => 'required|numeric|min:0',
        'probability' => 'required|integer|min:0|max:100',
        'status' => 'required|in:new,qualified,proposition,won,lost',
        'user_id' => 'nullable|exists:users,id',
        'notes' => 'nullable|string',
    ];

    public function mount()
    {
        $this->resetInputFields();
    }

    public function resetInputFields()
    {
        $this->lead_id = null;
        $this->title = '';
        $this->contact_name = '';
        $this->company_name = '';
        $this->email = '';
        $this->phone = '';
        $this->expected_revenue = 0;
        $this->probability = 10;
        $this->status = 'new';
        $this->user_id = Auth::id();
        $this->notes = '';
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

        Lead::query()->updateOrCreate(
            ['id' => $this->lead_id],
            [
                'title' => $this->title,
                'contact_name' => $this->contact_name,
                'company_name' => $this->company_name,
                'email' => $this->email,
                'phone' => $this->phone,
                'expected_revenue' => $this->expected_revenue,
                'probability' => $this->probability,
                'status' => $this->status,
                'user_id' => $this->user_id,
                'notes' => $this->notes,
            ]
        );

        ActivityLog::query()->create([
            'user_id' => Auth::id() ?? 1,
            'module' => 'CRM',
            'action' => $this->isEditMode ? 'Update Lead' : 'Create Lead',
            'description' => 'Lead "' . $this->title . '" saved successfully.'
        ]);

        session()->flash('success', 'Lead saved successfully.');
        $this->isOpen = false;
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $lead = Lead::query()->findOrFail($id);
        $this->lead_id = $lead->id;
        $this->title = $lead->title;
        $this->contact_name = $lead->contact_name;
        $this->company_name = $lead->company_name;
        $this->email = $lead->email;
        $this->phone = $lead->phone;
        $this->expected_revenue = $lead->expected_revenue;
        $this->probability = $lead->probability;
        $this->status = $lead->status;
        $this->user_id = $lead->user_id;
        $this->notes = $lead->notes;

        $this->isEditMode = true;
        $this->isOpen = true;
    }

    public function updateStage($id, $newStatus)
    {
        $lead = Lead::query()->findOrFail($id);
        $oldStatus = $lead->status;
        
        // Auto probabilities for stages
        $prob = match($newStatus) {
            'new' => 10,
            'qualified' => 30,
            'proposition' => 70,
            'won' => 100,
            'lost' => 0,
            default => 10,
        };

        $lead->update([
            'status' => $newStatus,
            'probability' => $prob,
        ]);

        ActivityLog::query()->create([
            'user_id' => Auth::id() ?? 1,
            'module' => 'CRM',
            'action' => 'Update Lead Status',
            'description' => "Updated Lead '{$lead->title}' status from {$oldStatus} to {$newStatus}."
        ]);

        session()->flash('success', "Lead moved to " . ucfirst($newStatus));
    }

    public function convertToQuotation($id)
    {
        $lead = Lead::query()->findOrFail($id);

        // 1. Ensure the customer exists or create a new Customer record from the Lead details
        $customer = Customer::query()->where('email', $lead->email)->first();
        if (!$customer) {
            $customer = Customer::query()->create([
                'name' => $lead->contact_name,
                'company_name' => $lead->company_name ?? 'Individual Lead',
                'email' => $lead->email ?? 'lead-' . $lead->id . '@erp.com',
                'phone' => $lead->phone ?? '-',
                'address' => 'Converted from CRM Lead notes: ' . ($lead->notes ?? '-'),
                'is_active' => true,
            ]);
        }

        // 2. Create the Sales Quotation
        $sqNum = 'SQ-' . now()->format('Ymd') . '-' . sprintf('%04d', SalesQuotation::query()->count() + 1);
        $sq = SalesQuotation::query()->create([
            'sq_number' => $sqNum,
            'customer_id' => $customer->id,
            'valid_until' => now()->addDays(14)->format('Y-m-d'),
            'status' => 'draft',
            'created_by' => Auth::id() ?? 1,
        ]);

        // 3. Mark Lead as Won
        $lead->update(['status' => 'won', 'probability' => 100]);

        ActivityLog::query()->create([
            'user_id' => Auth::id() ?? 1,
            'module' => 'CRM',
            'action' => 'Convert Lead to SQ',
            'description' => "Converted Lead '{$lead->title}' to Sales Quotation {$sqNum}."
        ]);

        session()->flash('success', "Lead converted to Sales Quotation: {$sqNum}");
    }

    public function delete($id)
    {
        $lead = Lead::query()->findOrFail($id);
        $lead->delete();

        session()->flash('success', 'Lead deleted successfully.');
    }

    public function render()
    {
        $query = Lead::query()->with('salesperson');

        if ($this->search) {
            $query->where(function($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('contact_name', 'like', '%' . $this->search . '%')
                  ->orWhere('company_name', 'like', '%' . $this->search . '%');
            });
        }

        $leads = $query->get();

        return view('livewire.crm-manager', [
            'leads' => $leads,
            'users' => User::all(),
            'stages' => ['new', 'qualified', 'proposition', 'won', 'lost']
        ]);
    }
}
