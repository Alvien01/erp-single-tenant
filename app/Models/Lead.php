<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{

    protected $fillable = [
        'title',
        'contact_name',
        'company_name',
        'email',
        'phone',
        'expected_revenue',
        'probability',
        'status',
        'user_id',
        'notes',
    ];

    public function salesperson()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
