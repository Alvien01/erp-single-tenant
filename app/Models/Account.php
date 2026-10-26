<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $guarded = [];

    protected $casts = [
        'balance' => 'decimal:2',
    ];

    public function journals()
    {
        return $this->hasMany(Journal::class);
    }
}
