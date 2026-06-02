<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DropshipOrderItem extends Model
{

    protected $fillable = ['dropship_order_id', 'product_id', 'qty', 'unit_price', 'subtotal'];

    public function dropshipOrder(): BelongsTo { return $this->belongsTo(DropshipOrder::class); }
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
}
