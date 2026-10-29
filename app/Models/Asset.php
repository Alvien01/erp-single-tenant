<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{

    public function depreciations()
    {
        return $this->hasMany(AssetDepreciation::class);
    }

    public function getTotalDepreciationAttribute(): float
    {
        return floatval($this->depreciations->sum('amount'));
    }

    public function getBookValueAttribute(): float
    {
        return floatval($this->purchase_price) - $this->total_depreciation;
    }
}
