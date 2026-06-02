<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandedCost extends Model
{

    protected $fillable = [
        'landed_cost_number',
        'description',
        'total_amount',
        'purchase_id',
        'status',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
}
