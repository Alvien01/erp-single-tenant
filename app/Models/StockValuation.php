<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockValuation extends Model
{

    protected $fillable = [
        'product_id',
        'quantity',
        'unit_cost',
        'total_value',
        'type',
        'reference_type',
        'reference_id',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
