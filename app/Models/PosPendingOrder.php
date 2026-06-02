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
}
