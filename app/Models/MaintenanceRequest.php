<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceRequest extends Model
{

    protected $fillable = [
        'asset_id',
        'asset_name',
        'description',
        'request_date',
        'repair_date',
        'cost',
        'status',
        'priority',
    ];

    protected $casts = [
        'request_date' => 'date',
        'repair_date' => 'date',
        'cost' => 'decimal:2',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}
