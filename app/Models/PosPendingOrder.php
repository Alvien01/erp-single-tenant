<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosPendingOrder extends Model
{

    protected $guarded = ['id'];

    protected $casts = [
        'cart_data' => 'array',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function getItemsPreviewTextAttribute(): string
    {
        $cartData = is_array($this->cart_data)
            ? $this->cart_data
            : json_decode($this->cart_data ?? '[]', true);

        $itemNames = is_array($cartData) ? array_column($cartData, 'name') : [];
        $text = implode(', ', array_slice($itemNames, 0, 3));
        if (count($itemNames) > 3) {
            $text .= '...';
        }
        return $text;
    }
}
