<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnItem extends Model
{

    protected $fillable = [
        'return_id',
        'product_id',
        'qty',
        'quantity',
        'unit_price',
        'subtotal',
        'reason',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            if (!isset($item->qty) && isset($item->quantity)) {
                $item->qty = $item->quantity;
            } elseif (!isset($item->quantity) && isset($item->qty)) {
                $item->quantity = $item->qty;
            }
            if (!isset($item->unit_price)) {
                $item->unit_price = 0;
            }
            if (!isset($item->subtotal)) {
                $item->subtotal = ($item->quantity ?? $item->qty ?? 0) * ($item->unit_price ?? 0);
            }
        });
    }

    public function getQuantityAttribute($value)
    {
        return $value ?? $this->attributes['qty'] ?? 0;
    }

    public function getQtyAttribute($value)
    {
        return $value ?? $this->attributes['quantity'] ?? 0;
    }

    public function returnModel(): BelongsTo
    {
        return $this->belongsTo(ReturnModel::class, 'return_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
