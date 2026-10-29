<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BudgetLine extends Model
{

    protected $fillable = [
        'budget_id',
        'account_id',
        'planned_amount',
        'actual_amount',
    ];

    protected $casts = [
        'planned_amount' => 'decimal:2',
        'actual_amount' => 'decimal:2',
    ];

    public function budget()
    {
        return $this->belongsTo(Budget::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function getRemainingAmountAttribute(): float
    {
        return floatval($this->planned_amount) - floatval($this->actual_amount);
    }

    public function getUsagePercentageAttribute(): float
    {
        $planned = floatval($this->planned_amount);
        return $planned > 0 ? (floatval($this->actual_amount) / $planned) * 100 : 0;
    }

    public function getClampedPercentageAttribute(): float
    {
        return min(100, $this->usage_percentage);
    }
}
