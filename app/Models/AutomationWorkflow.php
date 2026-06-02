<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AutomationWorkflow extends Model
{

    protected $fillable = ['name', 'description', 'trigger_type', 'trigger_model', 'status', 'created_by'];

    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function steps(): HasMany { return $this->hasMany(WorkflowStep::class, 'workflow_id'); }
}
