<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Fleet;
use App\Models\FleetService;
use App\Models\FleetFuelLog;
use App\Models\Employee;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class FleetManager extends Component
{
    use WithPagination;

    public $search = '';
    public $activeTab = 'vehicles'; // vehicles, services, fuel_logs

    // Form fields for Fleet/Vehicle
    public $vehicle_id;
    public $license_plate;
    public $model;
    public $driver_id;
    public $status = 'active';
    public $odometer = 0;
    public $acquisition_date;

    // Form fields for Service
    public $service_id;
    public $service_fleet_id;
    public $service_date;
    public $service_description;
    public $service_cost = 0;
    public $service_provider;
    public $service_status = 'planned';

    // Form fields for Fuel Log
    public $fuel_id;
    public $fuel_fleet_id;
    public $fuel_date;
    public $fuel_liters = 0;
    public $fuel_cost_per_liter = 0;
    public $fuel_total_cost = 0;
    public $fuel_odometer;

    public $modalType = null; // vehicle, service, fuel
    public $isEdit = false;

    protected $queryString = ['search', 'activeTab'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function calculateFuelTotal()
    {
        $this->fuel_total_cost = floatval($this->fuel_liters) * floatval($this->fuel_cost_per_liter);
    }

    public function openModal($type, $id = null)
    {
        $this->modalType = $type;
        $this->isEdit = $id ? true : false;

        if ($type === 'vehicle') {
            if ($id) {
                $fleet = Fleet::findOrFail($id);
                $this->vehicle_id = $fleet->id;
                $this->license_plate = $fleet->license_plate;
                $this->model = $fleet->model;
                $this->driver_id = $fleet->driver_id;
                $this->status = $fleet->status;
                $this->odometer = $fleet->odometer;
                $this->acquisition_date = $fleet->acquisition_date ? $fleet->acquisition_date->format('Y-m-d') : null;
            } else {
                $this->resetVehicleFields();
            }
        } elseif ($type === 'service') {
            if ($id) {
                $service = FleetService::findOrFail($id);
                $this->service_id = $service->id;
                $this->service_fleet_id = $service->fleet_id;
                $this->service_date = $service->service_date ? $service->service_date->format('Y-m-d') : null;
                $this->service_description = $service->description;
                $this->service_cost = $service->cost;
                $this->service_provider = $service->provider;
                $this->service_status = $service->status;
            } else {
                $this->resetServiceFields();
            }
        } elseif ($type === 'fuel') {
            if ($id) {
                $fuel = FleetFuelLog::findOrFail($id);
                $this->fuel_id = $fuel->id;
                $this->fuel_fleet_id = $fuel->fleet_id;
                $this->fuel_date = $fuel->date ? $fuel->date->format('Y-m-d') : null;
                $this->fuel_liters = $fuel->liters;
                $this->fuel_cost_per_liter = $fuel->cost_per_liter;
                $this->fuel_total_cost = $fuel->total_cost;
                $this->fuel_odometer = $fuel->odometer;
            } else {
                $this->resetFuelFields();
            }
        }
    }

    public function closeModal()
    {
        $this->modalType = null;
        $this->isEdit = false;
    }

    private function resetVehicleFields()
    {
        $this->vehicle_id = null;
        $this->license_plate = '';
        $this->model = '';
        $this->driver_id = null;
        $this->status = 'active';
        $this->odometer = 0;
        $this->acquisition_date = date('Y-m-d');
    }

    private function resetServiceFields()
    {
        $this->service_id = null;
        $this->service_fleet_id = null;
        $this->service_date = date('Y-m-d');
        $this->service_description = '';
        $this->service_cost = 0;
        $this->service_provider = '';
        $this->service_status = 'planned';
    }

    private function resetFuelFields()
    {
        $this->fuel_id = null;
        $this->fuel_fleet_id = null;
        $this->fuel_date = date('Y-m-d');
        $this->fuel_liters = 0;
        $this->fuel_cost_per_liter = 0;
        $this->fuel_total_cost = 0;
        $this->fuel_odometer = null;
    }

    public function saveVehicle()
    {
        $this->validate([
            'license_plate' => 'required|string|unique:fleets,license_plate,' . $this->vehicle_id,
            'model' => 'required|string',
            'driver_id' => 'nullable|exists:employees,id',
            'status' => 'required|in:active,in_service,out_of_service,sold',
            'odometer' => 'required|numeric|min:0',
            'acquisition_date' => 'nullable|date',
        ]);

        $data = [
            'license_plate' => $this->license_plate,
            'model' => $this->model,
            'driver_id' => $this->driver_id ?: null,
            'status' => $this->status,
            'odometer' => $this->odometer,
            'acquisition_date' => $this->acquisition_date ?: null,
        ];

        if ($this->isEdit) {
            $fleet = Fleet::findOrFail($this->vehicle_id);
            $fleet->update($data);
            $action = 'Updated vehicle: ' . $this->license_plate;
        } else {
            Fleet::create($data);
            $action = 'Created vehicle: ' . $this->license_plate;
        }

        ActivityLog::create([
            'user_id' => Auth::id(),
            'module' => 'Fleet Management',
            'action' => $this->isEdit ? 'update' : 'create',
            'description' => $action,
            'ip_address' => request()->ip(),
        ]);

        session()->flash('success', $this->isEdit ? 'Vehicle updated successfully!' : 'Vehicle created successfully!');
        $this->closeModal();
    }

    public function saveService()
    {
        $this->validate([
            'service_fleet_id' => 'required|exists:fleets,id',
            'service_date' => 'required|date',
            'service_description' => 'required|string|max:255',
            'service_cost' => 'required|numeric|min:0',
            'service_provider' => 'nullable|string',
            'service_status' => 'required|in:planned,in_progress,completed,canceled',
        ]);

        $data = [
            'fleet_id' => $this->service_fleet_id,
            'service_date' => $this->service_date,
            'description' => $this->service_description,
            'cost' => $this->service_cost,
            'provider' => $this->service_provider,
            'status' => $this->service_status,
        ];

        if ($this->isEdit) {
            $service = FleetService::findOrFail($this->service_id);
            $service->update($data);
            $action = 'Updated fleet service ID: ' . $service->id;
        } else {
            FleetService::create($data);
            $action = 'Created fleet service';
        }

        ActivityLog::create([
            'user_id' => Auth::id(),
            'module' => 'Fleet Management',
            'action' => $this->isEdit ? 'update' : 'create',
            'description' => $action,
            'ip_address' => request()->ip(),
        ]);

        session()->flash('success', 'Service log saved successfully!');
        $this->closeModal();
    }

    public function saveFuel()
    {
        $this->validate([
            'fuel_fleet_id' => 'required|exists:fleets,id',
            'fuel_date' => 'required|date',
            'fuel_liters' => 'required|numeric|min:0.01',
            'fuel_cost_per_liter' => 'required|numeric|min:0.01',
            'fuel_odometer' => 'nullable|numeric|min:0',
        ]);

        $this->calculateFuelTotal();

        $data = [
            'fleet_id' => $this->fuel_fleet_id,
            'date' => $this->fuel_date,
            'liters' => $this->fuel_liters,
            'cost_per_liter' => $this->fuel_cost_per_liter,
            'total_cost' => $this->fuel_total_cost,
            'odometer' => $this->fuel_odometer,
        ];

        if ($this->isEdit) {
            $fuel = FleetFuelLog::findOrFail($this->fuel_id);
            $fuel->update($data);
            $action = 'Updated fuel log ID: ' . $fuel->id;
        } else {
            FleetFuelLog::create($data);
            $action = 'Created fuel log';
        }

        ActivityLog::create([
            'user_id' => Auth::id(),
            'module' => 'Fleet Management',
            'action' => $this->isEdit ? 'update' : 'create',
            'description' => $action,
            'ip_address' => request()->ip(),
        ]);

        session()->flash('success', 'Fuel log saved successfully!');
        $this->closeModal();
    }

    public function deleteVehicle($id)
    {
        $fleet = Fleet::findOrFail($id);
        $plate = $fleet->license_plate;
        $fleet->delete();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'module' => 'Fleet Management',
            'action' => 'delete',
            'description' => 'Deleted vehicle: ' . $plate,
            'ip_address' => request()->ip(),
        ]);

        session()->flash('success', 'Vehicle deleted successfully!');
    }

    public function deleteService($id)
    {
        $service = FleetService::findOrFail($id);
        $service->delete();

        session()->flash('success', 'Service log deleted successfully!');
    }

    public function deleteFuel($id)
    {
        $fuel = FleetFuelLog::findOrFail($id);
        $fuel->delete();

        session()->flash('success', 'Fuel log deleted successfully!');
    }

    public function render()
    {
        $employees = Employee::orderBy('name')->get();
        $fleets = Fleet::with('driver')->orderBy('license_plate')->get();

        $vehiclesQuery = Fleet::with('driver')
            ->where(function($q) {
                $q->where('license_plate', 'like', '%' . $this->search . '%')
                  ->orWhere('model', 'like', '%' . $this->search . '%');
            });

        $servicesQuery = FleetService::with('fleet')
            ->whereHas('fleet', function($q) {
                $q->where('license_plate', 'like', '%' . $this->search . '%');
            })->orWhere('description', 'like', '%' . $this->search . '%');

        $fuelLogsQuery = FleetFuelLog::with('fleet')
            ->whereHas('fleet', function($q) {
                $q->where('license_plate', 'like', '%' . $this->search . '%');
            });

        return view('livewire.fleet-manager', [
            'vehiclesList' => $vehiclesQuery->paginate(10),
            'servicesList' => $servicesQuery->paginate(10),
            'fuelLogsList' => $fuelLogsQuery->paginate(10),
            'employees' => $employees,
            'fleets' => $fleets,
        ])->layout('layouts.app');
    }
}
