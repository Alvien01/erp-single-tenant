<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Subscription;
use App\Models\Customer;
use App\Models\ActivityLog;

class SubscriptionManager extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';

    // Form fields
    public $subscription_id;
    public $subscription_number;
    public $customer_id;
    public $plan_name;
    public $price = 0;
    public $billing_cycle = 'monthly';
    public $start_date;
    public $end_date;
    public $status = 'active';
    public $notes;

    public $isOpen = false;
    public $isEditMode = false;

    // Delete confirmation
    public $confirmingDeletion = false;
    public $deleteId = null;

    protected $rules = [
        'customer_id' => 'required|exists:customers,id',
        'plan_name' => 'required|string|max:255',
        'price' => 'required|numeric|min:0',
        'billing_cycle' => 'required|in:monthly,quarterly,yearly',
        'start_date' => 'required|date',
        'end_date' => 'nullable|date|after_or_equal:start_date',
        'status' => 'required|in:active,paused,cancelled,expired',
        'notes' => 'nullable|string',
    ];

    protected $messages = [
        'customer_id.required' => 'Customer wajib dipilih.',
        'plan_name.required' => 'Nama paket langganan wajib diisi.',
        'price.required' => 'Harga langganan wajib diisi.',
        'start_date.required' => 'Tanggal mulai wajib diisi.',
    ];

    public function mount()
    {
        $this->start_date = now()->format('Y-m-d');
        $this->end_date = now()->addMonth()->format('Y-m-d');
    }

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetInputFields();
    }

    public function resetInputFields()
    {
        $this->subscription_id = null;
        $this->subscription_number = 'SUB-' . now()->format('Ymd') . '-' . sprintf('%04d', Subscription::count() + 1);
        $this->customer_id = '';
        $this->plan_name = '';
        $this->price = 0;
        $this->billing_cycle = 'monthly';
        $this->start_date = now()->format('Y-m-d');
        $this->end_date = now()->addMonth()->format('Y-m-d');
        $this->status = 'active';
        $this->notes = '';
        $this->isEditMode = false;
        $this->confirmingDeletion = false;
        $this->resetValidation();
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function updatedBillingCycle($value)
    {
        if ($this->start_date) {
            $startDate = \Carbon\Carbon::parse($this->start_date);
            if ($value === 'monthly') {
                $this->end_date = $startDate->addMonth()->format('Y-m-d');
            } elseif ($value === 'quarterly') {
                $this->end_date = $startDate->addMonths(3)->format('Y-m-d');
            } elseif ($value === 'yearly') {
                $this->end_date = $startDate->addYear()->format('Y-m-d');
            }
        }
    }

    public function store()
    {
        $this->validate();

        if (empty($this->subscription_number)) {
            $this->subscription_number = 'SUB-' . now()->format('Ymd') . '-' . sprintf('%04d', Subscription::count() + 1);
        }

        Subscription::updateOrCreate(['id' => $this->subscription_id], [
            'subscription_number' => $this->subscription_number,
            'customer_id' => $this->customer_id,
            'plan_name' => $this->plan_name,
            'price' => $this->price,
            'billing_cycle' => $this->billing_cycle,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'status' => $this->status,
            'notes' => $this->notes,
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'module' => 'Subscriptions',
            'action' => $this->isEditMode ? 'Update Subscription' : 'Create Subscription',
            'description' => 'Subscription ' . $this->subscription_number . ' for ' . $this->plan_name . ' has been saved.'
        ]);

        session()->flash('success', 'Subscription saved successfully.');
        $this->closeModal();
    }

    public function edit($id)
    {
        $sub = Subscription::findOrFail($id);
        $this->subscription_id = $sub->id;
        $this->subscription_number = $sub->subscription_number;
        $this->customer_id = $sub->customer_id;
        $this->plan_name = $sub->plan_name;
        $this->price = $sub->price;
        $this->billing_cycle = $sub->billing_cycle;
        $this->start_date = $sub->start_date;
        $this->end_date = $sub->end_date;
        $this->status = $sub->status;
        $this->notes = $sub->notes;
        $this->isEditMode = true;
        $this->openModal();
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->confirmingDeletion = true;
    }

    public function delete()
    {
        if ($this->deleteId) {
            $sub = Subscription::find($this->deleteId);
            if ($sub) {
                ActivityLog::create([
                    'user_id' => auth()->id(),
                    'module' => 'Subscriptions',
                    'action' => 'Delete Subscription',
                    'description' => 'Subscription ' . $sub->subscription_number . ' has been deleted.'
                ]);
                $sub->delete();
                session()->flash('success', 'Subscription deleted successfully.');
            }
        }
        $this->confirmingDeletion = false;
        $this->deleteId = null;
    }

    public function render()
    {
        $query = Subscription::with('customer');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('subscription_number', 'like', '%' . $this->search . '%')
                  ->orWhere('plan_name', 'like', '%' . $this->search . '%')
                  ->orWhereHas('customer', function ($c) {
                      $c->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('company_name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        $activeCount = Subscription::where('status', 'active')->count();
        $totalMonthly = Subscription::where('status', 'active')->sum('price');

        return view('livewire.subscription-manager', [
            'subscriptions' => $query->latest()->paginate(10),
            'customers' => Customer::orderBy('name')->get(),
            'activeCount' => $activeCount,
            'totalMonthly' => $totalMonthly,
        ])->layout('layouts.app');
    }
}
