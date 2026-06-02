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
}
