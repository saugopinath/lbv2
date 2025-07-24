<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use HasFactory, HasUlids;

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'guard_name'     => 'web',
        'resource_types' => '[]',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'parent_role_id',
        'resource_types'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'resource_types' => 'array',
    ];

    public function parentRole()
    {
        return $this->belongsTo(self::class, 'parent_role_id');
    }

    public function childRoles()
    {
        return $this->hasMany(self::class, 'parent_role_id');
    }

    public function scopeForAuthUser(Builder $query)
    {
        $auth_user_role = auth()->user()->roles->first();

        return $query
            ->where('roles.id', $auth_user_role->id)
            ->orWhere('roles.parent_role_id', $auth_user_role->id);
    }

    protected function getResourceTypesArrAttribute()
    {
        return array_map(function ($resource_type) {
            $name = explode('\\', $resource_type)[2];
            $name = str()->snake($name, ' ');
            $name = str()->title($name);

            return [
                'id'    => $resource_type,
                'name'  => $name
            ];
        }, $this->resource_types);
    }
}
