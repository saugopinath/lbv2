<?php

namespace App\Livewire\UserPermission;

use App\Models\Permission;
use App\Models\User;
use Livewire\Component;

class AssignPermissionsPage extends Component
{
    public $users = [];
    public $permissions = [];
    public $allUsers = [];
    public $allPermissions = [];

    public function mount()
    {
        // dd('test');
        $this->allUsers = User::all();
        $this->allPermissions = Permission::all();
    }

    public function saveUserPermission()
    {
        $this->validate([
            'users' => 'required|array|min:1',
            'permissions' => 'required|array|min:1',
        ]);
        foreach ($this->users as $userId) {
            $user = User::find($userId);
            if ($user) {
                $user->syncPermissions(Permission::whereIn('id', $this->permissions)->get());
            }
        }
        $this->users = [];
        $this->permissions = [];
        $this->dispatch('toastr', [
                        'type' => 'success',
                        'message' => 'Permissions created successfully!']);
    }

    public function render()
    {
        return view('livewire.user-permission.assign-permissions-page');
    }
}
