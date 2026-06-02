<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FleetFuelLog extends Model
{

    protected $fillable = [
        'fleet_id',
        'date',
        'liters',
        'cost_per_liter',
        'total_cost',
        'odometer',
    ];

    protected $casts = [
        'date' => 'date',
        'liters' => 'decimal:2',
        'cost_per_liter' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'odometer' => 'decimal:2',
    ];

    public function fleet()
    {
        return $this->belongsTo(Fleet::class);
    }
}
