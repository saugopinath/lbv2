<?php

namespace App\Livewire;

use App\Models\Block;
use App\Models\District;
use App\Models\Subdivision;
use App\Models\Municipality;
use Livewire\Component;
use Illuminate\Support\Facades\Crypt;

class FilterLgdMaster extends Component
{
    public $districts = [], $subdivisions = [], $blocks = [], $urbanbodys = [], $gps = [], $wards = [];
    public $selectedDistrict, $selectedSubdivision, $selectedRuralurban, $selectedBlockurban, $selectedGpWard;
    public $login_type;
    public array $filter_condition = [];
    public $visible = [
        'district_dropdown' => 0,
        'subdivision_dropdown' => 0,
        'rural_urban_dropdown' => 0,
        'block_dropdown' => 0,
        'gp_ward_dropdown' => 0,
    ];

    public function mount($login_type = null)
    {
        $this->login_type = $login_type;

        $select_lgd = session('lgd_session') ?? [];

        if (!empty($select_lgd['district_id'])) {
            $this->filter_condition['district_id'] = Crypt::decryptString($select_lgd['district_id']);
        }

        if (!empty($select_lgd['subdivision_id'])) {
            $this->filter_condition['subdivision_id'] = Crypt::decryptString($select_lgd['subdivision_id']);
        }

        if (!empty($select_lgd['block_id'])) {
            $this->filter_condition['block_id'] = Crypt::decryptString($select_lgd['block_id']);
        }

        if ($this->login_type === '151') {
            $this->visible['district_dropdown'] = 1;
            $this->visible['rural_urban_dropdown'] = 1;
            $this->visible['block_dropdown'] = 1;
            $this->visible['gp_ward_dropdown'] = 1;
            $this->districts = District::all();
            // Subdivision visibility set dynamically in updatedSelectedRuralurban
        }
        if ($this->login_type === '152') {
            $this->visible['rural_urban_dropdown'] = 1;
            $this->visible['block_dropdown'] = 1;
            $this->visible['gp_ward_dropdown'] = 1;
            $this->selectedDistrict = $this->filter_condition['district_id'] ?? null;
            if ($this->selectedDistrict && !empty($this->filter_condition['subdivision_id'])) {
                $this->selectedSubdivision = $this->filter_condition['subdivision_id'];
                $this->loadSubdivisions();
            }
        }
        if ($this->login_type === '153') {
            $this->visible['gp_ward_dropdown'] = 1;
            $this->visible['subdivision_dropdown'] = 0;
            $this->selectedRuralurban = 2;
            $this->selectedBlockurban = $this->filter_condition['block_id']  ?? null;
            $this->loadGpOrWard();
        }
        if ($this->login_type === '154') {
            $this->visible['block_dropdown'] = 1;
            $this->visible['gp_ward_dropdown'] = 1;
            $this->visible['subdivision_dropdown'] = 1;
            $this->selectedDistrict = $this->filter_condition['district_id'] ?? null;
            $this->selectedRuralurban = 1;
             // Show for urban
            if ($this->selectedDistrict) {
                $this->loadSubdivisions();
                if (!empty($this->filter_condition['subdivision_id'])) {
                    $this->selectedSubdivision = $this->filter_condition['subdivision_id'];
                    $this->loadBlocksOrUrban();
                }
            }
        }

        // Set initial subdivision visibility if rural_urban is pre-set
        if ($this->selectedRuralurban) {
            $this->visible['subdivision_dropdown'] = ($this->selectedRuralurban == 1);
        }
    }

    public function loadSubdivisions()
    {
        $this->subdivisions = [];
        $this->selectedSubdivision = null;
        $this->blocks = [];
        $this->urbanbodys = [];
        $this->gps = [];
        $this->wards = [];
        $this->selectedBlockurban = null;
        $this->selectedGpWard = null;

        if ($this->selectedDistrict && $this->selectedRuralurban == 1) {
            $district = District::find($this->selectedDistrict);
            if ($district) {
                $this->subdivisions = $district->subdivisions;
            }
        }
    }

