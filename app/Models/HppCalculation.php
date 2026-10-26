<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HppCalculation extends Model
{

    protected $fillable = [
        'nama_barang',
        'calculation_type',
        'persediaan_awal',
        'pembelian_bersih',
        'persediaan_akhir',
        'harga_barang',
        'ongkir_forwarder',
        'tax_refund',
        'ongkir_indonesia',
        'ppn_rate',
        'ppn_value',
        'pph_type',
        'pph_value',
        'keuntungan_type',
        'keuntungan_rate',
        'keuntungan_value',
        'harga_jual',
        'total_hpp',
    ];

    protected $casts = [
        'persediaan_awal' => 'decimal:2',
        'pembelian_bersih' => 'decimal:2',
        'persediaan_akhir' => 'decimal:2',
        'harga_barang' => 'decimal:2',
        'ongkir_forwarder' => 'decimal:2',
        'tax_refund' => 'decimal:2',
        'ongkir_indonesia' => 'decimal:2',
        'ppn_rate' => 'decimal:2',
        'ppn_value' => 'decimal:2',
        'pph_value' => 'decimal:2',
        'keuntungan_rate' => 'decimal:2',
        'keuntungan_value' => 'decimal:2',
        'harga_jual' => 'decimal:2',
        'total_hpp' => 'decimal:2',
    ];
}
