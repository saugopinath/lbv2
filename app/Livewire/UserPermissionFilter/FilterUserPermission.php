<?php

namespace App\Livewire\UserPermissionFilter;

use App\Models\District;
use App\Models\OfficeMaster;
use App\Models\Role;
use App\Models\RoleOfficeTypeMapping;
use App\Models\Scheme;
use App\Models\State;
use Livewire\Component;

class FilterUserPermission extends Component
{
    public $role, $mapping_level, $selectscheme, $office, $selectedMappingLevel, $selectedState, $scheme, $selectedDistrict, $Role, $role_id, $ml;

    public $roles = [], $schemes = [], $offices = [], $states = [], $mapping_levels = [], $districts = [];

    public function mount()
    {
        $this->roles = Role::all();
        $this->schemes = Scheme::all();
        $this->states = State::where('is_active', 1)->where('lgd_code',  19)->get();
        // $this->districts = District::orderBy('name', 'asc')->get();
    }
    public function updatedRole($value)
    {
        $this->mapping_level = null;
        $this->mapping_levels = [];
        $this->office = null;
        $this->offices = [];
        $this->selectedMappingLevel = null;
        $this->selectedDistrict = null;
        $this->selectedState = null;


        if ($value) {

            $this->mapping_levels = RoleOfficeTypeMapping::with('officeType')
                ->where('role_id', $value)
                ->whereHas('officeType', function ($q) {
                    $q->whereIn('code', [151, 152, 153, 154]);
                })
                ->get()
                ->unique('office_type_id');
        }

        if (!in_array($value, [153, 154])) {
            $this->selectedDistrict = null;
        }
    }

    public function updatedSelectedMappingLevel($value)
    {
        $this->office = null;
        $this->offices = [];
        $this->ml = $value;
        if ($value) {
            $this->offices = OfficeMaster::where('office_type_id', $value)->get();
        }

        if (!in_array($value, [153, 154])) {
            $this->selectedDistrict = null;
        }
    }
    public function updatedSelectedState($value)
    {
        $this->selectedDistrict = null;
        $this->districts = [];

        if ($value) {
            $this->districts = District::where('state_id', $value)->orderBy('name', 'asc')->get();
        }
    }
    public function updatedSelectedDistrict($value)
    {
        if ($value) {
            $this->offices = OfficeMaster::where('office_type_id', $this->ml)->where('district_id', $value)->get();
        }
    }

    public function applyFilters()
    {
        $this->dispatch('userFilter', [
            'role' => $this->role,
            'mapping_level' => $this->selectedMappingLevel,
            'state' => $this->selectedState,
            'district' => $this->selectedDistrict,
            'office' => $this->office,
        ]);
    }

    public function resetFilters()
    {
        $this->role = null;
        $this->selectedMappingLevel = null;
        $this->selectedState = null;
        $this->selectedDistrict = null;
        $this->office = null;
        $this->districts = [];
        $this->offices = [];

        $this->dispatch('userFilter', []);
    }

    public function render()
    {
        $this->dispatch('hideLoader');
        return view('livewire.user-permission-filter.filter-user-permission');
    }
}
