<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{

    protected $fillable = [
        'employee_id',
        'date',
        'category',
        'amount',
        'description',
        'status',
        'approved_by',
        'approved_at',
        'paid_at',
    ];

    protected $casts = [
        'date' => 'date',
        'approved_at' => 'datetime',
        'paid_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
