<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\QualityCheckpoint;
use App\Models\QualityCheck;
use App\Models\Product;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class QualityControlManager extends Component
{
    use WithPagination;

    public $search = '';
    public $activeTab = 'checkpoints';
    public $isOpen = false;
    public $modalType = ''; // 'checkpoint', 'check'

    // Checkpoint Form Fields
    public $checkpoint_id;
    public $product_id;
    public $test_name;
    public $criteria;

    // Check Form Fields
    public $quality_checkpoint_id;
    public $reference_type = 'GoodReceipt';
    public $reference_id;
    public $status = 'passed';
    public $notes;

    public function openModal($type)
    {
        $this->modalType = $type;
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->modalType = '';
        $this->resetCheckpointFields();
        $this->resetCheckFields();
    }

    public function resetCheckpointFields()
    {
        $this->checkpoint_id = null;
        $this->product_id = null;
        $this->test_name = '';
        $this->criteria = '';
    }

    public function resetCheckFields()
    {
        $this->quality_checkpoint_id = null;
        $this->reference_type = 'GoodReceipt';
        $this->reference_id = null;
        $this->status = 'passed';
        $this->notes = '';
    }

    public function createCheckpoint()
    {
        $this->resetCheckpointFields();
        $this->openModal('checkpoint');
    }

    public function editCheckpoint($id)
    {
        $cp = QualityCheckpoint::query()->findOrFail($id);
        $this->checkpoint_id = $cp->id;
        $this->product_id = $cp->product_id;
        $this->test_name = $cp->test_name;
        $this->criteria = $cp->criteria;

        $this->openModal('checkpoint');
    }

    public function saveCheckpoint()
    {
        $this->validate([
            'product_id' => 'required|exists:products,id',
            'test_name' => 'required|string|max:255',
            'criteria' => 'required|string|max:1000',
        ]);

        QualityCheckpoint::query()->updateOrCreate(
            ['id' => $this->checkpoint_id],
            [
                'product_id' => $this->product_id,
                'test_name' => $this->test_name,
                'criteria' => $this->criteria,
            ]
        );

        ActivityLog::query()->create([
            'user_id' => Auth::id() ?? 1,
            'module' => 'Quality Control',
            'action' => $this->checkpoint_id ? 'Update Checkpoint' : 'Create Checkpoint',
            'description' => 'Saved quality checkpoint for product ID ' . $this->product_id
        ]);

        session()->flash('success', 'Quality checkpoint saved successfully.');
        $this->closeModal();
    }

    public function deleteCheckpoint($id)
    {
        QualityCheckpoint::query()->findOrFail($id)->delete();
        session()->flash('success', 'Checkpoint deleted.');
    }

    public function recordCheck($checkpointId)
    {
        $this->resetCheckFields();
        $this->quality_checkpoint_id = $checkpointId;
        $this->openModal('check');
    }

    public function saveCheck()
    {
        $this->validate([
            'quality_checkpoint_id' => 'required|exists:quality_checkpoints,id',
            'reference_type' => 'required|string',
            'reference_id' => 'required|numeric|min:1',
            'status' => 'required|string',
            'notes' => 'nullable|string|max:500',
        ]);

        QualityCheck::query()->create([
            'quality_checkpoint_id' => $this->quality_checkpoint_id,
            'reference_type' => $this->reference_type,
            'reference_id' => $this->reference_id,
            'checked_by' => Auth::id() ?? 1,
            'status' => $this->status,
            'notes' => $this->notes,
            'checked_at' => now(),
        ]);

        ActivityLog::query()->create([
            'user_id' => Auth::id() ?? 1,
            'module' => 'Quality Control',
            'action' => 'Log QC Test Run',
            'description' => 'Recorded check result ' . $this->status . ' for checkpoint ' . $this->quality_checkpoint_id
        ]);

        session()->flash('success', 'Quality check logged successfully.');
        $this->closeModal();
    }

    public function deleteCheck($id)
    {
        QualityCheck::query()->findOrFail($id)->delete();
        session()->flash('success', 'Quality check entry deleted.');
    }

    public function render()
    {
        $cpQuery = QualityCheckpoint::with('product');
        if ($this->search) {
            $cpQuery->whereHas('product', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            })->orWhere('test_name', 'like', '%' . $this->search . '%');
        }

        $checkQuery = QualityCheck::with(['checkpoint.product', 'checker'])->orderBy('checked_at', 'desc');

        return view('livewire.quality-control-manager', [
            'checkpoints' => $cpQuery->paginate(10),
            'checks' => $checkQuery->paginate(10),
            'products' => Product::all(),
        ]);
    }
}
