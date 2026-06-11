<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashTransaction extends Model
{
    protected $fillable = ['date', 'amount', 'type', 'reference', 'description', 'account_id', 'bank_account_id'];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }
}
