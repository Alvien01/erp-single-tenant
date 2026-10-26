<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkCenter extends Model
{
    protected $guarded = [];

    protected $casts = [
        'cost_per_hour' => 'decimal:2',
        'capacity' => 'decimal:2',
    ];
}
