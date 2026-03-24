<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Menu;
use App\Models\Permission;
use App\Models\Department;
use App\Models\Scheme;
use App\Models\Role;
use App\Helpers\MenuHelper;
use Illuminate\Support\Facades\DB;

class MenuManagement extends Component
{
    public $menus = [];
    public $permissions = [];
    public $departments = [];
    public $schemes = [];
    public $roles = [];

    public $selectedDepartments = [];
    public $selectedSchemes = [];
    public $selectedRoles = [];
    public $selectedPermissions = [];

    public $selectedMenu = null;
    public $isEditing = false;
    public $showModal = false;
    public $is_dependent = 'no';

    public $menu_name = '';
    public $icon = '';
    public $route = '';
    public $url = '';
    public $parent_id = '';
    public $menu_rank = 0;
    public $is_active = true;

    protected $rules = [
        'menu_name' => 'required',
        'menu_rank' => 'required|integer',
        'selectedDepartments' => 'required|array',
        'selectedSchemes' => 'required|array',
    ];

    public function mount()
    {
        $this->loadMenus();

        $this->permissions = Permission::all();
        $this->departments = Department::all();
        $this->roles = Role::all();
        $this->schemes = collect();
    }

    public function updatedSelectedDepartments()
    {
        $this->schemes = Scheme::whereIn(
            'department_id',
            $this->selectedDepartments
        )->get();

        $this->selectedSchemes = [];
    }

    public function updatedMenuName($value)
    {
        if ($this->is_dependent === 'yes' && !empty($value)) {
            $menu = Menu::where('menu_name', $value)->first();
            if ($menu) {
                $this->icon = $menu->icon;
                $this->route = $menu->route;
                $this->url = $menu->url;
                $this->parent_id = $menu->parent_id;
                $this->menu_rank = $menu->menu_rank;
                $this->is_active = $menu->is_active;

                $this->selectedDepartments = $menu->department_id ?? [];
                $this->updatedSelectedDepartments();
                $this->selectedSchemes = $menu->scheme_id ?? [];
                $this->selectedRoles = $menu->role_id ?? [];
                $this->selectedPermissions = $menu->permission_id ?? [];
            }
        }
    }

    public function loadMenus()
    {
        $this->menus = MenuHelper::getMenuTree();
    }

