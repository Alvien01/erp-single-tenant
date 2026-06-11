<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    protected $fillable = ['code', 'name', 'bank_name', 'account_number', 'balance'];

    public function cashTransactions()
    {
        return $this->hasMany(CashTransaction::class);
    }
}
