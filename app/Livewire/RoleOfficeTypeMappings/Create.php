<?php

namespace App\Livewire\RoleOfficeTypeMappings;

use Livewire\Component;
use App\Models\Role;
use App\Models\Codemaster;
use App\Models\RoleOfficeTypeMapping;
use Illuminate\Support\Facades\DB;

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
        $this->roles = Role::all();
        $officetype = Codemaster::getIdByCode(15);
        $this->mapping_levels = Codemaster::where('parent_id', $officetype)->whereIn('code', [151, 152, 153, 154])->get();
    }

    public function submit()
    {
        $this->validate();
        try {
            DB::beginTransaction();

            RoleOfficeTypeMapping::create([
                'role_id' => $this->role,
                'office_type_id' => $this->selectedMappingLevel,
            ]);

            DB::commit();

            session()->flash('success', 'Role Office Type Mapping created successfully!');
            return redirect()->route('role-office-master-mappings');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage())->withInput();
        }
    }
    public function updateReset()
    {
        $this->reset(['role', 'selectedMappingLevel']);
    }
    public function render()
    {
        return view('livewire.role-office-type-mappings.create');
    }
}
