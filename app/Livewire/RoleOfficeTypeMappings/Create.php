<?php

namespace App\Livewire\RoleOfficeTypeMappings;

use Livewire\Component;
use App\Models\Role;
use App\Models\Codemaster;
use App\Models\RoleOfficeTypeMapping;
use Masmerise\Toaster\Toaster;

class Create extends Component
{
    public $roles = [], $mapping_levels = [];

    public $role, $selectedMappingLevel;

    protected $rules = [
        'role' => 'required|exists:roles,id',
        'selectedMappingLevel' => 'required|exists:codemasters,code',
    ];

    protected $messages = [
        'role.required' => 'Please select a role',
        'selectedMappingLevel.required' => 'Please select a mapping level',
    ];

    public function mount()
    {
        $officetype = Codemaster::getIdByCode(15);
        $this->roles = Role::all();
        $this->mapping_levels = Codemaster::where('parent_id', $officetype)->get();
    }

    public function submit()
    {
        $this->validate();

        RoleOfficeTypeMapping::create([
            'role_id' => $this->role,
            'office_type_id' => $this->selectedMappingLevel,
        ]);

        // Toaster::success('Role Office Type Mapping created successfully!');
         session()->flash('success', 'Role Office Type Mapping created successfully!');

        return redirect()->route('role-office-master-mappings.index');
        // $this->dispatch('redirectToIndex');
    }

    public function render()
    {
        return view('livewire.role-office-type-mappings.create');
    }
}
