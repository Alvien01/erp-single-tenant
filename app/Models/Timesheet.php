<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Timesheet extends Model
{

    protected $fillable = [
        'task_id',
        'user_id',
        'date',
        'duration_hours',
        'description',
    ];

    protected $casts = [
        'date' => 'date',
        'duration_hours' => 'decimal:2',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
