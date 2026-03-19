<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DynamicWorkflowStep extends BaseAuditableModel
{
    protected $table = 'dynamic_workflow_steps';
    
    protected $fillable = [
        'scheme_id',
        'module_id',
        'label_id',
        'rank',
        'role_id',
        'action_type',
        'success_rank',
        'revert_rank',
        'is_final_step'
    ];

    protected $casts = [
        'is_final_step' => 'boolean',
        'rank' => 'integer',
        'success_rank' => 'integer',
        'revert_rank' => 'integer'
    ];

    public function module()
    {
        return $this->belongsTo(DynamicWorkflowModule::class, 'module_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
}
