<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlogPost extends Model
{

    protected $fillable = ['title', 'slug', 'content', 'excerpt', 'featured_image', 'category', 'tags', 'status', 'author_id', 'published_at', 'views'];
    protected $casts = ['published_at' => 'datetime'];

    public function author(): BelongsTo { return $this->belongsTo(User::class, 'author_id'); }
    public function scopePublished($q) { return $q->where('status', 'published'); }
}
