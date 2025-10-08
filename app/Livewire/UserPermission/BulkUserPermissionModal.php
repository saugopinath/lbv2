<?php

namespace App\Livewire\UserPermission;

use App\Models\User;
use Spatie\Permission\Models\Permission;
use Livewire\Component;
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
    $this->reset(['selectedPermissions', 'selectedUserIds', 'duplicateMessages']);
    $this->selectedUserIds = $users;
    $this->isOpen = true;
}


    public function close()
    {
        $this->reset(['selectedPermissions', 'selectedUserIds', 'duplicateMessages']);
        $this->isOpen = false;
        $this->dispatch('assign-success'); 
    }

    public function save()
    {
         if (empty($this->selectedUserIds)) {
            $this->addError('selectedUserIds', 'Please select at least one user.');
            return;
        }
        $users = User::whereIn('id', $this->selectedUserIds)->get();
        $duplicateMessages = [];
        // foreach ($users as $user) {
        //     $user->syncPermissions(Permission::whereIn('id', $this->selectedPermissions)->get());
        // } 
        foreach ($users as $user) {
            foreach ($this->selectedPermissions as $permissionId) {
                $permission = Permission::find($permissionId);

                if ($user->hasPermissionTo($permission->name)) {
                    $duplicateMessages[] = "{$user->name} already has {$permission->name}";
                }
            }
        }
        if (!empty($duplicateMessages)) {
            $this->duplicateMessages = $duplicateMessages;
            return;
        }
        foreach ($users as $user) {
            $user->syncPermissions(Permission::whereIn('id', $this->selectedPermissions)->get());
        } 
        $this->close();
        $this->dispatch('assign-success'); 
        session()->flash('success', 'Permissions updated for selected users successfully.');
    }
    public function render()
    {
        return view('livewire.user-permission.bulk-user-permission-modal');
    }
}
