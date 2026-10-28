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

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'planned' => 'bg-gray-50 text-gray-600 border-gray-100',
            'in_progress' => 'bg-amber-50 text-amber-700 border-amber-100',
            'completed' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
            'canceled' => 'bg-red-50 text-red-700 border-red-100',
            default => 'bg-gray-50 text-gray-600 border-gray-100',
        };
    }
}
