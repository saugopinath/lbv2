<?php

namespace App\Livewire\TrackBeneficiary;

use App\Models\BeneficiaryPersonalDetail;
use App\Models\Block;
use App\Models\District;
use App\Models\Municipality;
use App\Models\Panchayat;
use App\Models\Scheme;
use App\Models\Subdivision;
use App\Models\Ward;
use App\Models\UserRoleSchemeOfficeMapping;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Livewire\Component;
use Livewire\WithPagination;

class TrackBeneficiaryData extends Component
{
    // use WithPagination;

    // Filters
    public $scheme = '';
    public $district = '';
    public $areaType = ''; // 1 for Urban, 2 for Rural
    public $block = '';
    public $municipality = '';
    public $gp_ward = '';
    public $search = '';

    // Data for dropdowns
    public $schemes = [];
    public $districts = [];
    public $blocks = [];
    public $subDistricts = [];
    public $ulbs = [];
    public $gps = [];
    public $ulb_wards = [];

    // User restrictions
    public ?int $userDistrictId = null;
    public $isAdmin = false;

    public function mount()
    {
        $userId = Auth::id();
        $select_lgd = session('lgd_session');
        // dd($select_lgd);
        if (!empty($select_lgd['district_id'])) {
            $this->filter_condition['created_by_dist_code'] = Crypt::decryptString($select_lgd['district_id']);
        }
        if (!empty($select_lgd['block_id'])) {
            $this->filter_condition['created_by_local_body_code'] = Crypt::decryptString($select_lgd['block_id']);
        }
        if (!empty($select_lgd['subdivision_id'])) {
            $this->filter_condition['created_by_local_body_code'] = Crypt::decryptString($select_lgd['subdivision_id']);
        }
        if (!empty($select_lgd['subdivision_id'])) {
            $this->filter_condition['created_by_local_body_code'] = Crypt::decryptString($select_lgd['subdivision_id']);
        }

        $this->loadLocationData();
    }

    // public function updatedDistrict()
    // {
    //     $this->resetFilters(['block', 'municipality', 'gp_ward', 'areaType']);
    //     $this->loadLocationData();
    // }

    // public function updatedAreaType()
    // {
    //     $this->resetFilters(['block', 'municipality', 'gp_ward']);
    // }

    // public function updatedBlock()
    // {
    //     $this->resetFilters(['municipality', 'gp_ward']);
    // }

    // public function updatedMunicipality()
    // {
    //     $this->resetFilters(['gp_ward']);
    // }

    // protected function resetFilters($fields)
    // {
    //     foreach ($fields as $field) {
    //         $this->$field = '';
    //     }
    //     $this->resetPage();
    // }

    public function loadLocationData()
    {
        $scout = BeneficiaryPersonalDetail::search($this->search);


        // if ($this->district) {
        //     $scout->where('district_id', (int) $this->district);
        // }
        // if ($this->scheme) {
        //     $scout->where('scheme_id', (int) $this->scheme);
        // }
        // if ($this->areaType) {
        //     $scout->where('rural_urban', (int) $this->areaType);
        // }
        // if ($this->block) {
        //     $scout->where('blockurban', (int) $this->block);
        // }
        // if ($this->municipality) {
        //     $scout->where('blockurban', (int) $this->municipality);
        // }
        // if ($this->gp_ward) {
        //     $scout->where('gpward', (int) $this->gp_ward);
        // }
        $beneficiaries = $scout->paginate(20);
    }

    public function render()
    {
        // $scout = BeneficiaryPersonalDetail::search($this->search);

        // if ($this->district) {
        //     $scout->where('district_id', (int) $this->district);
        // }

        // if ($this->scheme) {
        //     $scout->where('scheme_id', (int) $this->scheme);
        // }

        // if ($this->areaType) {
        //     $scout->where('rural_urban', (int) $this->areaType);
        // }

        // if ($this->block) {
        //     $scout->where('blockurban', (int) $this->block);
        // }

        // if ($this->municipality) {
        //     $scout->where('blockurban', (int) $this->municipality);
        // }

        // if ($this->gp_ward) {
        //     $scout->where('gpward', (int) $this->gp_ward);
        // }

        // $beneficiaries = $scout->paginate(20);

        return view('livewire.track-beneficiary.track-beneficiary-data', [
            // 'beneficiaries' => $beneficiaries
        ]);
    }
}
