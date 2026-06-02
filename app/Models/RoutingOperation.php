<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoutingOperation extends Model
{

    protected $fillable = ['routing_id', 'work_center_id', 'name', 'sequence', 'time_cycle_minutes', 'setup_time_minutes', 'description'];

    public function routing(): BelongsTo { return $this->belongsTo(MrpRouting::class, 'routing_id'); }
    public function workCenter(): BelongsTo { return $this->belongsTo(WorkCenter::class); }
}
