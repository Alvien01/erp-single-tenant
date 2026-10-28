<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobPosition extends Model
{

    protected $fillable = [
        'title',
        'department',
        'expected_employees',
        'status',
        'description',
    ];

    public function applicants()
    {
        return $this->hasMany(Applicant::class);
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'open' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
            'closed' => 'bg-red-50 text-red-700 border-red-100',
            default => 'bg-gray-50 text-gray-700 border-gray-100',
        };
    }
}
