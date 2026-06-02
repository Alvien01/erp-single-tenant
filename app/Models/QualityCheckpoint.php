<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QualityCheckpoint extends Model
{

    protected $fillable = [
        'product_id',
        'test_name',
        'criteria',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function checks()
    {
        return $this->hasMany(QualityCheck::class);
    }
}
