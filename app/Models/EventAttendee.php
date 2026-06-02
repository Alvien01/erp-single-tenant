<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventAttendee extends Model
{

    protected $fillable = ['event_id', 'name', 'email', 'phone', 'company', 'status', 'amount_paid'];

    public function event(): BelongsTo { return $this->belongsTo(Event::class); }
}
