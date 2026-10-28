<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Applicant extends Model
{

    protected $fillable = [
        'name',
        'email',
        'phone',
        'job_position_id',
        'status',
        'applied_date',
        'notes',
    ];

    protected $casts = [
        'applied_date' => 'date',
    ];

    public function jobPosition()
    {
        return $this->belongsTo(JobPosition::class);
    }

    public function getStageButtonClass(string $stage): string
    {
        $active = $this->status === $stage;
        return match($stage) {
            'applied' => $active ? 'bg-blue-600 text-white' : 'bg-blue-50 text-blue-700 hover:bg-blue-100',
            'interview' => $active ? 'bg-amber-500 text-white' : 'bg-amber-50 text-amber-700 hover:bg-amber-100',
            'offered' => $active ? 'bg-purple-600 text-white' : 'bg-purple-50 text-purple-700 hover:bg-purple-100',
            'hired' => $active ? 'bg-emerald-600 text-white' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100',
            'rejected' => $active ? 'bg-red-600 text-white' : 'bg-red-50 text-red-700 hover:bg-red-100',
            default => 'bg-gray-50 text-gray-700 hover:bg-gray-100',
        };
    }
}
