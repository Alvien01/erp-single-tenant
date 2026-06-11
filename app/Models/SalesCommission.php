<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesCommission extends Model
{
    protected $fillable = ['sale_id', 'employee_id', 'amount', 'status'];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
