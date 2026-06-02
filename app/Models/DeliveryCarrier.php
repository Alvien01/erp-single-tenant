<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryCarrier extends Model
{

    protected $fillable = ['name', 'code', 'provider', 'tracking_url', 'default_cost', 'margin_percent', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];
}
