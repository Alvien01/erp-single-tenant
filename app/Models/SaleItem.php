<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{

    protected $guarded = [];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class)->withDefault([
            'name' => $this->product_name ?? '-',
            'unit' => 'pcs',
            'sku' => '-',
        ]);
    }
}
