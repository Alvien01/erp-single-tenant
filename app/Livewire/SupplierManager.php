<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Supplier;
use App\Models\ActivityLog;

class SupplierManager extends Component
{
    use WithPagination;

    public $search = '';
    
    // Form fields
    public $supplier_id;
    public $name;
    public $email;
    public $phone;
    public $company_name;
    public $address;
    public $status = 'active';

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
        $this->supplier_id = null;
        $this->name = '';
        $this->email = '';
        $this->phone = '';
        $this->company_name = '';
        $this->address = '';
        $this->status = 'active';
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
            'status' => 'required|in:active,inactive',
        ]);

        Supplier::updateOrCreate(['id' => $this->supplier_id], [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'company_name' => $this->company_name,
            'address' => $this->address,
            'status' => $this->status,
        ]);

        ActivityLog::create([
            'user_id' => auth()->id() ?? 1,
            'module' => 'Master Data',
            'action' => $this->isEditMode ? 'Update Supplier' : 'Create Supplier',
            'description' => 'Supplier ' . $this->name . ' has been saved.'
        ]);

        session()->flash('success', 'Supplier saved successfully.');
        $this->closeModal();
    }

    public function edit($id)
    {
        $supplier = Supplier::findOrFail($id);
        $this->supplier_id = $supplier->id;
        $this->name = $supplier->name;
        $this->email = $supplier->email;
        $this->phone = $supplier->phone;
        $this->company_name = $supplier->company_name;
        $this->address = $supplier->address;
        $this->status = $supplier->status;

        $this->isEditMode = true;
        $this->openModal();
    }

    public function delete($id)
    {
        $supplier = Supplier::findOrFail($id);
        
        ActivityLog::create([
            'user_id' => auth()->id() ?? 1,
            'module' => 'Master Data',
            'action' => 'Delete Supplier',
            'description' => 'Supplier ' . $supplier->name . ' has been deleted.'
        ]);

        $supplier->delete();
        session()->flash('success', 'Supplier deleted successfully.');
    }

    public function render()
    {
        $query = Supplier::query();

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('company_name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
        }

        return view('livewire.supplier-manager', [
            'suppliers' => $query->orderBy('name')->paginate(10),
        ]);
    }
}
