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

    // form

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

    ];

    public function mount()
    {
        $this->loadMenus();

        $this->permissions = Permission::all();

        $this->departments =Department::with('schemes')->where('id',1)->get();
        // dd($this->departments);
        $this->schemes = Scheme::all();
        $this->roles = Role::all();
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

        // JSON Load

        $this->selectedDepartments =
            $menu->department_id ?? [];

        $this->selectedSchemes =
            $menu->scheme_id ?? [];

        $this->selectedRoles =
            $menu->role_id ?? [];

        $this->selectedPermissions =
            $menu->permission_id ?? [];

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
                'url' => $this->url,

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
    }

    public function render()
    {
        $parentMenus = Menu::whereNull('parent_id')->get();

        return view(
            'livewire.menu-management',
            compact('parentMenus')
        );
    }
}