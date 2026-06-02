<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkflowStep extends Model
{

    protected $fillable = ['workflow_id', 'sequence', 'action_type', 'action_config', 'wait_hours', 'condition_field', 'condition_operator', 'condition_value'];
    protected $casts = ['action_config' => 'array'];

    public function workflow(): BelongsTo { return $this->belongsTo(AutomationWorkflow::class, 'workflow_id'); }
}
