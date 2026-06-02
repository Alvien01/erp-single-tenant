<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeferredEntry extends Model
{

    protected $fillable = ['name', 'type', 'account_id', 'recognition_account_id', 'total_amount', 'start_date', 'end_date', 'periods', 'amount_per_period', 'recognized_amount', 'status', 'reference_type', 'reference_id'];
    protected $casts = ['start_date' => 'date', 'end_date' => 'date'];

    public function account(): BelongsTo { return $this->belongsTo(Account::class); }
    public function recognitionAccount(): BelongsTo { return $this->belongsTo(Account::class, 'recognition_account_id'); }
}
