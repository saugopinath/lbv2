<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Models\Permission;



class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password','two_factor_code', 'two_factor_expires_at','flag_sent_otp','password_set_time','password_expires_at','updated_at','mobile_no'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function RoleSchemeOfficeMappings(): HasMany
    {
        
        return $this->hasMany(UserRoleSchemeOfficeMapping::class);
    }
    public function mappedRoles(): BelongsToMany
    {
        return $this->belongsToMany(
            Role::class,
            'user_role_scheme_office_mappings',
            'user_id',
            'role_id'
        )
        ->wherePivot('is_active', 1)
        ->where('roles.id', '!=', 10);
    }

    /**
     * Direct permissions assigned to the user (not via roles)
     */
    public function mappedPermissions(): BelongsToMany
    {
        return $this->morphToMany(
            Permission::class,
            'model',
            'model_has_permissions',
            'model_id',
            'permission_id'
        );
    }
}
