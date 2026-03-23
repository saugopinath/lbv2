<?php
// app/Models/Menu.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Menu extends Model
{
    protected $fillable = [
        'menu_name', 'icon', 'route', 'url', 'parent_id', 
        'menu_rank', 'department_id', 'scheme_id', 'role_id', 
        'permission_id', 'is_active'
    ];

    protected $casts = [
        'department_id' => 'array',
        'scheme_id' => 'array',
        'role_id' => 'array',
        'permission_id' => 'array',
        'is_active' => 'boolean',
        'menu_rank' => 'integer',
    ];

    protected $appends = ['has_children', 'permission_names'];

    /**
     * Parent menu relationship
     */
    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    /**
     * Child menus relationship
     */
    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id')
                    ->orderBy('menu_rank', 'asc');
    }

    /**
     * Get permission names from permission IDs
     */
    public function getPermissionNamesAttribute()
    {
        if (empty($this->permission_id)) {
            return [];
        }

        return Permission::whereIn('id', $this->permission_id)
                         ->pluck('name')
                         ->toArray();
    }

    /**
     * Check if menu has children
     */
    public function getHasChildrenAttribute()
    {
        return $this->children()->exists();
    }

    /**
     * Scope for active menus
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for root menus
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Get all permissions assigned to this menu
     */
    public function permissions()
    {
        return Permission::whereIn('id', $this->permission_id ?? [])->get();
    }

    /**
     * Check if user has access to this menu
     */
    public function userHasAccess($user = null)
    {
        $user = $user ?? auth()->user();
        
        if (!$user) return false;
        
        // If no permissions required, allow access
        if (empty($this->permission_id)) {
            return true;
        }
        
        // Check if user has any of the required permissions
        foreach ($this->permission_id as $permissionId) {
            $permission = Permission::find($permissionId);
            if ($permission && $user->hasPermission($permission->name)) {
                return true;
            }
        }
        
        return false;
    }
}