<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApprovalRule extends Model
{

    protected $fillable = [
        'module_type',
        'min_amount',
        'max_amount',
        'role_required',
        'sequence',
        'is_active',
    ];

    public function approvals(): HasMany
    {
        return $this->hasMany(Approval::class);
    }
}