    public function createMenu()
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->showModal = true;
    }

    public function editMenu($id)
    {
        $menu = Menu::findOrFail($id);

        $this->selectedMenu = $menu;

        $this->menu_name = $menu->menu_name;
        $this->icon = $menu->icon;
        $this->route = $menu->route;
        $this->url = $menu->url;
        $this->parent_id = $menu->parent_id;
        $this->menu_rank = $menu->menu_rank;
        $this->is_active = $menu->is_active;

        $this->selectedDepartments = $menu->department_id ?? [];
        $this->updatedSelectedDepartments();
        $this->selectedSchemes = $menu->scheme_id ?? [];

        $this->selectedRoles = $menu->role_id ?? [];
        $this->selectedPermissions = $menu->permission_id ?? [];

        $this->isEditing = true;
        $this->showModal = true;
    }

    public function saveMenu()
    {
        $this->validate();

        DB::beginTransaction();

        try {

            $data = [
                'menu_name' => $this->menu_name,
                'icon' => $this->icon,
                'route' => $this->route,               
                'parent_id' => $this->parent_id ?: null,
                'menu_rank' => $this->menu_rank,
                'department_id' => $this->selectedDepartments,
                'scheme_id' => $this->selectedSchemes,
                'role_id' => $this->selectedRoles,
                'permission_id' => $this->selectedPermissions,
                'is_active' => $this->is_active,
            ];

            if ($this->isEditing) {
                $this->selectedMenu->update($data);
            } else {
                Menu::create($data);
            }

            $this->generateHelperAndRoute();

            DB::commit();

            MenuHelper::clearCache();
            $this->loadMenus();
            $this->resetForm();
            $this->showModal = false;

        } catch (\Exception $e) {
            DB::rollBack();
            dd($e->getMessage());
        }
    }

    private function resetForm()
    {
        $this->menu_name = '';
        $this->icon = '';
        $this->route = '';
        $this->url = '';
        $this->parent_id = '';
        $this->menu_rank = 0;
        $this->is_active = true;

        $this->selectedDepartments = [];
        $this->selectedSchemes = [];
        $this->selectedRoles = [];
        $this->selectedPermissions = [];
        $this->schemes = collect();
        $this->is_dependent = 'no';
    }

    private function generateHelperAndRoute()
    {
        try {
            if ($this->menu_name) {
                $permissionName = $this->menu_name;
                
                // Fetch existing permission name if mapped
                if (!empty($this->selectedPermissions)) {
                     $permId = is_array($this->selectedPermissions) ? $this->selectedPermissions[0] : $this->selectedPermissions;
                     $perm = \App\Models\Permission::find($permId);
                     if ($perm) {
                         $permissionName = $perm->name;
                     }
                } else {
                     // Auto create permission in db if no permission selected
                     if (class_exists(\App\Models\Permission::class)) {
                         \App\Models\Permission::firstOrCreate(['name' => $permissionName]);
                     }
                }

                // Generate method name based on the permission name, not the menu name
                $baseName = preg_replace('/[^a-zA-Z0-9]+/', ' ', $permissionName);
                $methodName = 'can' . \Illuminate\Support\Str::studly($baseName);

                $helperPath = app_path('Helpers/WorkFlowPermissionHelper.php');
                $methodExists = false;

                if (file_exists($helperPath)) {
                    $helperContent = file_get_contents($helperPath);
                    $permissionNameQuoted = preg_quote($permissionName, '/');
                    
                    // Check if any existing method already checks for this exact permission
                    $pattern = '/public static function\s+([A-Za-z0-9_]+)\(\)\s*:\s*bool\s*\{[^}]*can\([\'"]' . $permissionNameQuoted . '[\'"]\)[^}]*\}/i';
                    
                    if (preg_match($pattern, $helperContent, $matches)) {
                        $methodName = $matches[1];
                        $methodExists = true;
                    } elseif (strpos($helperContent, "function {$methodName}(") !== false) {
                        // The generated method name already exists
                        $methodExists = true;
                    }

                    // Auto update WorkFlowPermissionHelper
                    if (!$methodExists) {
                        $helperContent = preg_replace('/}[ \n\t\r]*$/', '', $helperContent);
                        $newMethod = "\n    public static function {$methodName}(): bool\n    {\n        return Auth::user() && Auth::user()->can('{$permissionName}');\n    }\n}\n";
                        file_put_contents($helperPath, $helperContent . $newMethod);
                    }
                }

                // Auto update routes/web.php
                if (!empty($this->route) && !empty($this->url) && $this->route !== '#') {
                    $routePath = base_path('routes/web.php');
                    if (file_exists($routePath)) {
                        $routeContent = file_get_contents($routePath);
                        if (strpos($routeContent, "->name('{$this->route}')") === false) {
                            $url = ltrim($this->url, '/');
                            $newRoute = "\n// Auto-generated route for {$this->menu_name}\n";
                            $newRoute .= "Route::middleware(['auth', 'verified'])->group(function () {\n";
                            $newRoute .= "    Route::get('/{$url}', \App\Livewire\ApplicationView::class) // TODO: Update to correct Component Class\n";
                            $newRoute .= "        ->middleware('permission.redirect:{$methodName}')\n";
                            $newRoute .= "        ->name('{$this->route}');\n";
                            $newRoute .= "});\n";
                            
                            file_put_contents($routePath, $routeContent . $newRoute);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Menu auto string generation error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $parentMenus = Menu::whereNull('parent_id')->get();

        return view('livewire.menu-management', compact('parentMenus'));
    }
}
