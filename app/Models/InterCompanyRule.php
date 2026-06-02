<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InterCompanyRule extends Model
{

    protected $fillable = ['source_company_id', 'target_company_id', 'rule_type', 'auto_create', 'is_active'];

    public function sourceCompany(): BelongsTo { return $this->belongsTo(Company::class, 'source_company_id'); }
    public function targetCompany(): BelongsTo { return $this->belongsTo(Company::class, 'target_company_id'); }
}
