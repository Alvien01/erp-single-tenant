<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Customer;
use App\Models\ActivityLog;

class CustomerManager extends Component
{
    use WithPagination;

    public $search = '';
    
    // Form fields
    public $customer_id;
    public $name;
    public $email;
    public $phone;
    public $company_name;
    public $address;
    public $type = 'individual';

    public $isOpen = false;
    public $isEditMode = false;

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
        $this->customer_id = null;
        $this->name = '';
        $this->email = '';
        $this->phone = '';
        $this->company_name = '';
        $this->address = '';
        $this->type = 'individual';
        $this->isEditMode = false;
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function store()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'company_name' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'type' => 'required|in:individual,company',
        ]);

        Customer::updateOrCreate(['id' => $this->customer_id], [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'company_name' => $this->company_name,
            'address' => $this->address,
            'type' => $this->type,
        ]);

        ActivityLog::create([
            'user_id' => auth()->id() ?? 1,
            'module' => 'Master Data',
            'action' => $this->isEditMode ? 'Update Customer' : 'Create Customer',
            'description' => 'Customer ' . $this->name . ' has been saved.'
        ]);

        session()->flash('success', 'Customer saved successfully.');
        $this->closeModal();
    }

    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        $this->customer_id = $customer->id;
        $this->name = $customer->name;
        $this->email = $customer->email;
        $this->phone = $customer->phone;
        $this->company_name = $customer->company_name;
        $this->address = $customer->address;
        $this->type = $customer->type;

        $this->isEditMode = true;
        $this->openModal();
    }

    public function delete($id)
    {
        $customer = Customer::findOrFail($id);
        
        ActivityLog::create([
            'user_id' => auth()->id() ?? 1,
            'module' => 'Master Data',
            'action' => 'Delete Customer',
            'description' => 'Customer ' . $customer->name . ' has been deleted.'
        ]);

        $customer->delete();
        session()->flash('success', 'Customer deleted successfully.');
    }

    public function render()
    {
        $query = Customer::query();

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('company_name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
        }

        return view('livewire.customer-manager', [
            'customers' => $query->orderBy('name')->paginate(10),
        ]);
    }
}