    public function loadBlocksOrUrban()
    {
        $this->blocks = [];
        $this->urbanbodys = [];
        $this->gps = [];
        $this->wards = [];
        $this->selectedBlockurban = null;
        $this->selectedGpWard = null;

        if ($this->selectedRuralurban == 2) {
            // For rural, load blocks from district
            if ($this->selectedDistrict) {
                $district = District::find($this->selectedDistrict);
                if ($district) {
                    $this->blocks = $district->blocks;
                }
            }
        } elseif ($this->selectedRuralurban == 1 && $this->selectedSubdivision) {
            // For urban, load municipalities from subdivision
            $subdivision = Subdivision::find($this->selectedSubdivision);
            if ($subdivision) {
                $this->urbanbodys = $subdivision->municipalities;
            }
        }
    }

    public function loadGpOrWard()
    {
        $this->gps = [];
        $this->wards = [];
        $this->selectedGpWard = null;

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
        $this->selectedSubdivision = null;
        $this->selectedBlockurban = null;
        $this->selectedGpWard = null;
        $this->subdivisions = [];
        $this->blocks = [];
        $this->urbanbodys = [];
        $this->gps = [];
        $this->wards = [];

        if ($this->selectedRuralurban == 1) {
            $this->loadSubdivisions();
        } elseif ($this->selectedRuralurban == 2) {
            $this->loadBlocksOrUrban();
        }
    }

    public function updatedSelectedSubdivision()
    {
        $this->loadBlocksOrUrban();
    }

    public function updatedSelectedRuralurban()
    {
        $this->visible['subdivision_dropdown'] = ($this->selectedRuralurban == 1);

        $this->selectedSubdivision = null;
        $this->selectedBlockurban = null;
        $this->selectedGpWard = null;
        $this->subdivisions = [];
        $this->blocks = [];
        $this->urbanbodys = [];
        $this->gps = [];
        $this->wards = [];

        if ($this->selectedRuralurban == 1) {
            $this->loadSubdivisions();
        } elseif ($this->selectedRuralurban == 2) {
            $this->loadBlocksOrUrban();
        }
    }

    public function updatedSelectedBlockurban()
    {
        $this->loadGpOrWard();
    }

    public function resetFilters()
    {
        $this->selectedDistrict = null;
        $this->selectedSubdivision = null;
        $this->selectedRuralurban = null;
        $this->selectedBlockurban = null;
        $this->selectedGpWard = null;
        $this->subdivisions = [];
        $this->blocks = [];
        $this->urbanbodys = [];
        $this->gps = [];
        $this->wards = [];
        $this->visible['subdivision_dropdown'] = 0;

        if ($this->login_type === '151') {
            $this->districts = District::all();
        } elseif (in_array($this->login_type, ['152', '154'])) {
            if (!empty($this->filter_condition['district_id'])) {
                $this->selectedDistrict = $this->filter_condition['district_id'];
                if ($this->login_type === '154') {
                    $this->selectedRuralurban = 1;
                    $this->visible['subdivision_dropdown'] = 1;
                    $this->loadSubdivisions();
                    if (!empty($this->filter_condition['subdivision_id'])) {
                        $this->selectedSubdivision = $this->filter_condition['subdivision_id'];
                        $this->loadBlocksOrUrban();
                    }
                }
            }
        } elseif ($this->login_type === '153') {
            if (!empty($this->filter_condition['block_id'])) {
                $this->selectedRuralurban = 2;
                $this->selectedBlockurban = $this->filter_condition['block_id'];
                $this->visible['subdivision_dropdown'] = 0;
                $this->loadGpOrWard();
            }
        }

        $this->dispatch('filtersApplied', [
            'district_id' => null,
            'subdivision_id' => null,
            'rural_urban' => null,
            'blockurban' => null,
            'gp_ward' => null,
        ]);
    }

    public function applyFilters()
    {
        $this->dispatch('filtersApplied', [
            'district_id' => $this->selectedDistrict,
            'subdivision_id' => $this->selectedSubdivision,
            'rural_urban' => $this->selectedRuralurban,
            'blockurban' => $this->selectedBlockurban,
            'gp_ward' => $this->selectedGpWard,
        ]);
    }

    public function render()
    {
        return view('livewire.filter-lgd-master');
    }
}
