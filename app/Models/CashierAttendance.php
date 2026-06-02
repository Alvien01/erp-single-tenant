<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashierAttendance extends Model
{

    protected $guarded = ['id'];

    protected $casts = [
        'date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
