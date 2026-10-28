<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Invoice extends Model
{

    protected $fillable = [
        'invoice_number',
        'type',
        'customer_id',
        'supplier_id',
        'date',
        'due_date',
        'total_amount',
        'tax_amount',
        'grand_total',
        'status',
        'original_invoice_id',
        'reference_type',
        'reference_id',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function originalInvoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'original_invoice_id');
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'draft' => 'bg-gray-100 text-gray-700',
            'posted' => 'bg-blue-100 text-blue-800',
            'paid' => 'bg-emerald-100 text-emerald-800',
            'void' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-700',
        };
    }

    public function getTypeBadgeClassAttribute(): string
    {
        return match($this->type) {
            'credit_note' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'debit_note' => 'bg-rose-50 text-rose-700 border-rose-200',
            default => 'bg-gray-50 text-gray-700 border-gray-200',
        };
    }

    public function getPartnerNameAttribute(): string
    {
        return $this->type === 'credit_note'
            ? ($this->customer->name ?? '-')
            : ($this->supplier->name ?? '-');
    }
}
