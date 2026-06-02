<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HppCalculation extends Model
{

    protected $fillable = [
        'nama_barang',
        'harga_barang',
        'ongkir_supplier_to_forwarder',
        'tax_refund',
        'ongkir_china_to_indonesia',
        'pajak_impor',
        'margin',
        'total_hpp',
    ];
}
