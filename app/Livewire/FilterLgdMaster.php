<?php

namespace App\Livewire;

use App\Models\Block;
use Livewire\Component;
use App\Models\District;
use App\Models\Municipality;
use App\Helpers\EncryptionArray;
use Illuminate\Support\Facades\Crypt;

class FilterLgdMaster extends Component
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

    public function mount($login_type = null)
    {
        $this->login_type = $login_type;

        //  $select_lgd = EncryptionArray::lgdsession();
        // //   $select_lgd = session('lgd_session');

        // if ($select_lgd) {
        //     $select_lgd = array_map(function ($value) {
        //         try {
        //             return decrypt($value);
        //         } catch (\Exception $e) {
        //             return $value;
        //         }
        //     }, $select_lgd);
        // }

      $select_lgd = session('lgd_session');
        $filter_condition = [];

        if ($select_lgd) {
            foreach ($select_lgd as $key => $val) {
                try {
                    $filter_condition[$key] = Crypt::decryptString($val);
                } catch (\Exception $e) {
                    $filter_condition[$key] = $val;
                }
            }
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
            $this->selectedDistrict = $filter_condition['district_id'];
        }
        if ($this->login_type === '153') {
            $this->visible['gp_ward_dropdown'] = 1;
            $this->selectedRuralurban = 2;
            $this->selectedBlockurban = $filter_condition['block_id'];
            $this->loadGpOrWard();
        }
         if ($this->login_type === '154') {
            $this->visible['block_dropdown'] = 1;
            $this->visible['gp_ward_dropdown'] = 1;
            $this->selectedDistrict = $filter_condition['district_id'];
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

    public function resetFilters()
    {
        $this->reset([
            'selectedDistrict',
            'selectedRuralurban',
            'selectedBlockurban',
            'selectedGpWard',
            'blocks',
            'urbanbodys',
            'gps',
            'wards',
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
