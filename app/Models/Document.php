<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{

    protected $fillable = [
        'name',
        'category',
        'file_path',
        'version',
        'status',
        'created_by',
        'signed_at',
        'signature_data',
    ];

    protected $casts = [
        'signed_at' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function versions()
    {
        return $this->hasMany(DocumentVersion::class);
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'draft' => 'bg-gray-100 text-gray-600 border-gray-200',
            'pending_signature' => 'bg-amber-50 text-amber-700 border-amber-100',
            'signed' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
            default => 'bg-gray-100 text-gray-600 border-gray-200',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'draft' => 'Draft',
            'pending_signature' => 'Needs Signature',
            'signed' => 'Signed & Sealed',
            default => ucfirst(str_replace('_', ' ', (string) $this->status)),
        };
    }
}
