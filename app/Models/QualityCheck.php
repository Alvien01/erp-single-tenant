<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QualityCheck extends Model
{

    protected $fillable = [
        'quality_checkpoint_id',
        'reference_type',
        'reference_id',
        'checked_by',
        'status',
        'notes',
        'checked_at',
    ];

    protected $casts = [
        'checked_at' => 'datetime',
    ];

    public function checkpoint()
    {
        return $this->belongsTo(QualityCheckpoint::class, 'quality_checkpoint_id');
    }

    public function checker()
    {
        return $this->belongsTo(User::class, 'checked_by');
    }
}
