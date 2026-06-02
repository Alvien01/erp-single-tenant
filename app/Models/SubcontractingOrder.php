<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubcontractingOrder extends Model
{

    protected $fillable = ['subcontract_number', 'supplier_id', 'product_id', 'bom_id', 'quantity', 'send_date', 'expected_return_date', 'actual_return_date', 'cost_per_unit', 'total_cost', 'status', 'notes'];
    protected $casts = ['send_date' => 'date', 'expected_return_date' => 'date', 'actual_return_date' => 'date'];

    public function supplier(): BelongsTo { return $this->belongsTo(Supplier::class); }
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function bom(): BelongsTo { return $this->belongsTo(Bom::class); }
}
