<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\RoleMenuUserMapping;
use App\Models\Menu;
use App\Models\Role;
use App\Models\Scheme;
use App\Models\Department;
use App\Models\Permission;

class RoleMenuMappingManagement extends Component
{
    public $menus = [];
    public $roles = [];
    public $schemes = [];
    public $departments = [];
    public $permissions = [];

    public $showModal = false;
    public $isEditing = false;
    public $mapping_id = null;

    public $menu_id = '';
    public $role_id = '';
    public $scheme_id = '';
    public $department_id = '';
    public $permission_id = '';

    protected $listeners = [
        'editMapping' => 'editMapping',
    ];

    protected $rules = [
        'menu_id' => 'required',
    ];

    public function mount()
    {
        $this->menus = Menu::all();
        $this->roles = Role::all();
        $this->schemes = Scheme::all();
        $this->departments = Department::all();
        $this->permissions = Permission::all();
    }

    public function createMapping()
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->showModal = true;
    }

    public function editMapping($id)
    {
        $mapping = RoleMenuUserMapping::findOrFail($id);

        $this->mapping_id = $mapping->id;
        $this->menu_id = $mapping->menu_id;
        $this->role_id = $mapping->role_id;
        $this->scheme_id = $mapping->scheme_id;
        $this->department_id = $mapping->department_id;
        $this->permission_id = $mapping->permission_id;

        $this->isEditing = true;
        $this->showModal = true;
    }

    public function saveMapping()
    {
        $this->validate();

        $data = [
            'menu_id' => $this->menu_id,
            'role_id' => $this->role_id ?: null,
            'scheme_id' => $this->scheme_id ?: null,
            'department_id' => $this->department_id ?: null,
            'permission_id' => $this->permission_id ?: null,
        ];

        if ($this->isEditing) {
            $mapping = RoleMenuUserMapping::findOrFail($this->mapping_id);
            $mapping->update($data);
        } else {
            RoleMenuUserMapping::create($data);
        }

        $this->showModal = false;
        $this->dispatch('refreshDatatable');
    }

    private function resetForm()
    {
        $this->mapping_id = null;
        $this->menu_id = '';
        $this->role_id = '';
        $this->scheme_id = '';
        $this->department_id = '';
        $this->permission_id = '';
    }

    public function render()
    {
        return view('livewire.role-menu-mapping-management');
    }
}
