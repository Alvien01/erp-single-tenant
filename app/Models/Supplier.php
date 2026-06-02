<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}
