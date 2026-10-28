<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesQuotation extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'shipping_price' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'quotation_date' => 'date',
        'valid_until' => 'date',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SalesQuotationItem::class);
    }

    public function getTaxLabelAttribute(): string
    {
        return match ($this->tax_type) {
            'pph_05' => 'PPh 0,5%',
            'ppn_12', 'ppn_11' => 'PPN 12%',
            'none' => 'Bebas Pajak (0%)',
            default => 'PPh 0,5%',
        };
    }

    public function getCalculatedItemsSubtotalAttribute(): float
    {
        return (float) $this->items->sum(function ($item) {
            return $item->calculated_subtotal;
        });
    }
}
