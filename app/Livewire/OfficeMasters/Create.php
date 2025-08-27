<?php

namespace App\Livewire\OfficeMasters;

use App\Models\State;
use Livewire\Component;
use App\Models\Codemaster;
use App\Models\OfficeMaster;
use Masmerise\Toaster\Toaster;

class Create extends Component
{
    public $name, $address, $zip, $selectedMappingLevel, $selectedState;

    public $mapping_levels = [], $states = [];

    public function mount()
    {
        $officetype = Codemaster::getIdByCode(15);
        $this->states = State::orderBy('id', 'asc')->get();
        $this->mapping_levels = Codemaster::where('parent_id', $officetype)->get();
    }

    public function submit()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'zip' => 'required|numeric|digits:6',
            'selectedMappingLevel' => 'required|exists:codemasters,code',
            'selectedState' => 'required|exists:states,id',
        ]);

        dd([
            'name' => $this->name,
            'address' => $this->address,
            'zip' => $this->zip,
            'office_type_id' => $this->selectedMappingLevel,
            'state_id' => $this->selectedState,
            'district_id' => $this->selectedDistrict ?? null,
            // 'subdivision_id' => $this->selectedSubdivision ?? null,
            // 'municipalitiy_id' => $this->selectedBlockurban ?? null,
            // 'block_id' => $this->selectedBlockurban ?? null,
            // 'panchayat_id' => $this->selectedGpWard ?? null,
            // 'ward_id' => $this->selectedGpWard ?? null,

        ]);

        // OfficeMaster::create([
        //     'name' => $this->name,
        //     'address' => $this->address,
        //     'zip' => $this->zip,
        //     'office_type_id' => $this->selectedMappingLevel,
        //     'state_id' => $this->selectedState,
        //     'district_id' => $this->selectedDistrict ?? null,
        //     'subdivision_id' => $this->selectedSubdivision ?? null,
        //     'municipalitiy_id' => $this->selectedBlockurban ?? null,
        //     'block_id' => $this->selectedBlockurban ?? null,
        //     'panchayat_id' => $this->selectedGpWard ?? null,
        //     'ward_id' => $this->selectedGpWard ?? null,
        // ]);

        // Toaster::success('Office Master created successfully!');
        session()->flash('success', 'Office Master created successfully!');
        return redirect()->route('role-office-master-mappings.index');
    }

    public function render()
    {
        return view('livewire.office-masters.create');
    }
}
