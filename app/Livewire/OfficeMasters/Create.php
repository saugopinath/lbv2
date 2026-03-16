<?php

namespace App\Livewire\OfficeMasters;

use Livewire\Component;
use App\Models\State;
use App\Models\District;
use App\Models\Subdivision;
use App\Models\Block;
use App\Models\Codemaster;
use App\Models\OfficeMaster;
use Illuminate\Support\Facades\DB;
use App\Attributes\Loggable;
class Create extends Component
{
    public $name, $address, $zip;
    public $selectedMappingLevel, $selectedState, $selectedDistrict, $selectedSubdivision, $selectedBlockurban;

    public $mapping_levels = [];
    public $states = [];
    public $districts = [];
    public $subdivisions = [];
    public $blocks = [];

    public function mount()
    {
        $this->states = State::where('is_active', 1)->where('lgd_code', 19)->get();
        $officetype = Codemaster::getIdByCode(15);
        $this->mapping_levels = Codemaster::where('parent_id', $officetype)->whereIn('code', [151, 152, 153, 154])->get();
    }

    public function updatedSelectedMappingLevel($value)
    {
        $this->selectedDistrict = null;
        $this->selectedSubdivision = null;
        $this->selectedBlockurban = null;
        $this->districts = [];
        $this->subdivisions = [];
        $this->blocks = [];

        if (in_array($value, ['152', '153', '154']) && $this->selectedState) {
            $this->districts = District::where('state_id', $this->selectedState)->get();
        }
    }

    public function updatedSelectedState($stateId)
    {
        $this->selectedDistrict = null;
        $this->selectedSubdivision = null;
        $this->selectedBlockurban = null;
        $this->districts = [];
        $this->subdivisions = [];
        $this->blocks = [];

        if ($stateId && in_array($this->selectedMappingLevel, ['152', '153', '154'])) {
            $this->districts = District::where('state_id', $stateId)->get();
        }
    }

    public function updatedSelectedDistrict($districtId)
    {
        $this->selectedSubdivision = null;
        $this->selectedBlockurban = null;
        $this->subdivisions = [];
        $this->blocks = [];

        if ($this->selectedMappingLevel == '154' && $districtId) {
            $this->subdivisions = Subdivision::where('district_id', $districtId)->get();
        }

        if ($this->selectedMappingLevel == '153' && $districtId) {
            $this->blocks = Block::where('district_id', $districtId)->get();
        }
    }
    #[Loggable(level: 'C', nickname: 'Create OfficeMaster')]
    public function submit()
    {
        $rules = [
            'name' => 'required|string',
            'address' => 'required|string',
            'zip' => 'required|digits:6',
            'selectedMappingLevel' => 'required',
            'selectedState' => 'required',
        ];

        if (in_array($this->selectedMappingLevel, ['152', '153', '154'])) {
            $rules['selectedDistrict'] = 'required';
        }
        if ($this->selectedMappingLevel == '154') {
            $rules['selectedSubdivision'] = 'required';
        }
        if ($this->selectedMappingLevel == '153') {
            $rules['selectedBlockurban'] = 'required';
        }

        $this->validate($rules);

        try {
            DB::beginTransaction();

            OfficeMaster::create([
                'name' => $this->name,
                'address' => $this->address,
                'zip' => $this->zip,
                'office_type_id' => $this->selectedMappingLevel,
                'state_id' => $this->selectedState,
                'district_id' => $this->selectedDistrict ?? null,
                'subdivision_id' => $this->selectedSubdivision ?? null,
                'block_id' => $this->selectedBlockurban ?? null,
            ]);
            DB::commit();

            session()->flash('success', 'Office Master created successfully!');
            return redirect()->route('officemasters');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage())->withInput();
        }
    }
    public function updateReset()
    {
        $this->reset(['name', 'address', 'zip', 'selectedMappingLevel', 'selectedState', 'selectedDistrict', 'selectedSubdivision', 'selectedBlockurban']);
    }

    public function render()
    {
        return view('livewire.office-masters.create');
    }
}
