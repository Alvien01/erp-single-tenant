<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{

    protected $guarded = [];

    protected $casts = [
        'image' => 'array',
        'price' => 'decimal:2',
        'stock' => 'decimal:2',
        'min_stock' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }
}
