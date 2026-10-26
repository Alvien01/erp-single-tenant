<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesQuotationItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'qty' => 'decimal:2',
        'price' => 'decimal:2',
        'discount' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function salesQuotation(): BelongsTo
    {
        return $this->belongsTo(SalesQuotation::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
