<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Models\Role as SpatieRole;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class Role extends SpatieRole implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $table = 'roles';
    public $timestamps = false;

    use HasFactory;

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'guard_name'     => 'web',
        // 'can_manage_roles' => '[]',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        // 'parent_role_id',
        // 'can_manage_roles'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */

    public function parentRole()
    {
        return $this->belongsTo(self::class, 'parent_role_id');
    }

    public function childRoles()
    {
        return $this->hasMany(self::class, 'parent_role_id');
    }
    public function MapOfficeType(): HasMany
    {
        return $this->hasMany(RoleOfficeTypeMapping::class);
    }
    public function mappedPermissions(): BelongsToMany
    {
        return $this->belongsToMany(
            Permission::class,         // related model
            'role_has_permissions',    // pivot table
            'role_id',                 // foreign key on pivot for this model (Role)
            'permission_id'            // related key on pivot (Permission)
        );
    }

    public function workflowSteps(): BelongsToMany
    {
        return $this->belongsToMany(WorkflowStep::class, 'workflowstep_rolemappings', 'role_id', 'workflow_step_id');
    }
}
