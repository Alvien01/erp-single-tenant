<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentDisbursement extends Model
{
    protected $fillable = ['disbursement_number', 'supplier_id', 'purchase_id', 'payment_date', 'amount', 'payment_method', 'notes'];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
}
