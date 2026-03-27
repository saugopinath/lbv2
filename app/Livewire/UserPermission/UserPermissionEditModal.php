<?php

namespace App\Livewire\UserPermission;

use App\Helpers\WorkFlowPermissionHelper;
use App\Models\Permission;
use App\Models\User;
use Livewire\Component;
use Spatie\Permission\PermissionRegistrar;

class UserPermissionEditModal extends Component
{
    public $isOpen = false;

    public $userId;

    public $userName;

    public $permissions = [];

    public $selectedPermissions = [];

    public $schemeId = null;

    protected $listeners = [
        'UpdatePermission' => 'open'
    ];

    public function open($userId, $schemeId = null)
    {
        if ($schemeId) {
            $this->schemeId = $schemeId;
        }

        $this->resetValidation();
        $this->resetExcept(['isOpen']);

        $this->userId = $userId;

        $user = User::findOrFail($userId);

        $this->userName = $user->name;

        $this->permissions =
            Permission::pluck('name', 'id')->toArray();

        // Scheme respected load
        $schemeId = $this->schemeId ?? WorkFlowPermissionHelper::getSchemeId();

        if ($schemeId) {
            app(PermissionRegistrar::class)
                ->setPermissionsTeamId($schemeId);

            $this->selectedPermissions = $user->mappedPermissions()
                ->wherePivot('scheme_id', $schemeId)
                ->pluck('permissions.id')
                ->toArray();
        } else {
            $this->selectedPermissions =
                $user->permissions->pluck('id')->toArray();
        }

        $this->isOpen = true;
    }

    public function updateUserPermission()
    {
        $schemeId = $this->schemeId ?? WorkFlowPermissionHelper::getSchemeId();

        if (!$schemeId) {
            session()->flash(
                'error',
                'Please select scheme first.'
            );
            return;
        }

        $user = User::find($this->userId);

        app(PermissionRegistrar::class)
            ->setPermissionsTeamId($schemeId);

        $user->syncPermissions(
            array_map('intval', (array)$this->selectedPermissions)
        );

        // session()->flash(
        //     'success',
        //     'Permission updated successfully.'
        // );
        $this->dispatch('toastr', [
            'type' => 'success',
            'message' => 'Permission updated successfully!'
        ]);

        $this->close();
    }

    public function close()
    {
        $this->reset([
            'isOpen',
            'userId',
            'userName',
            'permissions',
            'selectedPermissions'
        ]);
    }

    public function render()
    {
        return view(
            'livewire.user-permission.user-permission-edit-modal'
        );
    }
}