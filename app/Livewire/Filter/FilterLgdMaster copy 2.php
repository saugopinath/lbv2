<?php

namespace App\Livewire\Filter;

use Livewire\Component;
use App\Models\District;
use App\Models\Block;
use App\Models\Municipality;
use Illuminate\Support\Facades\Crypt;


class FilterLgdMaster extends Component
{
    public $districts = [], $blocks = [], $urbanbodys = [], $gps = [], $wards = [];

    public $selectedDistrict, $selectedRuralurban, $selectedBlockurban, $selectedGpWard;


    public $office_type_id;


    public $visible = [
        'district_dropdown' => 0,
        'rural_urban_dropdown' => 0,
        'block_dropdown' => 0,
        'gp_ward_dropdown' => 0,
    ];

    public function mount($office_type_id = null, $district_id = null, $block_code = null, $subdivision_code = null)
    {

        $this->office_type_id = $office_type_id;
        $this->selectedDistrict = $district_id;
        $this->selectedBlockurban = $block_code;
        $this->selectedsubdivision = $subdivision_code;


        //state
        if ($this->office_type_id === '151') {
            $this->visible['district_dropdown'] = 1;
            $this->visible['rural_urban_dropdown'] = 1;
            $this->visible['block_dropdown'] = 1;
            $this->visible['gp_ward_dropdown'] = 1;
            $this->districts = District::all();
        }
        //district office
        if ($this->office_type_id === '152') {
            $this->visible['rural_urban_dropdown'] = 1;
            $this->visible['block_dropdown'] = 1;
            $this->visible['gp_ward_dropdown'] = 1;
            $this->selectedDistrict = $district_id;

        }
        //subdivision office
        if ($this->office_type_id === '154') {
            $this->visible['block_dropdown'] = 1;
            $this->visible['gp_ward_dropdown'] = 1;
            $this->selectedDistrict = $district_id;
            $this->selectedRuralurban = 1;
            $this->loadSubdivisions();
        }

        //block office
        if ($this->office_type_id === '153') {
            $this->visible['gp_ward_dropdown'] = 1;
            $this->selectedRuralurban = 2;

            $this->selectedBlockurban = $block_code;
            $this->loadGpOrWard();
        }
    }
    public function loadSubdivisions()
    {
        if ($this->selectedDistrict && $this->selectedRuralurban) {
            $district = District::find($this->selectedDistrict);

            // dd($district);
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

    public function search()
    {

        $this->dispatch('filtersApplied', [
            'district_id' => $this->selectedDistrict,
            'rural_urban' => $this->selectedRuralurban,
            'blockurban' => $this->selectedBlockurban,
            'gp_ward' => $this->selectedGpWard,
        ]);
    }


    public function resetFilters()
    {

        $this->redirect(request()->header('Referer'));
    }



    public function render()
    {
        return view('livewire.filter.filter-lgd-master');
    }
}
