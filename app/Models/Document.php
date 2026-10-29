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

    public function getFileUrlAttribute(): ?string
    {
        if (!$this->file_path) {
            return null;
        }

        if (str_starts_with($this->file_path, 'http://') || str_starts_with($this->file_path, 'https://')) {
            return $this->file_path;
        }

        return asset('storage/' . $this->file_path);
    }

    public function getFileExtensionAttribute(): string
    {
        return strtoupper(pathinfo($this->file_path ?? '', PATHINFO_EXTENSION) ?: 'FILE');
    }

    public function getFileSizeHumanAttribute(): ?string
    {
        if (!$this->file_path || !\Illuminate\Support\Facades\Storage::disk('public')->exists($this->file_path)) {
            return null;
        }

        $bytes = \Illuminate\Support\Facades\Storage::disk('public')->size($this->file_path);
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 1) . ' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 0) . ' KB';
        }
        return $bytes . ' B';
    }

    public function getFileExistsAttribute(): bool
    {
        return !empty($this->file_path) && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->file_path);
    }
}
