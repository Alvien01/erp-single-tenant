<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{

    protected $fillable = ['name', 'description', 'venue', 'start_date', 'end_date', 'type', 'status', 'max_attendees', 'ticket_price', 'organizer_id'];
    protected $casts = ['start_date' => 'datetime', 'end_date' => 'datetime'];

    public function organizer(): BelongsTo { return $this->belongsTo(User::class, 'organizer_id'); }
    public function attendees(): HasMany { return $this->hasMany(EventAttendee::class); }
}
