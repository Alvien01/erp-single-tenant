<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'employee_id',
        'date',
        'check_in',
        'check_out',
        'check_in_latitude',
        'check_in_longitude',
        'check_out_latitude',
        'check_out_longitude',
        'check_in_distance',
        'check_out_distance',
        'check_in_address',
        'check_out_address',
        'status',
        'notes',
    ];

    protected $casts = [
        'check_in_latitude' => 'float',
        'check_in_longitude' => 'float',
        'check_out_latitude' => 'float',
        'check_out_longitude' => 'float',
        'check_in_distance' => 'float',
        'check_out_distance' => 'float',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
