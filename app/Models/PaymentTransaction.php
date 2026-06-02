<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentTransaction extends Model
{

    protected $fillable = ['transaction_number', 'payment_provider_id', 'amount', 'fee', 'currency', 'status', 'reference_type', 'reference_id', 'external_id', 'gateway_response', 'paid_at'];
    protected $casts = ['gateway_response' => 'array', 'paid_at' => 'datetime'];

    public function provider(): BelongsTo { return $this->belongsTo(PaymentProvider::class, 'payment_provider_id'); }
}
