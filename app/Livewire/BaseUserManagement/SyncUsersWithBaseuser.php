<?php

namespace App\Livewire\BaseUserManagement;

use App\Models\Permission;
use App\Models\User;
use App\Models\UserRoleSchemeOfficeMapping;
use Livewire\Component;

class SyncUsersWithBaseuser extends Component
{

    public $isOpen = false;
    public $userId;
    public $userName;
    public $baseuserrole;
    public $selectedPermissions = [];
    public $duplicateMessages = [];

    protected $listeners = ['syncpermission' => 'open'];

    public function open($userId)
    {
        // dd($userId);
        $this->resetValidation();
        $this->resetExcept(['isOpen']);

        $this->userId = $userId;
        $this->userName = User::findOrFail($userId)->name;
        $this->baseuserrole = User::findOrFail($userId)->RoleSchemeOfficeMappings->pluck('role_id');
        // $this->permissions = Permission::pluck('name', 'id')->toArray();
        $this->selectedPermissions = User::findOrFail($userId)->permissions->pluck('id')->toArray();
        $this->isOpen = true;
    }

    public function syncbaseuserpermission()
    {
        $user = User::find($this->userId);

        if (!$user) {
            $this->dispatch('notify', ['message' => 'User not found!']);
            $this->isOpen = false;
            return;
        }
        $baseuserrole =  $this->baseuserrole;
        $selectedPermissions = $this->selectedPermissions;
        // $assignedPermissions = Permission::whereIn('id', $this->selectedPermissions)->get();
        $syncUserIds = UserRoleSchemeOfficeMapping::whereIn('role_id', $baseuserrole)
            ->pluck('user_id');


        //     foreach ($syncUserIds as $userId) {
        //     $user = User::find($userId);
        //     foreach ($selectedPermissions as $permissionId) {
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
        // foreach ($syncUserIds as $userId) {
        //     $user = User::find($userId);
        //     $user->syncPermissions(Permission::whereIn('id', $selectedPermissions)->get());
        // } 
        $this->close();
        $this->dispatch('assign-success'); 
        session()->flash('success', 'Permissions updated for selected users successfully.');
        // $user->syncPermissions(Permission::whereIn('id', $this->selectedPermissions)->get());
        // $this->close();
        // $this->dispatch('refreshUserTable');
        // $this->dispatch('notify', ['message' => 'Permissions updated successfully!']);
    }
    public function close()
    {
        $this->reset(['isOpen', 'userId', 'userName', 'selectedPermissions']);
    }
    public function render()
    {
        return view('livewire.base-user-management.sync-users-with-baseuser');
    }
}
