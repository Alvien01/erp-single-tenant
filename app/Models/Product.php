<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{

    protected $guarded = [];

    protected $casts = [
        'image' => 'array',
        'price' => 'decimal:2',
        'stock' => 'decimal:2',
        'min_stock' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function getFirstImageUrlAttribute(): ?string
    {
        if (empty($this->image)) {
            return null;
        }

        $img = is_array($this->image) ? $this->image : json_decode($this->image, true);
        if (is_array($img) && !empty($img)) {
            $firstImage = reset($img);
            return asset('storage/' . $firstImage);
        }

        return null;
    }
}
