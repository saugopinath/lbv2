<?php

namespace App\Livewire\RolePermissionManagement;

use App\Models\Role;
use Livewire\Component;
use App\Attributes\Loggable;

class RoleCreateModal extends Component
{

    public $name;

    public function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
        ];
        return $rules;
    }
    public function messages()
    {
        return [
            'name.required' => 'The permission name is required.',

        ];
    }
    #[Loggable(level: 'C', nickname: 'New Role Create')]
    public function save()
    {
        $this->dispatch('showLoader');
        $this->validate();

        $role = Role::create([
            'name'       => $this->name
        ]);
        // dd([
        //             'name'       => $this->name,
        //             'is_parent'  => $this->is_parent,
        //             'parent_id'  => $this->parent_id,
        //         ]);
        $this->reset(['name']);
        $this->dispatch('close-modal');
        $this->dispatch('hideLoader');
        // $this->dispatch('notify', 'Permission created successfully!', 'success');
        $this->dispatch('toastr', [
            'type' => 'success',
            'message' => 'Role created successfully!'
        ]);
        $this->dispatch('refreshDatatable');
    }
    public function cancel()
    {
        $this->reset(['name']);
        $this->dispatch('close-modal');
    }
    public function render()
    {
        return view('livewire.role-permission-management.role-create-modal');
    }
}
