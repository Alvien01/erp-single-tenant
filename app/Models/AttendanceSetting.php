<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceSetting extends Model
{
    protected $fillable = [
        'office_name',
        'office_latitude',
        'office_longitude',
        'allowed_radius',
        'work_start_time',
        'work_end_time',
        'late_tolerance_minutes',
        'early_checkin_minutes',
        'require_location',
        'is_active',
    ];

    protected $casts = [
        'office_latitude' => 'float',
        'office_longitude' => 'float',
        'allowed_radius' => 'integer',
        'late_tolerance_minutes' => 'integer',
        'early_checkin_minutes' => 'integer',
        'require_location' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Calculate the Haversine distance between two coordinates in meters.
     */
    public static function calculateDistance($lat1, $lon1, $lat2, $lon2): float
    {
        $earthRadius = 6371000; // meters

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 2);
    }
}
