<?php

namespace App\Livewire\RolePermissionManagement;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Spatie\Permission\PermissionRegistrar;
use App\Attributes\Loggable;

class RolePermissionManagementEditModal extends Component
{

    public $isOpen = false;
    public $roleId;
    public $roleName;
    public $permissions = [];
    public $selectedPermissions = [];

    protected $listeners = ['UpdateRolePermission' => 'open'];

    public function open($roleId)
    {
        // dd($userId);
        $this->resetValidation();
        $this->resetExcept(['isOpen']);
        $role = Role::findOrFail($roleId);
        $this->roleId = $role->id;
        $this->roleName = $role->name;
        $this->permissions = Permission::pluck('name', 'id')->toArray();
        $this->selectedPermissions = $role->permissions->pluck('id')->toArray();
        $this->isOpen = true;
    }
    #[Loggable(level: 'C', nickname: 'Role Permission Management')]
    public function updateRolePermission()
    {
        $role = Role::find($this->roleId);

        if (!$role) {
            $this->dispatch('notify', ['message' => 'Role not found!']);
            $this->dispatch('toastr', [
                'type' => 'success',
                'message' => 'Role created successfully!'
            ]);
            $this->isOpen = false;
            return;
        }
        // $role->syncPermissions(Permission::whereIn('id', $this->selectedPermissions)->get());
        // $this->close();
        // $this->dispatch('refreshDatatable');
        // $this->dispatch('notify', ['message' => 'Permissions updated successfully!']);
        // Selected permissions (array of ids) coming from UI
        $selectedIds = is_array($this->selectedPermissions) ? $this->selectedPermissions : [];
        // dd($selectedIds);
        // Current permission ids already attached to the role
        $currentIds = $role->permissions->pluck('id')->toArray();
        // dd($currentIds);
        // permission ids need to be added to the role
        $toAdd = array_values(array_diff($selectedIds, $currentIds));
        // dd($toAdd);
        // permission ids need to be removed from the role (optional)
        $toRemove = array_values(array_diff($currentIds, $selectedIds));
        // dd($toRemove);
        // DB::beginTransaction();
        try {
            // ⭐ Capture old permissions for the Audit Log
            $role->audit_old_permissions = $role->permissions->pluck('name')->toArray();

            // CASE A: role has no permissions before
            if (empty($currentIds) && !empty($selectedIds)) {
                // dd('here');
                // Add all selected permissions to role
                $permissions = Permission::whereIn('id', $selectedIds)->get();
                $role->givePermissionTo($permissions);
                $usersWithRole = $role->users()->get();
                // dd($usersWithRole);
                if ($usersWithRole->isNotEmpty()) {
                    // chunking for safety if there are many users
                    foreach ($usersWithRole as $user) {
                        // direct assign permission to each user
                        $user->givePermissionTo($permissions);
                    }
                }
            } else {
                // dd('there');
                if (!empty($toAdd)) {
                    // dd($toAdd);
                    $permissionsToAdd = Permission::whereIn('id', $toAdd)->get();
                    // dd($permissionsToAdd);
                    $role->givePermissionTo($permissionsToAdd);
                    $usersWithRole = $role->users()->get();
                    // dd($usersWithRole);
                    if ($usersWithRole->isNotEmpty()) {
                        foreach ($usersWithRole as $user) {
                            // direct assign permission to each user
                            $user->givePermissionTo($permissionsToAdd);
                        }
                    }
                }
                if (!empty($toRemove)) {
                    $permissionsToRemove = Permission::whereIn('id', $toRemove)->get();
                    $role->revokePermissionTo($permissionsToRemove);
                    $usersWithRole = $role->users()->get();
                    // dd($usersWithRole);
                    if ($usersWithRole->isNotEmpty()) {
                        foreach ($usersWithRole as $user) {
                            // direct assign permission to each user
                            $user->revokePermissionTo($permissionsToRemove);
                        }
                    }
                }
            }
            // ⭐ Force update timestamp to trigger audit trail
            $role->updated_at = now();
            $role->save();

            // clear permission cache so changes reflect immediately
            // app(PermissionRegistrar::class)->forgetCachedPermissions();
            // DB::commit();
            $this->close();
            $this->dispatch('refreshDatatable');
            $this->dispatch('toastr', [
                'type' => 'success',
                'message' => 'Permission successfully assignd to the Role'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            // log or send error message
            $this->dispatch('toastr', [
                'type' => 'error',
                'message' => 'Failed to Assign Permission'
            ]);
            // $this->dispatch('notify', ['message' => 'Update failed: ' . $e->getMessage()]);
        }
    }
    public function close()
    {
        $this->reset(['isOpen', 'roleId', 'roleName', 'permissions', 'selectedPermissions']);
        $this->dispatch('refreshDatatable');
    }
    public function render()
    {
        return view('livewire.role-permission-management.role-permission-management-edit-modal');
    }
}
