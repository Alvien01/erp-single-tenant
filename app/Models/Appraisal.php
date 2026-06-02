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
}
