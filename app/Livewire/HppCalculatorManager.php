<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\HppCalculation;
use App\Models\Product;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class HppCalculatorManager extends Component
{
    use WithPagination;

    public $search = '';
    public $isOpen = false;
    public $isEditMode = false;
    public $calculation_id;
    public $nama_barang = '';
    public $harga_barang = 0;
    public $ongkir_supplier_to_forwarder = 0;
    public $tax_refund = 0;
    public $ongkir_china_to_indonesia = 0;
    public $pajak_impor = 0;
    public $margin = 0;
    public $total_hpp = 0;
    public $selected_product_id = '';

    protected $rules = [
        'nama_barang' => 'required|string|max:255',
        'harga_barang' => 'required|integer|min:0',
        'ongkir_supplier_to_forwarder' => 'required|integer',
        'tax_refund' => 'required|integer',
        'ongkir_china_to_indonesia' => 'required|integer|min:0',
        'pajak_impor' => 'required|integer',
        'margin' => 'required|integer',
    ];

    public function mount()
    {
        $this->resetInputFields();
    }

    public function resetInputFields()
    {
        $this->calculation_id = null;
        $this->nama_barang = '';
        $this->harga_barang = 0;
        $this->ongkir_supplier_to_forwarder = 0;
        $this->tax_refund = 0;
        $this->ongkir_china_to_indonesia = 0;
        $this->pajak_impor = 0;
        $this->margin = 0;
        $this->total_hpp = 0;
        $this->selected_product_id = '';
        $this->isEditMode = false;
        $this->resetValidation();
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['harga_barang', 'ongkir_supplier_to_forwarder', 'tax_refund', 'ongkir_china_to_indonesia', 'pajak_impor', 'margin'])) {
            $this->$propertyName = $this->$propertyName === '' ? 0 : (int) $this->$propertyName;
        }

        $this->calculateHpp();
    }

    public function updatedSelectedProductId($id)
    {
        if ($id) {
            $product = Product::find($id);
            if ($product) {
                $this->nama_barang = $product->name;
                $this->harga_barang = (int)$product->price;
                $this->calculateHpp();
            }
        }
    }

    public function calculateHpp()
    {
        $this->total_hpp = (int)$this->harga_barang 
            + (int)$this->ongkir_supplier_to_forwarder 
            + (int)$this->tax_refund 
            + (int)$this->ongkir_china_to_indonesia 
            + (int)$this->pajak_impor 
            + (int)$this->margin;
    }

    public function openModal()
    {
        $this->isOpen = true;
        $this->calculateHpp();
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetInputFields();
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function loadCalculation($id)
    {
        $calc = HppCalculation::findOrFail($id);
        $this->calculation_id = $calc->id;
        $this->nama_barang = $calc->nama_barang;
        $this->harga_barang = (int)$calc->harga_barang;
        $this->ongkir_supplier_to_forwarder = (int)$calc->ongkir_supplier_to_forwarder;
        $this->tax_refund = (int)$calc->tax_refund;
        $this->ongkir_china_to_indonesia = (int)$calc->ongkir_china_to_indonesia;
        $this->pajak_impor = (int)$calc->pajak_impor;
        $this->margin = (int)$calc->margin;
        $this->total_hpp = (int)$calc->total_hpp;
        
        session()->flash('info', "Kalkulasi untuk '{$calc->nama_barang}' berhasil dimuat di panel kalkulator.");
    }

    public function store()
    {
        $this->validate();

        $this->calculateHpp();

        HppCalculation::updateOrCreate(
            ['id' => $this->calculation_id],
            [
                'nama_barang' => $this->nama_barang,
                'harga_barang' => (int)$this->harga_barang,
                'ongkir_supplier_to_forwarder' => (int)$this->ongkir_supplier_to_forwarder,
                'tax_refund' => (int)$this->tax_refund,
                'ongkir_china_to_indonesia' => (int)$this->ongkir_china_to_indonesia,
                'pajak_impor' => (int)$this->pajak_impor,
                'margin' => (int)$this->margin,
                'total_hpp' => $this->total_hpp,
            ]
        );
        try {
            ActivityLog::query()->create([
                'user_id' => Auth::id() ?? 1,
                'module' => 'HPP Calculator',
                'action' => $this->calculation_id ? 'Update HPP Calculation' : 'Create HPP Calculation',
                'description' => 'Saved HPP calculation for ' . $this->nama_barang . ' with Total HPP of Rp ' . number_format($this->total_hpp, 0, ',', '.')
            ]);
        } catch (\Exception $e) {
        }

        session()->flash('success', $this->calculation_id ? 'Kalkulasi HPP berhasil diperbarui.' : 'Kalkulasi HPP berhasil disimpan.');
        $this->closeModal();
    }

    public function edit($id)
    {
        $calc = HppCalculation::findOrFail($id);
        $this->calculation_id = $calc->id;
        $this->nama_barang = $calc->nama_barang;
        $this->harga_barang = (int)$calc->harga_barang;
        $this->ongkir_supplier_to_forwarder = (int)$calc->ongkir_supplier_to_forwarder;
        $this->tax_refund = (int)$calc->tax_refund;
        $this->ongkir_china_to_indonesia = (int)$calc->ongkir_china_to_indonesia;
        $this->pajak_impor = (int)$calc->pajak_impor;
        $this->margin = (int)$calc->margin;
        $this->total_hpp = (int)$calc->total_hpp;

        $this->isEditMode = true;
        $this->openModal();
    }

    public function delete($id)
    {
        HppCalculation::findOrFail($id)->delete();
        session()->flash('success', 'Kalkulasi HPP berhasil dihapus.');
    }

    public function render()
    {
        $query = HppCalculation::query();

        if ($this->search) {
            $query->where('nama_barang', 'like', '%' . $this->search . '%');
        }

        return view('livewire.hpp-calculator-manager', [
            'calculations' => $query->orderBy('created_at', 'desc')->paginate(10),
            'products' => Product::select('id', 'name', 'price')->orderBy('name')->get()
        ]);
    }
}
