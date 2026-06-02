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
}
