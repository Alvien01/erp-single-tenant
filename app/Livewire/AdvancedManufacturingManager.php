<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\MrpRouting;
use App\Models\SubcontractingOrder;
use App\Models\EcoRequest;
use App\Models\ProductionSchedule;
use App\Models\Bom;
use App\Models\Product;
use App\Models\WorkCenter;
use App\Models\Supplier;
use Illuminate\Support\Facades\Auth;

class AdvancedManufacturingManager extends Component
{
    use WithPagination;

    public $search = '';
    public $activeTab = 'routings'; // routings, subcontracting, eco, mps
    
    // Modal properties
    public $modalType = null;
    public $isEdit = false;
    public $editId = null;
    
    // Form properties for each type
    public $formData = [];

    public function render()
    {
        $s = '%'.$this->search.'%';
        return view('livewire.advanced-manufacturing-manager', [
            'routings' => MrpRouting::with('bom')->where('name', 'like', $s)->paginate(10, ['*'], 'rtPage'),
            'subcontracts' => SubcontractingOrder::with(['supplier', 'product'])->where('subcontract_number', 'like', $s)->paginate(10, ['*'], 'subPage'),
            'ecos' => EcoRequest::with(['bom', 'product', 'requester'])->where('eco_number', 'like', $s)->paginate(10, ['*'], 'ecoPage'),
            'schedules' => ProductionSchedule::with('product')->where('period', 'like', $s)->paginate(10, ['*'], 'mpsPage'),
            'boms' => Bom::with('product')->get(),
            'products' => Product::orderBy('name')->get(),
            'workCenters' => WorkCenter::all(),
            'suppliers' => Supplier::all(),
        ])->layout('layouts.app');
    }
    
    /**
     * Open modal for creating or editing a record
     */
    public function openModal($type, $id = null)
    {
        $this->modalType = $type;
        $this->isEdit = !is_null($id);
        $this->editId = $id;
        $this->formData = [];
        
        if ($this->isEdit && $id) {
            $this->loadRecordData($type, $id);
        }
    }
    
    /**
     * Load existing record data for editing
     */
    protected function loadRecordData($type, $id)
    {
        switch ($type) {
            case 'routings':
                $record = MrpRouting::find($id);
                if ($record) {
                    $this->formData = $record->toArray();
                }
                break;
            case 'subcontracting':
                $record = SubcontractingOrder::find($id);
                if ($record) {
                    $this->formData = $record->toArray();
                }
                break;
            case 'eco':
                $record = EcoRequest::find($id);
                if ($record) {
                    $this->formData = $record->toArray();
                }
                break;
            case 'mps':
                $record = ProductionSchedule::find($id);
                if ($record) {
                    $this->formData = $record->toArray();
                }
                break;
        }
    }
    
    /**
     * Close the modal
     */
    public function closeModal()
    {
        $this->modalType = null;
        $this->isEdit = false;
        $this->editId = null;
        $this->formData = [];
    }
    
    /**
     * Save the record (create or update)
     */
    public function saveRecord()
    {
        $this->validateFormData();
        
        switch ($this->modalType) {
            case 'routings':
                if ($this->isEdit) {
                    $record = MrpRouting::find($this->editId);
                    if ($record) {
                        $record->update($this->formData);
                    }
                } else {
                    MrpRouting::create($this->formData);
                }
                break;
            case 'subcontracting':
                if ($this->isEdit) {
                    $record = SubcontractingOrder::find($this->editId);
                    if ($record) {
                        $record->update($this->formData);
                    }
                } else {
                    SubcontractingOrder::create($this->formData);
                }
                break;
            case 'eco':
                if ($this->isEdit) {
                    $record = EcoRequest::find($this->editId);
                    if ($record) {
                        $record->update($this->formData);
                    }
                } else {
                    $this->formData['requester_id'] = Auth::id();
                    EcoRequest::create($this->formData);
                }
                break;
            case 'mps':
                if ($this->isEdit) {
                    $record = ProductionSchedule::find($this->editId);
                    if ($record) {
                        $record->update($this->formData);
                    }
                } else {
                    ProductionSchedule::create($this->formData);
                }
                break;
        }
        
        session()->flash('message', 'Record saved successfully.');
        $this->closeModal();
        $this->resetPage();
    }
    
    /**
     * Validate form data based on type
     */
    protected function validateFormData()
    {
        $rules = [];
        
        switch ($this->modalType) {
            case 'routings':
                $rules = [
                    'formData.name' => 'required|string|max:255',
                    'formData.bom_id' => 'required|exists:boms,id',
                    'formData.is_active' => 'boolean',
                ];
                break;
            case 'subcontracting':
                $rules = [
                    'formData.subcontract_number' => 'required|string|max:255',
                    'formData.supplier_id' => 'required|exists:suppliers,id',
                    'formData.product_id' => 'required|exists:products,id',
                    'formData.quantity' => 'required|numeric|min:1',
                    'formData.status' => 'string|max:50',
                ];
                break;
            case 'eco':
                $rules = [
                    'formData.eco_number' => 'required|string|max:255',
                    'formData.title' => 'required|string|max:255',
                    'formData.description' => 'nullable|string',
                    'formData.status' => 'string|max:50',
                ];
                break;
            case 'mps':
                $rules = [
                    'formData.product_id' => 'required|exists:products,id',
                    'formData.quantity' => 'required|numeric|min:1',
                    'formData.period' => 'required|string|max:50',
                    'formData.status' => 'string|max:50',
                ];
                break;
        }
        
        $this->validate($rules);
    }
    
    /**
     * Delete a record
     */
    public function delete($type, $id)
    {
        try {
            switch ($type) {
                case 'routings':
                    $record = MrpRouting::find($id);
                    if ($record) {
                        $record->delete();
                    }
                    break;
                case 'subcontracting':
                    $record = SubcontractingOrder::find($id);
                    if ($record) {
                        $record->delete();
                    }
                    break;
                case 'eco':
                    $record = EcoRequest::find($id);
                    if ($record) {
                        $record->delete();
                    }
                    break;
                case 'mps':
                    $record = ProductionSchedule::find($id);
                    if ($record) {
                        $record->delete();
                    }
                    break;
            }
            
            session()->flash('message', 'Record deleted successfully.');
            $this->resetPage();
        } catch (\Exception $e) {
            session()->flash('error', 'Unable to delete record: ' . $e->getMessage());
        }
    }
    
    /**
     * Reset pagination when search or tab changes
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }
    
    public function updatingActiveTab()
    {
        $this->resetPage();
        $this->search = '';
    }
    
    /**
     * Get validation error messages
     */
    protected function getValidationErrorMessages()
    {
        return [
            'formData.name.required' => 'Name is required.',
            'formData.subcontract_number.required' => 'Subcontract number is required.',
            'formData.eco_number.required' => 'ECO number is required.',
            'formData.title.required' => 'Title is required.',
            'formData.product_id.required' => 'Product is required.',
            'formData.quantity.required' => 'Quantity is required.',
            'formData.quantity.min' => 'Quantity must be at least 1.',
        ];
    }
}