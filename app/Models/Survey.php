<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Survey extends Model
{

    protected $fillable = ['title', 'description', 'type', 'status', 'created_by', 'open_at', 'close_at'];
    protected $casts = ['open_at' => 'datetime', 'close_at' => 'datetime'];

    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function questions(): HasMany { return $this->hasMany(SurveyQuestion::class); }
    public function responses(): HasMany { return $this->hasMany(SurveyResponse::class); }
}
