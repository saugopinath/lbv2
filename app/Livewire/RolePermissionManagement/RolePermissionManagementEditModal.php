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
    public string $permissionSearch = '';
    public $copyRoleId = '';
    public array $rolesList = [];

    protected $listeners = ['UpdateRolePermission' => 'open'];

    public function open($roleId)
    {
        $this->resetValidation();
        $this->resetExcept(['isOpen']);

        // 1 single query for all roles: extracts active role & dropdown list in memory (0 duplicate queries)
        $allRoles = Role::select('id', 'name')->orderBy('name')->get();
        $role = $allRoles->firstWhere('id', (int) $roleId);

        if (!$role) {
            $this->dispatch('toastr', [
                'type'    => 'error',
                'message' => 'Role not found.'
            ]);
            return;
        }

        $this->roleId = $role->id;
        $this->roleName = $role->name;
        $this->rolesList = $allRoles->where('id', '!=', $this->roleId)->pluck('name', 'id')->toArray();

        // Fetch permissions with required columns only
        $this->permissions = Permission::select('id', 'name')->orderBy('name')->pluck('name', 'id')->toArray();

        // Direct lightweight pivot query for currently assigned permission IDs
        $this->selectedPermissions = DB::table('role_has_permissions')
            ->where('role_id', $this->roleId)
            ->pluck('permission_id')
            ->map(fn($id) => (string) $id)
            ->toArray();

        $this->permissionSearch = '';
        $this->copyRoleId = '';
        $this->isOpen = true;
    }

    /**
     * Filter permissions in memory using substring matching (0 DB queries).
     */
    public function getFilteredPermissions()
    {
        $search = trim($this->permissionSearch);
        if ($search === '') {
            return $this->permissions;
        }

        return array_filter($this->permissions, function ($name) use ($search) {
            return stripos($name, $search) !== false;
        });
    }

    /**
     * Copy all permissions from selected role to replace current selection.
     */
    public function copyRolePermissions()
    {
        if (empty($this->copyRoleId)) {
            $this->dispatch('toastr', [
                'type'    => 'warning',
                'message' => 'Please select a role to copy permissions from.',
            ]);
            return;
        }

        $sourceRole = Role::select('id', 'name')->find($this->copyRoleId);
        if (!$sourceRole) {
            $this->dispatch('toastr', [
                'type'    => 'error',
                'message' => 'Selected role not found.',
            ]);
            return;
        }

        $permissionIds = DB::table('role_has_permissions')
            ->where('role_id', $this->copyRoleId)
            ->pluck('permission_id')
            ->map(fn($id) => (string) $id)
            ->toArray();

        $this->selectedPermissions = $permissionIds;

        $count = count($permissionIds);
        $this->dispatch('toastr', [
            'type'    => 'success',
            'message' => "Copied {$count} permission(s) from role '{$sourceRole->name}'.",
        ]);
    }

    #[Loggable(level: 'C', nickname: 'Role Permission Management')]
    public function updateRolePermission()
    {
        $role = Role::select('id', 'name', 'guard_name', 'updated_at')->find($this->roleId);

        if (!$role) {
            $this->dispatch('notify', ['message' => 'Role not found!']);
            $this->dispatch('toastr', [
                'type'    => 'error',
                'message' => 'Role not found.'
            ]);
            $this->isOpen = false;
            return;
        }

        // Load permissions relationship with required columns only
        $role->load('permissions:id,name,guard_name');

        // Selected permissions (array of int ids) coming from UI
        $selectedIds = array_map('intval', array_filter((array) $this->selectedPermissions));

        // Current permission ids already attached to the role
        $currentIds = $role->permissions->pluck('id')->toArray();

        // Permission ids need to be added to the role
        $toAdd = array_values(array_diff($selectedIds, $currentIds));

        // Permission ids need to be removed from the role
        $toRemove = array_values(array_diff($currentIds, $selectedIds));

        try {
            // Capture old permissions for the Audit Log
            $role->audit_old_permissions = $role->permissions->pluck('name')->toArray();

            // CASE A: role has no permissions before
            if (empty($currentIds) && !empty($selectedIds)) {
                $permissions = Permission::select('id', 'name', 'guard_name')->whereIn('id', $selectedIds)->get();
                $role->givePermissionTo($permissions);
                $usersWithRole = $role->users()->select('users.id')->get();
                if ($usersWithRole->isNotEmpty()) {
                    foreach ($usersWithRole as $user) {
                        $user->givePermissionTo($permissions);
                    }
                }
            } else {
                if (!empty($toAdd)) {
                    $permissionsToAdd = Permission::select('id', 'name', 'guard_name')->whereIn('id', $toAdd)->get();
                    $role->givePermissionTo($permissionsToAdd);
                    $usersWithRole = $role->users()->select('users.id')->get();
                    if ($usersWithRole->isNotEmpty()) {
                        foreach ($usersWithRole as $user) {
                            $user->givePermissionTo($permissionsToAdd);
                        }
                    }
                }
                if (!empty($toRemove)) {
                    $permissionsToRemove = Permission::select('id', 'name', 'guard_name')->whereIn('id', $toRemove)->get();
                    $role->revokePermissionTo($permissionsToRemove);
                    $usersWithRole = $role->users()->select('users.id')->get();
                    if ($usersWithRole->isNotEmpty()) {
                        foreach ($usersWithRole as $user) {
                            $user->revokePermissionTo($permissionsToRemove);
                        }
                    }
                }
            }

            // Force update timestamp to trigger audit trail
            $role->updated_at = now();
            $role->save();

            $this->close();
            $this->dispatch('refreshDatatable');
            $this->dispatch('toastr', [
                'type'    => 'success',
                'message' => 'Permissions successfully assigned to the Role'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('toastr', [
                'type'    => 'error',
                'message' => 'Failed to Assign Permissions'
            ]);
        }
    }
    public function close()
    {
        $this->reset(['isOpen', 'roleId', 'roleName', 'permissions', 'selectedPermissions', 'permissionSearch', 'copyRoleId', 'rolesList']);
        $this->dispatch('refreshDatatable');
    }
    public function render()
    {
        return view('livewire.role-permission-management.role-permission-management-edit-modal');
    }
}
