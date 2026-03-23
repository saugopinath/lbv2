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
    protected $menuDirectory = 'menus';

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
            $menus = $this->loadMenusFromJson($roleIds);

            // If JSON is missing, fallback to DB temporarily
            if (empty($menus)) {
                $menus = $this->loadMenusFromDB($roleIds);
            }

            return $this->normalizeMenuArray($menus);
        });
    }

    protected function loadMenusFromJson(array $roleIds)
    {
        $this->ensureMenuDirectoryExists();

        $menus = [];

        $baseMenus = $this->loadJsonMenus('base.json');
        if (!empty($baseMenus)) {
            $menus = array_merge($menus, $baseMenus);
        }

        foreach ($roleIds as $roleId) {
            $roleMenus = $this->loadJsonMenus('role_' . $roleId . '.json');
            if (!empty($roleMenus)) {
                $menus = array_merge($menus, $roleMenus);
            }
        }

        return $menus;
    }

    protected function loadMenusFromDB(array $roleIds)
    {
        $menus = Menu::whereHas('roles', function ($query) use ($roleIds) {
                $query->whereIn('role_id', $roleIds)
                      ->where('menu_role.is_active', true);
            })
            ->where('is_active', true)
            ->whereNull('parent_id')
            ->with(['children' => function ($query) use ($roleIds) {
                $query->whereHas('roles', function ($q) use ($roleIds) {
                    $q->whereIn('role_id', $roleIds)
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

        return $formattedMenus;
    }
    
    /**
     * Load menus from JSON file
     */
    protected function loadJsonMenus($filename)
    {
        $path = storage_path('app/' . $this->menuDirectory . '/' . $filename);

        if (!file_exists($path)) {
            return [];
        }

        $json = file_get_contents($path);
        return json_decode($json, true) ?? [];
    }
    
    /**
     * Format a single menu item
     */
    protected function formatMenu($menu)
    {
        $item = [
            'id' => $menu->id,
            'name' => $menu->name,
            'icon' => $this->normalizeIcon($menu->icon),
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

    protected function normalizeIcon($icon)
    {
        if (empty($icon)) {
            return 'fas fa-folder';
        }

        $icon = trim($icon);

        // Fix typo and normalize FontAwesome prefixes
        $icon = preg_replace('/\bfass\b/', 'fas', $icon);
        $icon = preg_replace('/\bfa\s+fa-(\w+)\b/', 'fas fa-$1', $icon);

        if (!preg_match('/\b(fas|far|fal|fad|fab)\b/', $icon)) {
            $icon = 'fas ' . $icon;
        }

        return trim(preg_replace('/\s+/', ' ', $icon));
    }

    public function generateJsonForRole(Role $role)
    {
        $menus = Menu::whereHas('roles', function ($query) use ($role) {
                $query->where('role_id', $role->id)
                      ->where('menu_role.is_active', true);
            })
            ->where('is_active', true)
            ->whereNull('parent_id')
            ->with(['children' => function ($query) use ($role) {
                $query->whereHas('roles', function ($q) use ($role) {
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

        $this->ensureMenuDirectoryExists();
        $jsonString = json_encode($formattedMenus, JSON_PRETTY_PRINT);
        Storage::disk('local')->put($this->menuDirectory . '/role_' . $role->id . '.json', $jsonString);

        return true;
    }

    protected function ensureMenuDirectoryExists()
    {
        if (!Storage::disk('local')->exists($this->menuDirectory)) {
            Storage::disk('local')->makeDirectory($this->menuDirectory);
        }
    }

    protected function normalizeMenuArray(array $menus)
    {
        return array_map(function ($menu) {
            $menu['icon'] = $this->normalizeIcon($menu['icon'] ?? null);

            if (!empty($menu['children']) && is_array($menu['children'])) {
                $menu['children'] = $this->normalizeMenuArray($menu['children']);
            }

            return $menu;
        }, $menus);
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