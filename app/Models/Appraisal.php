<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appraisal extends Model
{

    protected $fillable = [
        'employee_id',
        'appraisal_date',
        'manager_id',
        'period',
        'score',
        'notes',
        'status',
    ];

    protected $casts = [
        'appraisal_date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'draft' => 'bg-gray-50 text-gray-600 border-gray-100',
            'confirmed' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
            default => 'bg-gray-50 text-gray-600 border-gray-100',
        };
    }
}
