<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FleetService extends Model
{

    protected $fillable = [
        'fleet_id',
        'service_date',
        'description',
        'cost',
        'provider',
        'status',
    ];

    protected $casts = [
        'service_date' => 'date',
        'cost' => 'decimal:2',
    ];

    public function fleet()
    {
        return $this->belongsTo(Fleet::class);
    }
}
