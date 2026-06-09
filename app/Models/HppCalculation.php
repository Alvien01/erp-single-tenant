<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HppCalculation extends Model
{

    protected $fillable = [
        'nama_barang',
        'persediaan_awal',
        'pembelian_bersih',
        'persediaan_akhir',
        'total_hpp',
    ];
}
