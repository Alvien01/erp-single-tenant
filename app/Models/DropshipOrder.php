<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DropshipOrder extends Model
{

    protected $fillable = ['dropship_number', 'sale_id', 'supplier_id', 'customer_id', 'status', 'tracking_number', 'carrier_id', 'notes'];

    public function sale(): BelongsTo { return $this->belongsTo(Sale::class); }
    public function supplier(): BelongsTo { return $this->belongsTo(Supplier::class); }
    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function carrier(): BelongsTo { return $this->belongsTo(DeliveryCarrier::class, 'carrier_id'); }
    public function items(): HasMany { return $this->hasMany(DropshipOrderItem::class); }
}
