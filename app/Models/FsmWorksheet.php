<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FsmWorksheet extends Model
{

    protected $fillable = ['field_service_order_id', 'work_performed', 'materials_used', 'labor_hours', 'labor_cost', 'material_cost', 'technician_notes', 'customer_feedback'];

    public function fieldServiceOrder(): BelongsTo { return $this->belongsTo(FieldServiceOrder::class); }
}
