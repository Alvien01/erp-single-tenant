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
}
