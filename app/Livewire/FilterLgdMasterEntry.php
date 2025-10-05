<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\District;
use App\Models\Block;
use App\Models\Municipality;

class FilterLgdMasterEntry extends Component
{
    public $districts = [], $blocks = [], $urbanbodys = [], $gps = [], $wards = [];

    public $selectedDistrict, $selectedRuralurban, $selectedBlockurban, $selectedGpWard;
    public $login_type;

    public $visible = [
        'district_dropdown' => 0,
        'rural_urban_dropdown' => 0,
        'block_dropdown' => 0,
        'gp_ward_dropdown' => 0,
    ];

    public function mount($login_type = null, $selectedDistrict = null, $selectedRuralurban = null, $selectedBlockurban = null, $selectedGpWard = null)
    {
        $this->login_type = $login_type;
        $this->selectedDistrict = $selectedDistrict;
        $this->selectedRuralurban = $selectedRuralurban;
        $this->selectedBlockurban = $selectedBlockurban;
        $this->selectedGpWard = $selectedGpWard;
        if ($this->login_type === 'state_office') {
            $this->visible['district_dropdown'] = 1;
            $this->visible['rural_urban_dropdown'] = 1;
            $this->visible['block_dropdown'] = 1;
            $this->visible['gp_ward_dropdown'] = 1;
            $this->districts = District::all();
            $this->loadSubdivisions();
            $this->loadGpOrWard();
        }
        if ($this->login_type === 'district_office') {
            $this->visible['rural_urban_dropdown'] = 1;
            $this->visible['block_dropdown'] = 1;
            $this->visible['gp_ward_dropdown'] = 1;
            $this->selectedDistrict = 318;
        }
        if ($this->login_type === 'subdivision_office') {
            $this->visible['block_dropdown'] = 1;
            $this->visible['gp_ward_dropdown'] = 1;
            $this->selectedDistrict = 318;
            $this->selectedRuralurban = 1;
            $this->loadSubdivisions();
        }
        if ($this->login_type === 'block_office') {
            $this->visible['gp_ward_dropdown'] = 1;
            $this->selectedRuralurban = 2;
            $this->selectedDistrict = 318;
            $this->selectedBlockurban = 2974;
            $this->loadGpOrWard();
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
    public function updatedSelectedGpWard()
    {
        $this->dispatch('lgdSelectionChanged', [
            'selectedDistrict' => $this->selectedDistrict,
            'selectedRuralurban' => $this->selectedRuralurban,
            'selectedBlockurban' => $this->selectedBlockurban,
            'selectedGpWard' => $this->selectedGpWard,
        ]);
    }
    public function render()
    {
        return view('livewire.filter-lgd-master-entry');
    }
}
