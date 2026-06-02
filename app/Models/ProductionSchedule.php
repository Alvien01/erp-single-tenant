<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionSchedule extends Model
{

    protected $fillable = ['product_id', 'period', 'forecast_demand', 'actual_demand', 'planned_production', 'actual_production', 'opening_stock', 'closing_stock', 'notes'];

    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
}
