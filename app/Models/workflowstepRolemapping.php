<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkflowsteproleMapping extends Model
{
    protected $table = "workflowstep_rolemappings";
    public static function getLabelRoleIdsByRole($roleId)
    {
        return self::where('role_id', $roleId)
            ->first(['same_label_role_id', 'next_label_role_id']);
    }
}
