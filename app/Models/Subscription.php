<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $guarded = [];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function getCycleBadgeClassAttribute(): string
    {
        return match($this->billing_cycle) {
            'yearly' => 'bg-purple-50 text-purple-700 border-purple-200',
            'quarterly' => 'bg-amber-50 text-amber-700 border-amber-200',
            default => 'bg-blue-50 text-blue-700 border-blue-200'
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'active' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'paused' => 'bg-amber-50 text-amber-700 border-amber-200',
            'cancelled' => 'bg-red-50 text-red-700 border-red-200',
            'expired' => 'bg-gray-100 text-gray-600 border-gray-200',
            default => 'bg-gray-50 text-gray-600 border-gray-200'
        };
    }
}
