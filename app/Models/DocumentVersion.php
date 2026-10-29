<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentVersion extends Model
{

    protected $fillable = [
        'document_id',
        'version',
        'file_path',
        'created_by',
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
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
}
