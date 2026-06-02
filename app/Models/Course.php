<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{

    protected $fillable = ['title', 'slug', 'description', 'category', 'level', 'status', 'duration_hours', 'instructor_id'];

    public function instructor(): BelongsTo { return $this->belongsTo(User::class, 'instructor_id'); }
    public function lessons(): HasMany { return $this->hasMany(CourseLesson::class); }
    public function enrollments(): HasMany { return $this->hasMany(CourseEnrollment::class); }
}
