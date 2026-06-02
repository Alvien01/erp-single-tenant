<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BomItem extends Model
{

    public function component()
    {
        return $this->belongsTo(Product::class, 'component_id');
    }

    public function bom()
    {
        return $this->belongsTo(Bom::class, 'bom_id');
    }
}
