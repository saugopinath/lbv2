<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkflowStep extends Model
{
    public function scheme()
    {
        return $this->belongsTo(Scheme::class);
    }

    public function parent()
    {
        return $this->belongsTo(WorkflowStep::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(WorkflowStep::class, 'parent_id');
    }
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'workflowstep_rolemappings', 'workflow_step_id', 'role_id')
            ->withPivot(['rank', 'same_label_role_id', 'next_label_role_id'])
            ->withTimestamps();
    }
}
