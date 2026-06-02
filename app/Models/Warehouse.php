<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{

    public function locations()
    {
        return $this->hasMany(WarehouseLocation::class);
    }

    public function stockItems()
    {
        return $this->hasMany(StockItem::class);
    }
}
