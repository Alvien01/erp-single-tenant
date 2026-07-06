<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxFiling extends Model
{
    protected $fillable = [
        'tax_type',
        'period',
        'amount',
        'filing_date',
        'ntpn',
        'status',
        'notes'
    ];

    protected $casts = [
        'filing_date' => 'date',
        'amount' => 'decimal:2'
    ];
}
