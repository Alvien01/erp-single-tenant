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

    public function getPriorityBadgeClassAttribute(): string
    {
        return match($this->priority) {
            'low' => 'bg-gray-50 text-gray-700 border-gray-200',
            'medium' => 'bg-blue-50 text-blue-700 border-blue-100',
            'high' => 'bg-red-50 text-red-700 border-red-100',
            default => 'bg-gray-50 text-gray-700 border-gray-200',
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'requested' => 'bg-gray-50 text-gray-600 border-gray-100',
            'in_progress' => 'bg-amber-50 text-amber-700 border-amber-100',
            'repaired' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
            'scrap' => 'bg-red-50 text-red-700 border-red-100',
            default => 'bg-gray-50 text-gray-600 border-gray-100',
        };
    }
}
