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
}
