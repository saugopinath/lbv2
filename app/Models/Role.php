<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role as SpatieRole;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class Role extends SpatieRole implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $table = 'roles';
    public $timestamps = false;

    use HasFactory, HasUlids;

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'guard_name'     => 'web',
        'can_manage_roles' => '[]',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'parent_role_id',
        'can_manage_roles'
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
<<<<<<< HEAD
     public function mappings(): HasMany
    {
        return $this->hasMany(UserRoleSchemeOfficeMapping::class, 'role_id');
    }
    
   


    
=======
>>>>>>> d726694e2ff4cbf8a12d9642a72f953c3c34c7b5
}
