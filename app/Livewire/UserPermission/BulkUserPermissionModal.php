<?php

namespace App\Livewire\UserPermission;

use App\Helpers\WorkFlowPermissionHelper;
use App\Models\Permission;
use App\Models\User;
use Livewire\Component;
use Spatie\Permission\PermissionRegistrar;

class BulkUserPermissionModal extends Component
{
    public $isOpen = false;

    public $selectedUserIds = [];

    public $permissions = [];

    public $selectedPermissions = [];

    public $schemeId = null;

    protected $listeners = [
        'open-bulk-assign-permission-modal'
        => 'open'
    ];

    public function mount()
    {
        $this->permissions =
            Permission::pluck('name', 'id')->toArray();
    }

    public function open($users, $schemeId = null)
    {
        if ($schemeId) {
            $this->schemeId = $schemeId;
        }

        $this->reset([
            'selectedPermissions',
            'selectedUserIds'
        ]);

        $this->selectedUserIds = $users;

        $this->isOpen = true;
    }

    public function close()
    {
        $this->reset([
            'selectedPermissions',
            'selectedUserIds'
        ]);

        $this->isOpen = false;

        $this->dispatch('assign-success');
    }

    public function save()
    {
        $schemeId = $this->schemeId ?? WorkFlowPermissionHelper::getSchemeId();

        if (!$schemeId) {
            session()->flash(
                'error',
                'Select scheme first.'
            );
            return;
        }

        if ($schemeId) {
            $schemeId = (int) $schemeId;
            app(PermissionRegistrar::class)
                ->setPermissionsTeamId($schemeId);

            foreach ($this->selectedUserIds as $userId) {
                $user = User::find($userId);
                if ($user) {
                    $user->givePermissionTo(
                        array_map('intval', (array) $this->selectedPermissions)
                    );
                }
            }
        }

        // $this->dispatch('toastr', [
        //     'type' => 'success',
        //     'message' => 'All applications verified successfully!'
        // ]);
        session()->flash(
            'success',
            'All applications verified successfully!'
        );

        $this->close();
    }

    public function remove()
    {
        $schemeId = $this->schemeId ?? WorkFlowPermissionHelper::getSchemeId();

        if (!$schemeId) {
            return;
        }

        if ($schemeId) {
            $schemeId = (int) $schemeId;
            app(PermissionRegistrar::class)
                ->setPermissionsTeamId($schemeId);

            foreach ($this->selectedUserIds as $userId) {
                $user = User::find($userId);
                if ($user) {
                    $user->revokePermissionTo(
                        array_map('intval', (array) $this->selectedPermissions)
                    );
                }
            }
        }

        $this->dispatch('toastr', [
            'type' => 'success',
            'message' => 'Permissions removed successfully!'
        ]);

        // session()->flash(
        //     'success',
        //     'Permissions removed successfully.'
        // );

        $this->close();
    }

    public function render()
    {
        return view(
            'livewire.user-permission.bulk-user-permission-modal'
        );
    }
}