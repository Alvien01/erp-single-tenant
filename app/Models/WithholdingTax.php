<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WithholdingTax extends Model
{
    protected $fillable = ['type', 'amount', 'reference_type', 'reference_id', 'status'];
}
