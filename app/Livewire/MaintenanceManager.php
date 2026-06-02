<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\MaintenanceRequest;
use App\Models\Asset;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class MaintenanceManager extends Component
{
    use WithPagination;

    public $search = '';
    public $activeTab = 'requests'; // requests, calendar/stats

    // Form fields
    public $request_id;
    public $asset_id;
    public $asset_name;
    public $description;
    public $request_date;
    public $repair_date;
    public $cost = 0;
    public $status = 'requested';
    public $priority = 'medium';

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
            $req = MaintenanceRequest::findOrFail($id);
            $this->request_id = $req->id;
            $this->asset_id = $req->asset_id;
            $this->asset_name = $req->asset_name;
            $this->description = $req->description;
            $this->request_date = $req->request_date ? $req->request_date->format('Y-m-d') : null;
            $this->repair_date = $req->repair_date ? $req->repair_date->format('Y-m-d') : null;
            $this->cost = $req->cost;
            $this->status = $req->status;
            $this->priority = $req->priority;
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
        $this->request_id = null;
        $this->asset_id = null;
        $this->asset_name = '';
        $this->description = '';
        $this->request_date = date('Y-m-d');
        $this->repair_date = null;
        $this->cost = 0;
        $this->status = 'requested';
        $this->priority = 'medium';
    }

    public function onAssetChange($assetId)
    {
        if ($assetId) {
            $asset = Asset::find($assetId);
            if ($asset) {
                $this->asset_name = $asset->asset_name;
            }
        }
    }

    public function save()
    {
        $this->validate([
            'asset_id' => 'nullable|exists:assets,id',
            'asset_name' => 'required|string|max:255',
            'description' => 'required|string',
            'request_date' => 'required|date',
            'repair_date' => 'nullable|date|after_or_equal:request_date',
            'cost' => 'required|numeric|min:0',
            'status' => 'required|in:requested,in_progress,repaired,scrap',
            'priority' => 'required|in:low,medium,high',
        ]);

        $data = [
            'asset_id' => $this->asset_id ?: null,
            'asset_name' => $this->asset_name,
            'description' => $this->description,
            'request_date' => $this->request_date,
            'repair_date' => $this->repair_date ?: null,
            'cost' => $this->cost,
            'status' => $this->status,
            'priority' => $this->priority,
        ];

        if ($this->isEdit) {
            $req = MaintenanceRequest::findOrFail($this->request_id);
            $req->update($data);
            $action = 'Updated maintenance request for: ' . $this->asset_name;
        } else {
            MaintenanceRequest::create($data);
            $action = 'Created maintenance request for: ' . $this->asset_name;
        }

        ActivityLog::create([
            'user_id' => Auth::id(),
            'module' => 'Maintenance Management',
            'action' => $this->isEdit ? 'update' : 'create',
            'description' => $action,
            'ip_address' => request()->ip(),
        ]);

        session()->flash('success', 'Maintenance request saved successfully!');
        $this->closeModal();
    }

    public function delete($id)
    {
        $req = MaintenanceRequest::findOrFail($id);
        $asset = $req->asset_name;
        $req->delete();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'module' => 'Maintenance Management',
            'action' => 'delete',
            'description' => 'Deleted maintenance request: ' . $asset,
            'ip_address' => request()->ip(),
        ]);

        session()->flash('success', 'Maintenance request deleted successfully!');
    }

    public function render()
    {
        $assets = Asset::orderBy('asset_name')->get();

        $query = MaintenanceRequest::with('asset')
            ->where(function($q) {
                $q->where('asset_name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });

        // Statistics
        $totalCost = MaintenanceRequest::sum('cost');
        $activeRepairs = MaintenanceRequest::whereIn('status', ['requested', 'in_progress'])->count();
        $repairedCount = MaintenanceRequest::where('status', 'repaired')->count();
        $scrapCount = MaintenanceRequest::where('status', 'scrap')->count();

        return view('livewire.maintenance-manager', [
            'requests' => $query->orderBy('priority', 'desc')->paginate(10),
            'assets' => $assets,
            'stats' => [
                'total_cost' => $totalCost,
                'active_repairs' => $activeRepairs,
                'repaired' => $repairedCount,
                'scrap' => $scrapCount,
            ]
        ])->layout('layouts.app');
    }
}
