<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{

    protected $fillable = ['title', 'description', 'customer_id', 'assigned_to', 'start_time', 'end_time', 'location', 'status', 'type', 'notes'];
    protected $casts = ['start_time' => 'datetime', 'end_time' => 'datetime'];

    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function assignee(): BelongsTo { return $this->belongsTo(User::class, 'assigned_to'); }
}
