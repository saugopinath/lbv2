<?php

namespace App\Livewire\RolePermissionManagement;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use App\Attributes\Loggable;

class RoleCreateModal extends Component
{
    public $name;
    public $copyRoleId = '';
    public array $rolesList = [];

    public function rules()
    {
        $rules = [
            'name'       => 'required|string|max:255',
            'copyRoleId' => 'nullable|exists:roles,id',
        ];
        return $rules;
    }

    public function messages()
    {
        return [
            'name.required' => 'The role name is required.',
        ];
    }

    #[Loggable(level: 'C', nickname: 'New Role Create')]
    public function save()
    {
        $this->dispatch('showLoader');
        $this->validate();

        $role = Role::create([
            'name' => $this->name
        ]);

        if (!empty($this->copyRoleId)) {
            $permissionIds = DB::table('role_has_permissions')
                ->where('role_id', $this->copyRoleId)
                ->pluck('permission_id')
                ->toArray();

            if (!empty($permissionIds)) {
                $permissions = Permission::select('id', 'name', 'guard_name')
                    ->whereIn('id', $permissionIds)
                    ->get();
                $role->givePermissionTo($permissions);
            }
        }

        $this->reset(['name', 'copyRoleId']);
        $this->dispatch('close-modal');
        $this->dispatch('hideLoader');
        $this->dispatch('toastr', [
            'type'    => 'success',
            'message' => 'Role created successfully!'
        ]);
        $this->dispatch('refreshDatatable');
    }

    public function cancel()
    {
        $this->reset(['name', 'copyRoleId']);
        $this->dispatch('close-modal');
    }

    public function render()
    {
        $this->rolesList = Role::select('id', 'name')
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
        return view('livewire.role-permission-management.role-create-modal');
    }
}

