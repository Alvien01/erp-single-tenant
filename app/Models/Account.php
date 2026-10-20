<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $guarded = [];

    public function journals()
    {
        return $this->hasMany(Journal::class);
    }
}
