<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Store;
use App\Models\Warehouse;

class StoreManager extends Component
{
    public $search = '';
    public $showModal = false;
    public $editingId = null;

    public $code = '';
    public $name = '';
    public $address = '';
    public $phone = '';
    public $email = '';
    public $tax_rate = 11;
    public $service_charge_rate = 0;
    public $warehouse_id = '';
    public $receipt_header = '';
    public $receipt_footer = 'Terima Kasih Atas Kunjungan Anda!';
    public $is_active = true;

    public function openCreate()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEdit($id)
    {
        $s = Store::findOrFail($id);
        $this->editingId = $s->id;
        $this->code = $s->code;
        $this->name = $s->name;
        $this->address = $s->address;
        $this->phone = $s->phone;
        $this->email = $s->email;
        $this->tax_rate = $s->tax_rate;
        $this->service_charge_rate = $s->service_charge_rate;
        $this->warehouse_id = $s->warehouse_id;
        $this->receipt_header = $s->receipt_header;
        $this->receipt_footer = $s->receipt_footer;
        $this->is_active = $s->is_active;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'code' => 'required|string|max:20',
            'name' => 'required|string|max:255',
        ]);

        $data = [
            'code' => strtoupper($this->code),
            'name' => $this->name,
            'address' => $this->address,
            'phone' => $this->phone,
            'email' => $this->email,
            'tax_rate' => $this->tax_rate,
            'service_charge_rate' => $this->service_charge_rate,
            'warehouse_id' => $this->warehouse_id ?: null,
            'receipt_header' => $this->receipt_header,
            'receipt_footer' => $this->receipt_footer,
            'is_active' => $this->is_active,
        ];

        if ($this->editingId) {
            Store::find($this->editingId)->update($data);
            session()->flash('success', 'Store berhasil diperbarui!');
        } else {
            Store::create($data);
            session()->flash('success', 'Store baru berhasil dibuat!');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function delete($id)
    {
        Store::findOrFail($id)->delete();
        session()->flash('success', 'Store dihapus.');
    }

    public function toggleActive($id)
    {
        $s = Store::findOrFail($id);
        $s->update(['is_active' => !$s->is_active]);
    }

    private function resetForm()
    {
        $this->editingId = null;
        $this->code = '';
        $this->name = '';
        $this->address = '';
        $this->phone = '';
        $this->email = '';
        $this->tax_rate = 11;
        $this->service_charge_rate = 0;
        $this->warehouse_id = '';
        $this->receipt_header = '';
        $this->receipt_footer = 'Terima Kasih Atas Kunjungan Anda!';
        $this->is_active = true;
    }

    public function render()
    {
        $stores = Store::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%")
                ->orWhere('code', 'like', "%{$this->search}%"))
            ->latest()->paginate(15);

        return view('livewire.store-manager', [
            'stores' => $stores,
            'warehouses' => Warehouse::all(),
        ]);
    }
}
