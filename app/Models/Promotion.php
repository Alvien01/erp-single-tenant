<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{

    protected $guarded = ['id'];

    protected $casts = [
        'value' => 'decimal:2',
        'min_purchase' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'tiered_rules' => 'array',
        'active_days' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'promotion_products');
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Check if promo is currently valid
     */
    public function isCurrentlyValid(): bool
    {
        if (!$this->is_active) return false;

        $now = now();

        if ($this->start_date && $now->lt($this->start_date)) return false;
        if ($this->end_date && $now->gt($this->end_date->endOfDay())) return false;

        if ($this->start_time && $this->end_time) {
            $currentTime = $now->format('H:i:s');
            if ($currentTime < $this->start_time || $currentTime > $this->end_time) return false;
        }

        if ($this->active_days && !empty($this->active_days)) {
            if (!in_array(strtolower($now->format('l')), $this->active_days)) return false;
        }

        return true;
    }
}
