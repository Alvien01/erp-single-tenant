<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoodReceiptItem extends Model
{
    protected $fillable = [
        'good_receipt_id',
        'product_id',
        'warehouse_id',
        'qty_received',
        'unit_price',
        'subtotal',
    ];

    public function goodReceipt(): BelongsTo
    {
        return $this->belongsTo(GoodReceipt::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }
}
