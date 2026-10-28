<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EcoRequest extends Model
{

    protected $fillable = ['eco_number', 'bom_id', 'product_id', 'title', 'description', 'reason', 'old_bom_version', 'new_bom_version', 'requested_by', 'approved_by', 'status', 'approved_at'];
    protected $casts = ['approved_at' => 'datetime'];

    public function bom(): BelongsTo { return $this->belongsTo(Bom::class); }
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function requester(): BelongsTo { return $this->belongsTo(User::class, 'requested_by'); }
    public function approver(): BelongsTo { return $this->belongsTo(User::class, 'approved_by'); }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'draft' => 'bg-gray-100 text-gray-700 border-gray-200',
            'pending_review' => 'bg-amber-50 text-amber-700 border-amber-200',
            'approved' => 'bg-blue-50 text-blue-700 border-blue-200',
            'applied' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'rejected' => 'bg-red-50 text-red-700 border-red-200',
            default => 'bg-gray-50 text-gray-600 border-gray-200',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'draft' => 'Draft',
            'pending_review' => 'Pending Review',
            'approved' => 'Approved',
            'applied' => 'Applied (Live)',
            'rejected' => 'Rejected',
            default => ucfirst((string) $this->status),
        };
    }
}
