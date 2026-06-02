<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class FieldServiceOrder extends Model
{

    protected $fillable = ['fsm_number', 'customer_id', 'project_id', 'assigned_to', 'title', 'description', 'priority', 'status', 'scheduled_date', 'start_time', 'end_time', 'location_address', 'latitude', 'longitude', 'total_cost', 'customer_signature'];
    protected $casts = ['scheduled_date' => 'datetime', 'start_time' => 'datetime', 'end_time' => 'datetime'];

    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function project(): BelongsTo { return $this->belongsTo(Project::class); }
    public function technician(): BelongsTo { return $this->belongsTo(User::class, 'assigned_to'); }
    public function worksheet(): HasOne { return $this->hasOne(FsmWorksheet::class); }
    public function partsUsed(): HasMany { return $this->hasMany(FsmPartUsed::class); }
}
