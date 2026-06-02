<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseLocation extends Model
{

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
