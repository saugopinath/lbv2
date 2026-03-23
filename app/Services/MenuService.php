<?php
// app/Services/MenuService.php
namespace App\Services;

use App\Models\Menu;
use App\Models\Role;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MenuService
{
    /**
     * Get user menus from JSON file or cache
     */
    public function getUserMenus()
    {
        $user = Auth::user();
        if (!$user) {
            return [];
        }

        $roleIds = $user->roles->pluck('id')->toArray();
        $cacheKey = 'user_menus_' . implode('_', $roleIds);

        return Cache::remember($cacheKey, 3600, function () use ($roleIds) {
            $menus = [];

            // Always load from JSON storage (storage/app/menus) in sidebar.
            $baseMenus = $this->loadJsonMenus('base.json');
            if ($baseMenus) {
                $menus = array_merge($menus, $baseMenus);
            }

            foreach ($roleIds as $roleId) {
                $roleMenus = $this->loadJsonMenus('role_' . $roleId . '.json');
                if ($roleMenus) {
                    $menus = array_merge($menus, $roleMenus);
                }
            }

            return $menus;
        });
    }
    
    /**
     * Load menus from JSON file
     */
    protected function loadJsonMenus($filename)
    {
        $path = storage_path('app/menus/' . $filename);
        if (file_exists($path)) {
            $json = file_get_contents($path);
            return json_decode($json, true) ?? [];
        }
        return [];
    }
    
    /**
     * Format a single menu item
     */
    protected function formatMenu($menu)
    {
        $item = [
            'id' => $menu->id,
            'name' => $menu->name,
            'icon' => $menu->icon,
            'route' => $menu->route,
            'url' => $menu->url,
            'permission_key' => $menu->permission_key,
            'children' => []
        ];
        
        foreach ($menu->children as $child) {
            $item['children'][] = $this->formatMenu($child);
        }
        
        return $item;
    }
    
    /**
     * Generate JSON file for all roles
     */
    public function generateJsonForAllRoles()
    {
        $roles = Role::all();
        
        foreach ($roles as $role) {
            $menus = Menu::whereHas('roles', function($query) use ($role) {
                    $query->where('role_id', $role->id)
                          ->where('menu_role.is_active', true);
                })
                ->where('is_active', true)
                ->whereNull('parent_id')
                ->with(['children' => function($query) use ($role) {
                    $query->whereHas('roles', function($q) use ($role) {
                        $q->where('role_id', $role->id)
                          ->where('menu_role.is_active', true);
                    })
                    ->where('is_active', true)
                    ->orderBy('order');
                }])
                ->orderBy('order')
                ->get();
            
            $formattedMenus = [];
            foreach ($menus as $menu) {
                $formattedMenus[] = $this->formatMenu($menu);
            }
            
            $jsonString = json_encode($formattedMenus, JSON_PRETTY_PRINT);
            Storage::disk('local')->put('menus/role_' . $role->id . '.json', $jsonString);
        }
        
        return true;
    }
    
    /**
     * Clear all menu caches
     */
    public function clearMenuCache()
    {
        // Clear all user menu caches
        $users = \App\Models\User::all();
        foreach ($users as $user) {
            $roleIds = $user->roles->pluck('id')->toArray();
            $cacheKey = 'user_menus_' . implode('_', $roleIds);
            Cache::forget($cacheKey);
        }
        
        // Regenerate JSON files
        $this->generateJsonForAllRoles();
        
        return true;
    }
}