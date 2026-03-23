<?php
// app/Helpers/MenuHelper.php

namespace App\Helpers;

use App\Models\Menu;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class MenuHelper
{
    /**
     * Get all menus accessible to current user
     */
    public static function getMenus($refresh = false)
    {
        $userId = Auth::id();
        $cacheKey = "user_menus_{$userId}";
        
        if ($refresh) {
            Cache::forget($cacheKey);
        }
        
        return Cache::remember($cacheKey, 3600, function () use ($userId) {
            $user = Auth::user();
            
            // Get all root menus
            $rootMenus = Menu::active()
                ->root()
                ->orderBy('menu_rank')
                ->get();
            
            // Filter based on user permissions
            return $rootMenus->filter(function ($menu) use ($user) {
                return self::hasMenuAccess($menu, $user);
            })->map(function ($menu) use ($user) {
                $menu->children = self::getFilteredChildren($menu, $user);
                return $menu;
            });
        });
    }
    
    /**
     * Check if user has access to menu
     */
    private static function hasMenuAccess($menu, $user)
    {
        if (empty($menu->permission_id)) {
            return true;
        }
        
        foreach ($menu->permission_id as $permissionId) {
            $permission = \App\Models\Permission::find($permissionId);
            if ($permission && $user->hasPermission($permission->name)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Get filtered children menus
     */
    private static function getFilteredChildren($parent, $user)
    {
        return $parent->children->filter(function ($child) use ($user) {
            return self::hasMenuAccess($child, $user);
        })->map(function ($child) use ($user) {
            $child->children = self::getFilteredChildren($child, $user);
            return $child;
        });
    }
    
    /**
     * Get all menus for admin management
     */
    public static function getAllMenus()
    {
        return Menu::with('children')
            ->root()
            ->orderBy('menu_rank')
            ->get();
    }
    
    /**
     * Build menu tree for management
     */
    public static function getMenuTree()
    {
        $menus = Menu::all();
        return self::buildTree($menus);
    }
    
    private static function buildTree($menus, $parentId = null)
    {
        $result = [];
        
        foreach ($menus as $menu) {
            if ($menu->parent_id == $parentId) {
                $children = self::buildTree($menus, $menu->id);
                if ($children) {
                    $menu->children = $children;
                }
                $result[] = $menu;
            }
        }
        
        return $result;
    }
    
    /**
     * Clear menu cache for user
     */
    public static function clearCache($userId = null)
    {
        if ($userId) {
            Cache::forget("user_menus_{$userId}");
        } else {
            // Clear all user menu caches
            $users = \App\Models\User::all();
            foreach ($users as $user) {
                Cache::forget("user_menus_{$user->id}");
            }
        }
    }
}