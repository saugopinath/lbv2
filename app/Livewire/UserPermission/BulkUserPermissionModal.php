<?php

namespace App\Livewire\UserPermission;

use App\Models\User;
use App\Models\Permission;
use Livewire\Component;
use App\Attributes\Loggable;

class BulkUserPermissionModal extends Component
{
    public $isOpen = false;
    public $selectedUserIds = [];
    public $permissions = [];
    public $selectedPermissions = [];
    public $duplicateMessages = [];

    protected $listeners = ['open-bulk-assign-permission-modal' => 'open'];

    public function mount()
    {
        $this->permissions = Permission::pluck('name', 'id')->toArray();
    }

    public function open($users)
    {
        $this->reset(['selectedPermissions', 'selectedUserIds']);
        $this->selectedUserIds = $users;
        $this->isOpen = true;
    }


    public function close()
    {
        $this->reset(['selectedPermissions', 'selectedUserIds']);
        $this->isOpen = false;
        $this->dispatch('assign-success');
    }
    #[Loggable(level: 'C', nickname: 'Assign Bulk Permission ')]
    public function save()
    {
        if (empty($this->selectedUserIds)) {
            $this->addError('selectedUserIds', 'Please select at least one user.');
            return;
        }
        $users = User::whereIn('id', $this->selectedUserIds)->get();
        // $duplicateMessages = [];
        // foreach ($users as $user) {
        //     $user->syncPermissions(Permission::whereIn('id', $this->selectedPermissions)->get());
        // } 
        // foreach ($users as $user) {
        //     foreach ($this->selectedPermissions as $permissionId) {
        //         $permission = Permission::find($permissionId);

        //         if ($user->hasPermissionTo($permission->name)) {
        //             $duplicateMessages[] = "{$user->name} already has {$permission->name}";
        //         }
        //     }
        // }
        // if (!empty($duplicateMessages)) {
        //     $this->duplicateMessages = $duplicateMessages;
        //     return;
        // }
        $permissions = Permission::whereIn('id', $this->selectedPermissions)->get();
        foreach ($users as $user) {
            // ⭐ Capture old permissions for the Audit Log
            $user->audit_old_permissions = $user->permissions->pluck('name')->toArray();

            $user->givePermissionTo($permissions);

            // ⭐ Force update and save to trigger audit trail
            $user->updated_at = now();
            $user->save();
        }
        $this->close();
        $this->dispatch('toastr', [
            'type' => 'success',
            'message' => 'Permissions Assigned successfully!'
        ]);
    }
    #[Loggable(level: 'C', nickname: 'Remove Bulk Permission')]
    public function remove()
    {
        if (empty($this->selectedUserIds)) {
            $this->addError('selectedUserIds', 'Please select at least one user.');
            return;
        }
        $users = User::whereIn('id', $this->selectedUserIds)->get();
        foreach ($users as $user) {
            // ⭐ Capture old permissions for the Audit Log
            $user->audit_old_permissions = $user->permissions->pluck('name')->toArray();

            foreach ($this->selectedPermissions as $permissionId) {
                $permission = Permission::find($permissionId);

                if ($user->hasPermissionTo($permission->name)) {
                    $user->revokePermissionTo($permission->name);
                }
            }

            // ⭐ Force update and save to trigger audit trail
            $user->updated_at = now();
            $user->save();
        }

        $this->close();
        $this->dispatch('toastr', [
            'type' => 'success',
            'message' => 'Permissions Removed successfully!'
        ]);
    }
    public function render()
    {
        return view('livewire.user-permission.bulk-user-permission-modal');
    }
}
