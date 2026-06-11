<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxInvoice extends Model
{
    protected $fillable = ['invoice_number', 'type', 'date', 'dpp', 'ppn', 'status'];
}
