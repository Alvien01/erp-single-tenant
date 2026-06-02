<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FsmPartUsed extends Model
{

    protected $fillable = ['field_service_order_id', 'product_id', 'qty', 'unit_price', 'subtotal'];

    public function fieldServiceOrder(): BelongsTo { return $this->belongsTo(FieldServiceOrder::class); }
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
}
