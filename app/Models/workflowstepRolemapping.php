<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkflowsteproleMapping extends Model
{
    protected $table = "workflowstep_rolemappings";
    public static function getLabelRoleIdsByRole($schemeId, $roleId, $rank = null)
    {
        $query = self::query();
        if ($rank !== null) {
            $query->where('rank', $rank);
        } else {
            $query->where('role_id', $roleId);
        }
        return $query->where('scheme_id', $schemeId)
            ->first(['same_label_role_id', 'next_label_role_id']);
    }
    public function workflowStep()
    {
        return $this->belongsTo(WorkflowStep::class, 'workflow_step_id', 'id');
    }
}
