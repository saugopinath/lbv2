<?php

namespace App\Livewire;

use App\Models\Block;
use App\Models\Subdivision;
use Livewire\Component;
use App\Models\District;
use App\Models\Municipality;
use Illuminate\Support\Facades\Crypt;

class FilterLgdMaster extends Component
{
    public $districts = [], $blocks = [], $urbanbodys = [], $gps = [], $wards = [], $subdivisions = [];
    public $selectedDistrict, $selectedRuralurban, $selectedBlockurban, $selectedGpWard, $selectedSubdivision;
    public array $filter_condition = [];
    public $button_show;
    public $visible = [
        'district_dropdown' => 0,
        'rural_urban_dropdown' => 0,
        'subdivision_dropdown' => 0,
        'block_dropdown' => 0,
        'gp_ward_dropdown' => 0,
    ];

    public function mount($button_show=null)
    {
        $select_lgd = session('lgd_session');
        $this->button_show = $button_show;

        $login_type =  Crypt::decryptString($select_lgd['office_type_id']);

        if (!empty($select_lgd['district_id'])) {
            $this->filter_condition['district_id'] = Crypt::decryptString($select_lgd['district_id']);
        }

        if (!empty($select_lgd['block_id'])) {
            $this->filter_condition['block_id'] = Crypt::decryptString($select_lgd['block_id']);
        }

        if (!empty($select_lgd['subdivision_id'])) {
            $this->filter_condition['subdivision_id'] = Crypt::decryptString($select_lgd['subdivision_id']);
        }

        if ($login_type === '151') {
            $this->visible['district_dropdown'] = 1;
            $this->visible['rural_urban_dropdown'] = 1;
            $this->visible['subdivision_dropdown'] = 0; // Toggled dynamically for urban
            $this->visible['block_dropdown'] = 1;
            $this->visible['gp_ward_dropdown'] = 1;
            $this->districts = District::all();
        }
        if ($login_type === '152') {
            $this->visible['rural_urban_dropdown'] = 1;
            $this->visible['subdivision_dropdown'] = 0; // Toggled dynamically for urban
            $this->visible['block_dropdown'] = 1;
            $this->visible['gp_ward_dropdown'] = 1;
            $this->selectedDistrict = $this->filter_condition['district_id'];
        }
        if ($login_type === '153') {
            $this->visible['gp_ward_dropdown'] = 1;
            $this->selectedRuralurban = 2;
            $this->selectedBlockurban = $this->filter_condition['block_id'];
            $this->loadGpOrWard();
        }
        if ($login_type === '154') {
            // $this->visible['subdivision_dropdown'] = 1;
            $this->visible['block_dropdown'] = 1;
            $this->visible['gp_ward_dropdown'] = 1;
            $this->selectedDistrict = $this->filter_condition['district_id'];
            $this->selectedRuralurban = 1;
            $this->selectedSubdivision = $this->filter_condition['subdivision_id'];
            $this->loadDistrictSubdivisions();
            $this->loadMunicipalities();
        }
    }
    public function loadDistrictSubdivisions()
    {
        if ($this->selectedDistrict && $this->selectedRuralurban == 1) {
            $district = District::find($this->selectedDistrict);
            if ($district) {
                $this->subdivisions = $district->subdivisions;
            }
        }
    }
    public function loadBlocks()
    {
        if ($this->selectedDistrict && $this->selectedRuralurban == 2) {
            $district = District::find($this->selectedDistrict);
            if ($district) {
                $this->blocks = $district->blocks;
            }
        }
    }
    public function loadMunicipalities()
    {
        if ($this->selectedSubdivision && $this->selectedRuralurban == 1) {
            $subdivision = Subdivision::find($this->selectedSubdivision);
            if ($subdivision) {
                $this->urbanbodys = $subdivision->municipalities;
            }
        }
    }
    public function loadGpOrWard()
    {
        if ($this->selectedBlockurban && $this->selectedRuralurban) {
            if ($this->selectedRuralurban == 1) {
                $municipality = Municipality::find($this->selectedBlockurban);
                if ($municipality) {
                    $this->wards = $municipality->wards;
                }
            } elseif ($this->selectedRuralurban == 2) {
                $block = Block::find($this->selectedBlockurban);
                if ($block) {
                    $this->gps = $block->panchayats;
                }
            }
        }
    }
    public function updatedSelectedDistrict()
    {
        $this->selectedRuralurban = null;
        $this->selectedSubdivision = null;
        $this->selectedBlockurban = null;
        $this->selectedGpWard = null;
        $this->blocks = [];
        $this->urbanbodys = [];
        $this->subdivisions = [];
    }

    public function updatedSelectedRuralurban()
    {
        $this->selectedSubdivision = null;
        $this->selectedBlockurban = null;
        $this->selectedGpWard = null;
        $this->blocks = [];
        $this->urbanbodys = [];
        $this->subdivisions = [];
        if ($this->selectedRuralurban == 1) {
            $this->visible['subdivision_dropdown'] = 1;
            $this->loadDistrictSubdivisions();
        } elseif ($this->selectedRuralurban == 2) {
            $this->visible['subdivision_dropdown'] = 0;
            $this->loadBlocks();
        } else {
            $this->visible['subdivision_dropdown'] = 0;
        }
    }

    public function updatedSelectedSubdivision()
    {
        $this->selectedBlockurban = null;
        $this->selectedGpWard = null;
        $this->urbanbodys = [];
        $this->gps = [];
        $this->wards = [];
        $this->loadMunicipalities();
    }

    public function updatedSelectedBlockurban()
    {
        $this->selectedGpWard = null;
        $this->gps = [];
        $this->wards = [];
        $this->loadGpOrWard();
    }
    public function resetFilters()
    {
        $this->selectedDistrict = null;
        $this->selectedRuralurban = null;
        $this->selectedSubdivision = null;
        $this->selectedBlockurban = null;
        $this->selectedGpWard = null;
        $this->blocks = [];
        $this->urbanbodys = [];
        $this->subdivisions = [];
        $this->gps = [];
        $this->wards = [];

        $select_lgd = session('lgd_session');

        $login_type =  Crypt::decryptString($select_lgd['office_type_id']);

        if ($login_type === '151') {
            $this->districts = District::all();
        } elseif (in_array($login_type, ['152', '154'])) {
            if (!empty($this->filter_condition['district_id'])) {
                $this->selectedDistrict = $this->filter_condition['district_id'];
            }
            if ($login_type === '154') {
                $this->selectedRuralurban = 1;
                $this->visible['subdivision_dropdown'] = 1;
                $this->selectedSubdivision = $this->filter_condition['subdivision_id'];
                $this->loadDistrictSubdivisions();
                $this->loadMunicipalities();
            }
        } elseif ($login_type === '153') {
            if (!empty($this->filter_condition['block_id'])) {
                $this->selectedRuralurban = 2;
                $this->selectedBlockurban = $this->filter_condition['block_id'];
                $this->loadGpOrWard();
            }
        }

        $this->dispatch('filtersApplied', [
            'district_id' => null,
            'rural_urban' => null,
            'subdivision_id' => null,
            'blockurban'  => null,
            'gp_ward'     => null,
        ]);
    }

    public function applyFilters()
    {
        $this->dispatch('filtersApplied', [
            'district_id' => $this->selectedDistrict,
            'rural_urban' => $this->selectedRuralurban,
            'subdivision_id' => $this->selectedSubdivision,
            'blockurban' => $this->selectedBlockurban,
            'gp_ward' => $this->selectedGpWard,
        ]);
    }

    public function render()
    {
        return view('livewire.filter-lgd-master');
    }
}