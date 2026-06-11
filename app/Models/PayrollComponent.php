<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollComponent extends Model
{
    protected $fillable = ['payroll_id', 'name', 'type', 'amount'];

    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }
}
