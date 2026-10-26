<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RentalOrder extends Model
{
    protected $guarded = [];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'return_date' => 'date',
        'rental_rate' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'quantity' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Get descriptive status badge information
     */
    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'confirmed' => [
                'label' => 'Terkonfirmasi',
                'class' => 'bg-blue-50 text-blue-700 border-blue-200'
            ],
            'ongoing' => [
                'label' => 'Sedang Disewa',
                'class' => 'bg-indigo-50 text-indigo-700 border-indigo-200'
            ],
            'returned' => [
                'label' => 'Dikembalikan',
                'class' => 'bg-amber-50 text-amber-700 border-amber-200'
            ],
            'completed' => [
                'label' => 'Selesai',
                'class' => 'bg-emerald-50 text-emerald-700 border-emerald-200'
            ],
            'cancelled' => [
                'label' => 'Dibatalkan',
                'class' => 'bg-rose-50 text-rose-700 border-rose-200'
            ],
            default => [
                'label' => 'Draft',
                'class' => 'bg-gray-100 text-gray-700 border-gray-200'
            ],
        };
    }

    /**
     * Get payment status badge information
     */
    public function getPaymentStatusBadgeAttribute()
    {
        return match ($this->payment_status) {
            'paid' => [
                'label' => 'Lunas',
                'class' => 'bg-emerald-50 text-emerald-700 border-emerald-200'
            ],
            'partially_paid' => [
                'label' => 'Sebagian',
                'class' => 'bg-amber-50 text-amber-700 border-amber-200'
            ],
            default => [
                'label' => 'Belum Lunas',
                'class' => 'bg-rose-50 text-rose-700 border-rose-200'
            ],
        };
    }
}
