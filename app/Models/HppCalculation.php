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
}
