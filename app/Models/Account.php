<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{

    public function journals()
    {
        return $this->hasMany(Journal::class);
    }
}
