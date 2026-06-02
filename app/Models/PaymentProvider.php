<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentProvider extends Model
{

    protected $fillable = ['name', 'code', 'provider_type', 'credentials', 'is_active', 'is_test_mode', 'fee_percent', 'fee_fixed'];
    protected $casts = ['credentials' => 'array', 'is_active' => 'boolean', 'is_test_mode' => 'boolean'];
}
