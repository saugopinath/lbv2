<?php

namespace App\Livewire\UserPermission;

use App\Models\Permission;
use App\Models\User;
use Livewire\Component;

class UserPermissionEditModal extends Component

    {
    public $isOpen = false;
    public $userId;
    public $userName;
    public $permissions = [];
    public $selectedPermissions = [];

    protected $listeners = ['UpdatePermission' => 'open'];

    public function open($userId)
    {
        // dd($userId);
        $this->resetValidation();
        $this->resetExcept(['isOpen']);

        $this->userId = $userId;
        $this->userName = User::findOrFail($userId)->name;
        $this->permissions = Permission::pluck('name', 'id')->toArray();
        $this->selectedPermissions = User::findOrFail($userId)->permissions->pluck('id')->toArray();
        $this->isOpen = true;
    }

    public function updateUserPermission()
    {
        $user = User::find($this->userId);

        if (!$user) {
            $this->dispatch('notify', ['message' => 'User not found!']);
            $this->isOpen = false;
            return;
        }
        $user->syncPermissions(Permission::whereIn('id', $this->selectedPermissions)->get());
        $this->close();
        $this->dispatch('toastr', [
                        'type' => 'success',
                        'message' => 'Permissions Assign successfully!']);
        $this->dispatch('refreshUserTable');
    }
    public function close()
    {
         $this->reset(['isOpen', 'userId', 'userName', 'permissions', 'selectedPermissions']);
    }
    public function render()
    {
        return view('livewire.user-permission.user-permission-edit-modal');

    }
}
