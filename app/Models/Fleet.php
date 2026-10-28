<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fleet extends Model
{

    protected $fillable = [
        'license_plate',
        'model',
        'driver_id',
        'status',
        'odometer',
        'acquisition_date',
    ];

    protected $casts = [
        'acquisition_date' => 'date',
        'odometer' => 'decimal:2',
    ];

    public function driver()
    {
        return $this->belongsTo(Employee::class, 'driver_id');
    }

    public function services()
    {
        return $this->hasMany(FleetService::class);
    }

    public function fuelLogs()
    {
        return $this->hasMany(FleetFuelLog::class);
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'active' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
            'in_service' => 'bg-amber-50 text-amber-700 border-amber-100',
            'out_of_service' => 'bg-red-50 text-red-700 border-red-100',
            'sold' => 'bg-gray-50 text-gray-600 border-gray-100',
            default => 'bg-gray-50 text-gray-600 border-gray-100',
        };
    }
}
