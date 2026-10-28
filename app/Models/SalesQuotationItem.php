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

    public function getDisplayNameAttribute(): string
    {
        return !empty($this->product_name) ? $this->product_name : ($this->product->name ?? '-');
    }

    public function getDisplayModelAttribute(): string
    {
        return !empty($this->model) ? $this->model : ($this->product->sku ?? ($this->product->unit ?? '-'));
    }

    public function getCalculatedSubtotalAttribute(): float
    {
        return (is_numeric($this->subtotal) && floatval($this->subtotal) > 0)
            ? floatval($this->subtotal)
            : ((float) $this->qty * (float) $this->price);
    }
}
