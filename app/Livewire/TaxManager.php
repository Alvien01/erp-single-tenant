<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Tax;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class TaxManager extends Component
{
    use WithPagination;

    public $search = '';
    public $isOpen = false;
    public $modalType = ''; // 'tax'

    // Form fields
    public $tax_id;
    public $name;
    public $rate = 0;
    public $type = 'sales';
    public $is_active = true;

    public function openModal($type)
    {
        $this->modalType = $type;
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->modalType = '';
        $this->resetTaxFields();
    }

    public function resetTaxFields()
    {
        $this->tax_id = null;
        $this->name = '';
        $this->rate = 0;
        $this->type = 'sales';
        $this->is_active = true;
    }

    public function createTax()
    {
        $this->resetTaxFields();
        $this->openModal('tax');
    }

    public function editTax($id)
    {
        $tax = Tax::query()->findOrFail($id);
        $this->tax_id = $tax->id;
        $this->name = $tax->name;
        $this->rate = $tax->rate;
        $this->type = $tax->type;
        $this->is_active = $tax->is_active;

        $this->openModal('tax');
    }

    public function saveTax()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0|max:100',
            'type' => 'required|string',
        ]);

        Tax::query()->updateOrCreate(
            ['id' => $this->tax_id],
            [
                'name' => $this->name,
                'rate' => $this->rate,
                'type' => $this->type,
                'is_active' => $this->is_active,
            ]
        );

        ActivityLog::query()->create([
            'user_id' => Auth::id() ?? 1,
            'module' => 'Tax Management',
            'action' => $this->tax_id ? 'Update Tax' : 'Create Tax',
            'description' => 'Saved tax rule: ' . $this->name . ' with rate ' . $this->rate . '%'
        ]);

        session()->flash('success', 'Tax rule saved successfully.');
        $this->closeModal();
    }

    public function deleteTax($id)
    {
        Tax::query()->findOrFail($id)->delete();
        session()->flash('success', 'Tax rule deleted successfully.');
    }

    public function toggleTaxStatus($id)
    {
        $tax = Tax::query()->findOrFail($id);
        $tax->is_active = !$tax->is_active;
        $tax->save();

        session()->flash('success', 'Tax status updated.');
    }

    public function render()
    {
        $query = Tax::query();

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('type', 'like', '%' . $this->search . '%');
        }

        return view('livewire.tax-manager', [
            'taxes' => $query->orderBy('name')->paginate(10)
        ]);
    }
}
