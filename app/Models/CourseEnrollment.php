<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseEnrollment extends Model
{

    protected $fillable = ['course_id', 'user_id', 'progress', 'status', 'completed_at'];
    protected $casts = ['completed_at' => 'datetime'];

    public function course(): BelongsTo { return $this->belongsTo(Course::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
