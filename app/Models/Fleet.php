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
}
