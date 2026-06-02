<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MrpRouting extends Model
{

    protected $fillable = ['name', 'bom_id', 'notes', 'is_active'];

    public function bom(): BelongsTo { return $this->belongsTo(Bom::class); }
    public function operations(): HasMany { return $this->hasMany(RoutingOperation::class, 'routing_id'); }
}
