<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CmsPage extends Model
{

    protected $fillable = ['title', 'slug', 'content', 'meta_title', 'meta_description', 'status', 'created_by', 'published_at', 'sort_order'];
    protected $casts = ['published_at' => 'datetime'];

    public function author(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function scopePublished($q) { return $q->where('status', 'published'); }
}
