<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\EcoRequest;
use App\Models\WorkCenter;
use App\Models\Product;
use App\Models\Bom;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class PlmManager extends Component
{
    use WithPagination;

    public $activeTab = 'eco'; // 'eco' or 'work_centers'
    public $search = '';
    public $statusFilter = '';

    // Modal state
    public $isOpen = false;
    public $isEditMode = false;
    public $confirmingDeletion = false;
    public $deleteType = null;
    public $deleteId = null;

    // ECO Form fields
    public $eco_id;
    public $eco_number;
    public $product_id;
    public $bom_id;
    public $title;
    public $description;
    public $reason = 'Cost Optimization';
    public $old_bom_version;
    public $new_bom_version;
    public $status = 'draft';

    // Work Center Form fields
    public $wc_id;
    public $wc_code;
    public $wc_name;
    public $wc_cost_per_hour = 0;
    public $wc_capacity = 1;
    public $wc_status = 'active';

    public function mount()
    {
        $this->resetInputFields();
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->search = '';
        $this->statusFilter = '';
        $this->resetPage();
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
        // ECO
        $this->eco_id = null;
        $this->eco_number = $this->generateEcoNumber();
        $this->product_id = '';
        $this->bom_id = '';
        $this->title = '';
        $this->description = '';
        $this->reason = 'Cost Optimization';
        $this->old_bom_version = '';
        $this->new_bom_version = '';
        $this->status = 'draft';

        // Work Center
        $this->wc_id = null;
        $this->wc_code = 'WC-' . sprintf('%02d', WorkCenter::count() + 1);
        $this->wc_name = '';
        $this->wc_cost_per_hour = 0;
        $this->wc_capacity = 1;
        $this->wc_status = 'active';

        $this->isEditMode = false;
        $this->confirmingDeletion = false;
        $this->resetValidation();
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function updatedProductId($value)
    {
        if ($value) {
            $bom = Bom::where('product_id', $value)->first();
            if ($bom) {
                $this->bom_id = $bom->id;
                $this->old_bom_version = $bom->bom_version ?: '1.0';
                $this->new_bom_version = $this->incrementVersion($this->old_bom_version);
            } else {
                $this->bom_id = '';
                $this->old_bom_version = '1.0';
                $this->new_bom_version = '1.1';
            }
        }
    }

    public function updatedBomId($value)
    {
        if ($value) {
            $bom = Bom::find($value);
            if ($bom) {
                $this->product_id = $bom->product_id;
                $this->old_bom_version = $bom->bom_version ?: '1.0';
                $this->new_bom_version = $this->incrementVersion($this->old_bom_version);
            }
        }
    }

    private function incrementVersion($version): string
    {
        if (preg_match('/^(\d+)\.(\d+)$/', (string)$version, $matches)) {
            return $matches[1] . '.' . (((int)$matches[2]) + 1);
        }
        return '1.1';
    }

    private function generateEcoNumber(): string
    {
        $prefix = 'ECO-' . date('Ymd') . '-';
        $last = EcoRequest::where('eco_number', 'like', $prefix . '%')->orderBy('id', 'desc')->first();
        if ($last && preg_match('/-(\d{4})$/', $last->eco_number, $m)) {
            $next = ((int)$m[1]) + 1;
        } else {
            $next = 1;
        }
        $candidate = $prefix . sprintf('%04d', $next);
        while (EcoRequest::where('eco_number', $candidate)->exists()) {
            $next++;
            $candidate = $prefix . sprintf('%04d', $next);
        }
        return $candidate;
    }

    public function store()
    {
        if ($this->activeTab === 'eco') {
            $this->storeEco();
        } else {
            $this->storeWorkCenter();
        }
    }

    public function storeEco()
    {
        $this->validate([
            'product_id' => 'required|exists:products,id',
            'bom_id' => 'required|exists:bom,id',
            'title' => 'required|string|max:255',
            'reason' => 'required|string|max:255',
            'new_bom_version' => 'required|string|max:50',
            'status' => 'required|in:draft,pending_review,approved,rejected,applied',
            'description' => 'nullable|string',
        ], [
            'product_id.required' => 'Pilih produk yang akan direvisi.',
            'bom_id.required' => 'BOM resep produk wajib ada.',
            'title.required' => 'Judul perubahan ECO wajib diisi.',
            'new_bom_version.required' => 'Versi BOM baru wajib diisi.',
        ]);

        if (empty($this->eco_number)) {
            $this->eco_number = $this->generateEcoNumber();
        }

        $userId = Auth::id() ?? 1;

        $eco = EcoRequest::updateOrCreate(['id' => $this->eco_id], [
            'eco_number' => $this->eco_number,
            'bom_id' => $this->bom_id,
            'product_id' => $this->product_id,
            'title' => $this->title,
            'description' => $this->description,
            'reason' => $this->reason,
            'old_bom_version' => $this->old_bom_version ?: '1.0',
            'new_bom_version' => $this->new_bom_version,
            'requested_by' => $this->isEditMode && $this->eco_id ? (EcoRequest::find($this->eco_id)?->requested_by ?? $userId) : $userId,
            'status' => $this->status,
            'approved_by' => in_array($this->status, ['approved', 'applied']) ? $userId : null,
            'approved_at' => in_array($this->status, ['approved', 'applied']) ? now() : null,
        ]);

        // If applied, update the target BOM version
        if ($this->status === 'applied') {
            $bom = Bom::find($this->bom_id);
            if ($bom && $this->new_bom_version) {
                $bom->bom_version = $this->new_bom_version;
                $bom->save();
            }
        }

        ActivityLog::create([
            'user_id' => $userId,
            'module' => 'PLM',
            'action' => $this->isEditMode ? 'Update ECO' : 'Create ECO',
            'description' => 'Engineering Change Order ' . $eco->eco_number . ' (' . $eco->title . ') saved with status ' . $this->status
        ]);

        session()->flash('success', 'Engineering Change Order (ECO) berhasil disimpan.');
        $this->closeModal();
    }

    public function storeWorkCenter()
    {
        $this->validate([
            'wc_code' => 'required|string|max:50',
            'wc_name' => 'required|string|max:255',
            'wc_cost_per_hour' => 'required|numeric|min:0',
            'wc_capacity' => 'required|numeric|min:0.01',
            'wc_status' => 'required|in:active,inactive',
        ], [
            'wc_code.required' => 'Kode stasiun kerja wajib diisi.',
            'wc_name.required' => 'Nama stasiun kerja wajib diisi.',
        ]);

        WorkCenter::updateOrCreate(['id' => $this->wc_id], [
            'code' => $this->wc_code,
            'name' => $this->wc_name,
            'cost_per_hour' => floatval($this->wc_cost_per_hour),
            'capacity' => floatval($this->wc_capacity),
            'status' => $this->wc_status,
        ]);

        ActivityLog::create([
            'user_id' => Auth::id() ?? 1,
            'module' => 'PLM',
            'action' => $this->isEditMode ? 'Update Work Center' : 'Create Work Center',
            'description' => 'Work Center ' . $this->wc_name . ' has been saved.'
        ]);

        session()->flash('success', 'Stasiun Kerja (Work Center) berhasil disimpan.');
        $this->closeModal();
    }

    public function editEco($id)
    {
        $eco = EcoRequest::findOrFail($id);
        $this->eco_id = $eco->id;
        $this->eco_number = $eco->eco_number;
        $this->product_id = $eco->product_id;
        $this->bom_id = $eco->bom_id;
        $this->title = $eco->title;
        $this->description = $eco->description;
        $this->reason = $eco->reason;
        $this->old_bom_version = $eco->old_bom_version;
        $this->new_bom_version = $eco->new_bom_version;
        $this->status = $eco->status;
        $this->isEditMode = true;
        $this->openModal();
    }

    public function editWorkCenter($id)
    {
        $wc = WorkCenter::findOrFail($id);
        $this->wc_id = $wc->id;
        $this->wc_code = $wc->code;
        $this->wc_name = $wc->name;
        $this->wc_cost_per_hour = $wc->cost_per_hour;
        $this->wc_capacity = $wc->capacity;
        $this->wc_status = $wc->status ?? 'active';
        $this->isEditMode = true;
        $this->openModal();
    }

    public function confirmDelete($type, $id)
    {
        $this->deleteType = $type;
        $this->deleteId = $id;
        $this->confirmingDeletion = true;
    }

    public function delete()
    {
        if ($this->deleteType === 'eco' && $this->deleteId) {
            $eco = EcoRequest::find($this->deleteId);
            if ($eco) {
                $eco->delete();
                session()->flash('success', 'ECO berhasil dihapus.');
            }
        } elseif ($this->deleteType === 'wc' && $this->deleteId) {
            $wc = WorkCenter::find($this->deleteId);
            if ($wc) {
                $wc->delete();
                session()->flash('success', 'Work Center berhasil dihapus.');
            }
        }

        $this->confirmingDeletion = false;
        $this->deleteType = null;
        $this->deleteId = null;
    }

    public function changeEcoStatus($id, $newStatus)
    {
        $eco = EcoRequest::findOrFail($id);
        $eco->status = $newStatus;
        if (in_array($newStatus, ['approved', 'applied'])) {
            $eco->approved_by = Auth::id() ?? 1;
            $eco->approved_at = now();
        }
        if ($newStatus === 'applied') {
            $bom = Bom::find($eco->bom_id);
            if ($bom && $eco->new_bom_version) {
                $bom->bom_version = $eco->new_bom_version;
                $bom->save();
            }
        }
        $eco->save();
        session()->flash('success', 'Status ECO berhasil diperbarui ke ' . ucfirst(str_replace('_', ' ', $newStatus)));
    }

    public function render()
    {
        $ecoQuery = EcoRequest::with(['product', 'bom', 'requester', 'approver']);
        if ($this->search) {
            $ecoQuery->where(function ($q) {
                $q->where('eco_number', 'like', '%' . $this->search . '%')
                  ->orWhere('title', 'like', '%' . $this->search . '%')
                  ->orWhere('reason', 'like', '%' . $this->search . '%')
                  ->orWhereHas('product', function ($p) {
                      $p->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }
        if ($this->statusFilter) {
            $ecoQuery->where('status', $this->statusFilter);
        }

        $wcQuery = WorkCenter::query();
        if ($this->search) {
            $wcQuery->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('code', 'like', '%' . $this->search . '%');
            });
        }

        return view('livewire.plm-manager', [
            'ecos' => $ecoQuery->latest()->paginate(10, ['*'], 'ecoPage'),
            'workCenters' => $wcQuery->latest()->paginate(10, ['*'], 'wcPage'),
            'products' => Product::orderBy('name')->get(),
            'boms' => Bom::with('product')->get(),
            'totalEco' => EcoRequest::count(),
            'pendingEco' => EcoRequest::where('status', 'pending_review')->count(),
            'appliedEco' => EcoRequest::where('status', 'applied')->count(),
            'totalWc' => WorkCenter::count(),
        ])->layout('layouts.app');
    }
}
