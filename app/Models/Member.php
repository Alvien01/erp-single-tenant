<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{

    protected $guarded = ['id'];

    protected $casts = [
        'birth_date' => 'date',
        'total_spending' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function pointLogs()
    {
        return $this->hasMany(MemberPointLog::class);
    }

    public function transactions()
    {
        return $this->hasMany(PosTransaction::class);
    }

    public function vouchers()
    {
        return $this->hasMany(Voucher::class);
    }

    /**
     * Get tier discount percentage
     */
    public function getTierDiscountAttribute(): float
    {
        return match ($this->tier) {
            'gold' => 5.0,
            'silver' => 2.0,
            default => 0.0,
        };
    }

    /**
     * Auto-upgrade tier based on total spending
     */
    public function checkTierUpgrade(): void
    {
        if ($this->total_spending >= 5000000) {
            $this->update(['tier' => 'gold']);
        } elseif ($this->total_spending >= 2000000) {
            $this->update(['tier' => 'silver']);
        }
    }
}
