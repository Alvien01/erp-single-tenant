<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Warehouse;
use App\Models\ActivityLog;

class WarehouseManager extends Component
{
    use WithPagination;

    public $search = '';
    
    // Form fields
    public $warehouse_id;
    public $warehouse_name;
    public $location;

    // Sub-locations management
    public $selectedWarehouseForLocations = null;
    public $new_loc_code;
    public $new_loc_name;

    public $isOpen = false;
    public $isEditMode = false;

    // Delete warehouse confirmation modal state
    public $confirmingWarehouseDeletion = false;
    public $deleteWarehouseId = null;
    public $deleteWarehouseName = '';

    // Delete location confirmation modal state
    public $confirmingLocationDeletion = false;
    public $deleteLocationId = null;
    public $deleteLocationName = '';

    public function openModal()
    {
        $this->confirmingWarehouseDeletion = false;
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetInputFields();
    }

    public function resetInputFields()
    {
        $this->warehouse_id = null;
        $this->warehouse_name = '';
        $this->location = '';
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
            'warehouse_name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
        ]);

        Warehouse::updateOrCreate(['id' => $this->warehouse_id], [
            'warehouse_code' => $this->isEditMode ? Warehouse::find($this->warehouse_id)->warehouse_code : 'WH-' . strtoupper(substr(uniqid(), -5)),
            'warehouse_name' => $this->warehouse_name,
            'address' => $this->location,
        ]);

        ActivityLog::create([
            'user_id' => auth()->id() ?? 1,
            'module' => 'Master Data',
            'action' => $this->isEditMode ? 'Update Warehouse' : 'Create Warehouse',
            'description' => 'Warehouse ' . $this->warehouse_name . ' has been saved.'
        ]);

        session()->flash('success', 'Warehouse saved successfully.');
        $this->closeModal();
    }

    public function edit($id)
    {
        $warehouse = Warehouse::findOrFail($id);
        $this->warehouse_id = $warehouse->id;
        $this->warehouse_name = $warehouse->warehouse_name;
        $this->location = $warehouse->address;

        $this->isEditMode = true;
        $this->openModal();
    }

    public function confirmDelete($id)
    {
        $warehouse = Warehouse::find($id);
        if ($warehouse) {
            $this->isOpen = false;
            $this->deleteWarehouseId = $warehouse->id;
            $this->deleteWarehouseName = $warehouse->warehouse_name;
            $this->confirmingWarehouseDeletion = true;
        }
    }

    public function cancelDelete()
    {
        $this->confirmingWarehouseDeletion = false;
        $this->deleteWarehouseId = null;
        $this->deleteWarehouseName = '';
    }

    public function delete($id = null)
    {
        $targetId = $id ?? $this->deleteWarehouseId;
        if (!$targetId) {
            return;
        }

        $warehouse = Warehouse::findOrFail($targetId);
        
        ActivityLog::create([
            'user_id' => auth()->id() ?? 1,
            'module' => 'Master Data',
            'action' => 'Delete Warehouse',
            'description' => 'Warehouse ' . $warehouse->warehouse_name . ' has been deleted.'
        ]);

        $warehouse->delete();
        session()->flash('success', 'Warehouse deleted successfully.');
        $this->cancelDelete();
    }

    // Warehouse Location management
    public function manageLocations($id)
    {
        $this->selectedWarehouseForLocations = Warehouse::with('locations')->findOrFail($id);
        $this->new_loc_code = 'LOC-' . strtoupper(substr(uniqid(), -4));
        $this->new_loc_name = '';
    }

    public function closeLocations()
    {
        $this->selectedWarehouseForLocations = null;
        $this->cancelDeleteLocation();
    }

    public function addLocation()
    {
        $this->validate([
            'new_loc_code' => 'required|string|max:50',
            'new_loc_name' => 'required|string|max:100',
        ]);

        \App\Models\WarehouseLocation::create([
            'warehouse_id' => $this->selectedWarehouseForLocations->id,
            'location_code' => $this->new_loc_code,
            'location_name' => $this->new_loc_name,
        ]);

        ActivityLog::create([
            'user_id' => auth()->id() ?? 1,
            'module' => 'Master Data',
            'action' => 'Add Warehouse Location',
            'description' => "Location {$this->new_loc_name} ({$this->new_loc_code}) added to warehouse {$this->selectedWarehouseForLocations->warehouse_name}."
        ]);

        // Refresh locations
        $this->manageLocations($this->selectedWarehouseForLocations->id);
        session()->flash('success_loc', 'Sub-location added successfully.');
    }

    public function confirmDeleteLocation($id)
    {
        $loc = \App\Models\WarehouseLocation::find($id);
        if ($loc) {
            $this->deleteLocationId = $loc->id;
            $this->deleteLocationName = $loc->location_name . ' (' . $loc->location_code . ')';
            $this->confirmingLocationDeletion = true;
        }
    }

    public function cancelDeleteLocation()
    {
        $this->confirmingLocationDeletion = false;
        $this->deleteLocationId = null;
        $this->deleteLocationName = '';
    }

    public function deleteLocation($id = null)
    {
        $targetId = $id ?? $this->deleteLocationId;
        if (!$targetId) {
            return;
        }

        $loc = \App\Models\WarehouseLocation::findOrFail($targetId);
        $wId = $loc->warehouse_id;

        ActivityLog::create([
            'user_id' => auth()->id() ?? 1,
            'module' => 'Master Data',
            'action' => 'Delete Warehouse Location',
            'description' => "Location {$loc->location_name} ({$loc->location_code}) deleted."
        ]);

        $loc->delete();
        $this->cancelDeleteLocation();
        $this->manageLocations($wId);
        session()->flash('success_loc', 'Sub-location deleted successfully.');
    }

    public function render()
    {
        $query = Warehouse::query();

        if ($this->search) {
            $query->where('warehouse_name', 'like', '%' . $this->search . '%')
                  ->orWhere('address', 'like', '%' . $this->search . '%');
        }

        return view('livewire.warehouse-manager', [
            'warehouses' => $query->orderBy('warehouse_name')->paginate(10),
        ]);
    }
}
