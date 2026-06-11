<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeriodClosing extends Model
{
    protected $fillable = ['closing_date', 'closed_by', 'status', 'notes'];

    public function user()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }
}
