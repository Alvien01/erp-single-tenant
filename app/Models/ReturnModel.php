<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ReturnModel extends Model
{

    protected $table = 'returns';

    protected $fillable = [
        'return_number',
        'type',
        'reference_type',
        'reference_id',
        'return_date',
        'warehouse_id',
        'total_amount',
        'status',
        'notes',
        'created_by',
    ];

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ReturnItem::class, 'return_id');
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'draft' => 'bg-gray-100 text-gray-700',
            'completed' => 'bg-emerald-100 text-emerald-800',
            'cancelled' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-700',
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return $this->reference_type === \App\Models\Sale::class ? 'customer' : 'vendor';
    }

    public function getTypeBadgeClassAttribute(): string
    {
        return match($this->type_label) {
            'customer' => 'bg-blue-50 text-blue-700 border-blue-200',
            'vendor' => 'bg-purple-50 text-purple-700 border-purple-200',
            default => 'bg-gray-50 text-gray-700 border-gray-200',
        };
    }

    public function getReferenceNumberAttribute(): string
    {
        if (!$this->reference) {
            return '-';
        }

        return $this->reference_type === \App\Models\Sale::class
            ? ($this->reference->invoice_number ?? '-')
            : ($this->reference->purchase_number ?? '-');
    }
}
