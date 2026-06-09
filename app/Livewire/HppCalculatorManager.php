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
    public $persediaan_awal = 0;
    public $pembelian_bersih = 0;
    public $persediaan_akhir = 0;
    public $total_hpp = 0;
    public $selected_product_id = '';

    protected $rules = [
        'nama_barang' => 'required|string|max:255',
        'persediaan_awal' => 'required|integer|min:0',
        'pembelian_bersih' => 'required|integer|min:0',
        'persediaan_akhir' => 'required|integer|min:0',
    ];

    public function mount()
    {
        $this->resetInputFields();
    }

    public function resetInputFields()
    {
        $this->calculation_id = null;
        $this->nama_barang = '';
        $this->persediaan_awal = 0;
        $this->pembelian_bersih = 0;
        $this->persediaan_akhir = 0;
        $this->total_hpp = 0;
        $this->selected_product_id = '';
        $this->isEditMode = false;
        $this->resetValidation();
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['persediaan_awal', 'pembelian_bersih', 'persediaan_akhir'])) {
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
                $this->pembelian_bersih = (int)$product->price;
                $this->calculateHpp();
            }
        }
    }

    public function calculateHpp()
    {
        $this->total_hpp = (int)$this->persediaan_awal 
            + (int)$this->pembelian_bersih 
            - (int)$this->persediaan_akhir;
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
        $this->persediaan_awal = (int)$calc->persediaan_awal;
        $this->pembelian_bersih = (int)$calc->pembelian_bersih;
        $this->persediaan_akhir = (int)$calc->persediaan_akhir;
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
                'persediaan_awal' => (int)$this->persediaan_awal,
                'pembelian_bersih' => (int)$this->pembelian_bersih,
                'persediaan_akhir' => (int)$this->persediaan_akhir,
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
        $this->persediaan_awal = (int)$calc->persediaan_awal;
        $this->pembelian_bersih = (int)$calc->pembelian_bersih;
        $this->persediaan_akhir = (int)$calc->persediaan_akhir;
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
