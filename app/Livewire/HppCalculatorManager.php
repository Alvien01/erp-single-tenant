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
    public $active_tab = 'umum';

    // Umum HPP Fields
    public $persediaan_awal = 0;
    public $pembelian_bersih = 0;
    public $persediaan_akhir = 0;

    // Internal HPP Fields
    public $harga_barang = 0;
    public $ongkir_forwarder = 0;
    public $tax_refund = 0;
    public $ongkir_indonesia = 0;
    public $ppn_rate = 11.0;
    public $ppn_value = 0;
    public $pph_type = 'final_0.5';
    public $pph_value = 0;
    public $keuntungan_type = 'nominal';
    public $keuntungan_rate = 0;
    public $keuntungan_value = 0;
    public $harga_jual = 0;

    public $total_hpp = 0;
    public $selected_product_id = '';

    public function rules()
    {
        if ($this->active_tab === 'internal') {
            return [
                'nama_barang' => 'required|string|max:255',
                'harga_barang' => 'required|integer|min:0',
                'ongkir_forwarder' => 'required|integer|min:0',
                'tax_refund' => 'required|integer|min:0',
                'ongkir_indonesia' => 'required|integer|min:0',
                'ppn_rate' => 'required|numeric|min:0|max:100',
                'pph_type' => 'required|string|in:final_0.5,badan_11,none',
                'keuntungan_type' => 'required|string|in:nominal,margin,markup',
                'keuntungan_rate' => 'required|numeric|min:0',
            ];
        }

        return [
            'nama_barang' => 'required|string|max:255',
            'persediaan_awal' => 'required|integer|min:0',
            'pembelian_bersih' => 'required|integer|min:0',
            'persediaan_akhir' => 'required|integer|min:0',
        ];
    }

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
        
        $this->harga_barang = 0;
        $this->ongkir_forwarder = 0;
        $this->tax_refund = 0;
        $this->ongkir_indonesia = 0;
        $this->ppn_rate = 11.0;
        $this->ppn_value = 0;
        $this->pph_type = 'final_0.5';
        $this->pph_value = 0;
        $this->keuntungan_type = 'nominal';
        $this->keuntungan_rate = 0;
        $this->keuntungan_value = 0;
        $this->harga_jual = 0;

        $this->total_hpp = 0;
        $this->selected_product_id = '';
        $this->isEditMode = false;
        $this->resetValidation();
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, [
            'persediaan_awal', 'pembelian_bersih', 'persediaan_akhir',
            'harga_barang', 'ongkir_forwarder', 'tax_refund', 'ongkir_indonesia'
        ])) {
            $this->$propertyName = $this->$propertyName === '' ? 0 : (int) $this->$propertyName;
        }

        if (in_array($propertyName, ['ppn_rate', 'keuntungan_rate'])) {
            $this->$propertyName = $this->$propertyName === '' ? 0.0 : (float) $this->$propertyName;
        }

        $this->calculateHpp();
    }

    public function updatedSelectedProductId($id)
    {
        if ($id) {
            $product = Product::find($id);
            if ($product) {
                $this->nama_barang = $product->name;
                if ($this->active_tab === 'internal') {
                    $this->harga_barang = (int)$product->price;
                } else {
                    $this->pembelian_bersih = (int)$product->price;
                }
                $this->calculateHpp();
            }
        }
    }

    public function setActiveTab($tab)
    {
        $this->active_tab = $tab;
        $this->calculateHpp();
    }

    public function calculateHpp()
    {
        if ($this->active_tab === 'internal') {
            $harga = (int)$this->harga_barang;
            $ongkir_fwd = (int)$this->ongkir_forwarder;
            $refund = (int)$this->tax_refund;
            $ongkir_indo = (int)$this->ongkir_indonesia;
            $rate_ppn = (float)$this->ppn_rate;
            
            // Subtotal cost (Note: Tax refund reduces cost, so we subtract it)
            $subtotal = $harga + $ongkir_fwd - $refund + $ongkir_indo;
            $this->ppn_value = (int)round($subtotal * ($rate_ppn / 100));
            
            $cost_with_ppn = $subtotal + $this->ppn_value;
            
            $rate_keuntungan = (float)$this->keuntungan_rate;
            $type_keuntungan = $this->keuntungan_type;
            $type_pph = $this->pph_type;
            
            if ($type_keuntungan === 'nominal') {
                $keuntungan_nominal = $rate_keuntungan;
                if ($type_pph === 'final_0.5') {
                    $harga_jual = ($cost_with_ppn + $keuntungan_nominal) / 0.995;
                    $pph_nominal = $harga_jual * 0.005;
                } elseif ($type_pph === 'badan_11') {
                    $pph_nominal = $keuntungan_nominal * 0.11;
                    $harga_jual = $cost_with_ppn + $keuntungan_nominal + $pph_nominal;
                } else {
                    $pph_nominal = 0;
                    $harga_jual = $cost_with_ppn + $keuntungan_nominal;
                }
            } elseif ($type_keuntungan === 'margin') {
                $m = $rate_keuntungan / 100;
                if ($m >= 0.995 && $type_pph === 'final_0.5') {
                    $m = 0.99;
                }
                if ($type_pph === 'final_0.5') {
                    $divider = 0.995 - $m;
                    $harga_jual = $divider > 0 ? ($cost_with_ppn / $divider) : $cost_with_ppn;
                    $pph_nominal = $harga_jual * 0.005;
                    $keuntungan_nominal = $harga_jual * $m;
                } elseif ($type_pph === 'badan_11') {
                    $divider = 1 - (1.11 * $m);
                    $harga_jual = $divider > 0 ? ($cost_with_ppn / $divider) : $cost_with_ppn;
                    $keuntungan_nominal = $harga_jual * $m;
                    $pph_nominal = $keuntungan_nominal * 0.11;
                } else {
                    $divider = 1 - $m;
                    $harga_jual = $divider > 0 ? ($cost_with_ppn / $divider) : $cost_with_ppn;
                    $keuntungan_nominal = $harga_jual * $m;
                    $pph_nominal = 0;
                }
            } else { // markup
                $r = $rate_keuntungan / 100;
                $keuntungan_nominal = $cost_with_ppn * $r;
                if ($type_pph === 'final_0.5') {
                    $harga_jual = ($cost_with_ppn + $keuntungan_nominal) / 0.995;
                    $pph_nominal = $harga_jual * 0.005;
                } elseif ($type_pph === 'badan_11') {
                    $pph_nominal = $keuntungan_nominal * 0.11;
                    $harga_jual = $cost_with_ppn + $keuntungan_nominal + $pph_nominal;
                } else {
                    $pph_nominal = 0;
                    $harga_jual = $cost_with_ppn + $keuntungan_nominal;
                }
            }
            
            $this->keuntungan_value = (int)round($keuntungan_nominal);
            $this->pph_value = (int)round($pph_nominal);
            $this->harga_jual = (int)round($harga_jual);
            $this->total_hpp = $cost_with_ppn + $this->pph_value;
        } else {
            $this->total_hpp = (int)$this->persediaan_awal 
                + (int)$this->pembelian_bersih 
                - (int)$this->persediaan_akhir;
        }
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
        $this->active_tab = $calc->calculation_type ?? 'umum';
        
        if ($this->active_tab === 'internal') {
            $this->harga_barang = (int)$calc->harga_barang;
            $this->ongkir_forwarder = (int)$calc->ongkir_forwarder;
            $this->tax_refund = (int)$calc->tax_refund;
            $this->ongkir_indonesia = (int)$calc->ongkir_indonesia;
            $this->ppn_rate = (float)($calc->ppn_rate ?? 11.0);
            $this->ppn_value = (int)$calc->ppn_value;
            $this->pph_type = $calc->pph_type ?? 'final_0.5';
            $this->pph_value = (int)$calc->pph_value;
            $this->keuntungan_type = $calc->keuntungan_type ?? 'nominal';
            $this->keuntungan_rate = (float)$calc->keuntungan_rate;
            $this->keuntungan_value = (int)$calc->keuntungan_value;
            $this->harga_jual = (int)$calc->harga_jual;
        } else {
            $this->persediaan_awal = (int)$calc->persediaan_awal;
            $this->pembelian_bersih = (int)$calc->pembelian_bersih;
            $this->persediaan_akhir = (int)$calc->persediaan_akhir;
        }
        $this->total_hpp = (int)$calc->total_hpp;
        
        session()->flash('info', "Kalkulasi untuk '{$calc->nama_barang}' berhasil dimuat di panel kalkulator.");
    }

    public function store()
    {
        $this->validate();

        $this->calculateHpp();

        $data = [
            'nama_barang' => $this->nama_barang,
            'calculation_type' => $this->active_tab,
            'total_hpp' => $this->total_hpp,
        ];

        if ($this->active_tab === 'internal') {
            $data['harga_barang'] = (int)$this->harga_barang;
            $data['ongkir_forwarder'] = (int)$this->ongkir_forwarder;
            $data['tax_refund'] = (int)$this->tax_refund;
            $data['ongkir_indonesia'] = (int)$this->ongkir_indonesia;
            $data['ppn_rate'] = (float)$this->ppn_rate;
            $data['ppn_value'] = (int)$this->ppn_value;
            $data['pph_type'] = $this->pph_type;
            $data['pph_value'] = (int)$this->pph_value;
            $data['keuntungan_type'] = $this->keuntungan_type;
            $data['keuntungan_rate'] = (float)$this->keuntungan_rate;
            $data['keuntungan_value'] = (int)$this->keuntungan_value;
            $data['harga_jual'] = (int)$this->harga_jual;
            
            $data['persediaan_awal'] = null;
            $data['pembelian_bersih'] = null;
            $data['persediaan_akhir'] = null;
        } else {
            $data['persediaan_awal'] = (int)$this->persediaan_awal;
            $data['pembelian_bersih'] = (int)$this->pembelian_bersih;
            $data['persediaan_akhir'] = (int)$this->persediaan_akhir;
            
            $data['harga_barang'] = null;
            $data['ongkir_forwarder'] = null;
            $data['tax_refund'] = null;
            $data['ongkir_indonesia'] = null;
            $data['ppn_rate'] = null;
            $data['ppn_value'] = null;
            $data['pph_type'] = null;
            $data['pph_value'] = null;
            $data['keuntungan_type'] = null;
            $data['keuntungan_rate'] = null;
            $data['keuntungan_value'] = null;
            $data['harga_jual'] = null;
        }

        HppCalculation::updateOrCreate(
            ['id' => $this->calculation_id],
            $data
        );

        try {
            ActivityLog::query()->create([
                'user_id' => Auth::id() ?? 1,
                'module' => 'HPP Calculator',
                'action' => $this->calculation_id ? 'Update HPP Calculation' : 'Create HPP Calculation',
                'description' => 'Saved HPP calculation (' . ($this->active_tab === 'internal' ? 'Internal' : 'Umum') . ') for ' . $this->nama_barang . ' with Total HPP of Rp ' . number_format($this->total_hpp, 0, ',', '.')
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
        $this->active_tab = $calc->calculation_type ?? 'umum';
        
        if ($this->active_tab === 'internal') {
            $this->harga_barang = (int)$calc->harga_barang;
            $this->ongkir_forwarder = (int)$calc->ongkir_forwarder;
            $this->tax_refund = (int)$calc->tax_refund;
            $this->ongkir_indonesia = (int)$calc->ongkir_indonesia;
            $this->ppn_rate = (float)($calc->ppn_rate ?? 11.0);
            $this->ppn_value = (int)$calc->ppn_value;
            $this->pph_type = $calc->pph_type ?? 'final_0.5';
            $this->pph_value = (int)$calc->pph_value;
            $this->keuntungan_type = $calc->keuntungan_type ?? 'nominal';
            $this->keuntungan_rate = (float)$calc->keuntungan_rate;
            $this->keuntungan_value = (int)$calc->keuntungan_value;
            $this->harga_jual = (int)$calc->harga_jual;
        } else {
            $this->persediaan_awal = (int)$calc->persediaan_awal;
            $this->pembelian_bersih = (int)$calc->pembelian_bersih;
            $this->persediaan_akhir = (int)$calc->persediaan_akhir;
        }
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
