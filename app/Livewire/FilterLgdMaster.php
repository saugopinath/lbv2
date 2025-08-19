<?php

namespace App\Livewire;

use App\Models\Block;
use Livewire\Component;
use App\Models\District;
use App\Models\Municipality;
use Illuminate\Support\Facades\Crypt;

class FilterLgdMaster extends Component
{
    public $districts = [], $blocks = [], $urbanbodys = [], $gps = [], $wards = [];
    public $selectedDistrict, $selectedRuralurban, $selectedBlockurban, $selectedGpWard;
    public $login_type;
    public array $filter_condition = [];
    public $visible = [
        'district_dropdown' => 0,
        'rural_urban_dropdown' => 0,
        'block_dropdown' => 0,
        'gp_ward_dropdown' => 0,
    ];

    public function mount($login_type = null)
    {
        $this->login_type = $login_type;

        $select_lgd = session('lgd_session');

        // foreach ($select_lgd as $key => $val) {
        //     $this->filter_condition[$key] = Crypt::decryptString($val);
        // }

        if (!empty($select_lgd['district_id'])) {
            $this->filter_condition['district_id'] = Crypt::decryptString($select_lgd['district_id']);
        }

        if (!empty($select_lgd['block_id'])) {
            $this->filter_condition['block_id'] = Crypt::decryptString($select_lgd['block_id']);
        }

        if (!empty($select_lgd['subdivision_id'])) {
            $this->filter_condition['subdivision_id'] = Crypt::decryptString($select_lgd['subdivision_id']);
        }

        if ($this->login_type === '151') {
            $this->visible['district_dropdown'] = 1;
            $this->visible['rural_urban_dropdown'] = 1;
            $this->visible['block_dropdown'] = 1;
            $this->visible['gp_ward_dropdown'] = 1;
            $this->districts = District::all();
        }
        if ($this->login_type === '152') {
            $this->visible['rural_urban_dropdown'] = 1;
            $this->visible['block_dropdown'] = 1;
            $this->visible['gp_ward_dropdown'] = 1;
            $this->selectedDistrict = $this->filter_condition['district_id'];
        }
        if ($this->login_type === '153') {
            $this->visible['gp_ward_dropdown'] = 1;
            $this->selectedRuralurban = 2;
            $this->selectedBlockurban = $select_lgd['block_id'];
            $this->loadGpOrWard();
        }
        if ($this->login_type === '154') {
            $this->visible['block_dropdown'] = 1;
            $this->visible['gp_ward_dropdown'] = 1;
            $this->selectedDistrict = $this->filter_condition['district_id'];
            $this->selectedRuralurban = 1;
            $this->loadSubdivisions();
        }
    }

    public function loadSubdivisions()
    {
        if ($this->selectedDistrict && $this->selectedRuralurban) {
            $district = District::find($this->selectedDistrict);
            if ($district) {
                if ($this->selectedRuralurban == 1) {
                    $this->urbanbodys = $district->municipalities;
                } elseif ($this->selectedRuralurban == 2) {
                    $this->blocks = $district->blocks;
                }
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
        $this->selectedBlockurban = null;
        $this->selectedGpWard = null;
        $this->blocks = [];
        $this->urbanbodys = [];
    }

    public function updatedSelectedRuralurban()
    {
        $this->selectedBlockurban = null;
        $this->selectedGpWard = null;
        $this->blocks = [];
        $this->urbanbodys = [];
        $this->loadSubdivisions();
    }

    public function updatedSelectedBlockurban()
    {
        $this->selectedGpWard = null;
        $this->gps = [];
        $this->wards = [];
        $this->loadGpOrWard();
    }

    // public function resetFilters()
    // {
    //     $this->reset([
    //         'selectedDistrict',
    //         'selectedRuralurban',
    //         'selectedBlockurban',
    //         'selectedGpWard',
    //         'blocks',
    //         'urbanbodys',
    //         'gps',
    //         'wards',
    //     ]);
    // }
    public function resetFilters()
    {
        $this->selectedDistrict = null;
        $this->selectedRuralurban = null;
        $this->selectedBlockurban = null;
        $this->selectedGpWard = null;
        $this->blocks = [];
        $this->urbanbodys = [];
        $this->gps = [];
        $this->wards = [];

        if ($this->login_type === '151') {
            $this->districts = District::all();
        } elseif (in_array($this->login_type, ['152', '154'])) {
            if (!empty($this->filter_condition['district_id'])) {
                $this->selectedDistrict = $this->filter_condition['district_id'];
                $this->loadSubdivisions();
            }
        } elseif ($this->login_type === '153') {
            if (!empty($this->filter_condition['block_id'])) {
                $this->selectedRuralurban = 2;
                $this->selectedBlockurban = $this->filter_condition['block_id'];
                $this->loadGpOrWard();
            }
        }

        $this->dispatch('filtersApplied', [
            'district_id' => null,
            'rural_urban' => null,
            'blockurban'  => null,
            'gp_ward'     => null,
        ]);
    }

    public function applyFilters()
    {
        $this->dispatch('filtersApplied', [
            'district_id' => $this->selectedDistrict,
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
