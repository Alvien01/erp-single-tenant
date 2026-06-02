<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankStatement extends Model
{

    protected $fillable = [
        'date',
        'description',
        'amount',
        'reference',
        'is_reconciled',
        'journal_entry_id',
    ];

    protected $casts = [
        'date' => 'date',
        'is_reconciled' => 'boolean',
        'amount' => 'decimal:2',
    ];
}
