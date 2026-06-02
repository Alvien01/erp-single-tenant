<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Journal extends Model
{

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
