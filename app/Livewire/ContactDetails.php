<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\DraftBeneficiaryPersonal;
use App\Models\DraftBeneficiaryContact;
use Illuminate\Support\Facades\Auth;
use App\Models\State;

class ContactDetails extends Component
{
    public $mode;
    public $stateName, $state, $policestation, $villtowncity, $housepremiseno, $postoffice, $pincode;
    public $district_id, $rural_urban, $blockurban, $gp_ward;
    protected $listeners = ['lgdSelectionChanged' => 'receiveLgdSelection'];
    public function receiveLgdSelection($data)
    {
        $this->district_id = $data['district_id'];
        $this->rural_urban = $data['rural_urban'];
        $this->blockurban = $data['blockurban'];
        $this->gp_ward = $data['gp_ward'];
    }
    public function mount($mode = null)
    {
        $this->mode = $mode;
        $record = State::where('lgd_code', 19)->first();
        if ($record) {
            $this->state = $record->lgd_code;
            $this->stateName = $record->name;
        }
    }
    public function rules() {
        $rules = [
            'state' => 'required|numeric',
            'policestation' => 'required|string',
            'villtowncity' => 'required|string',
            'postoffice' => 'required|string',
            'pincode' => 'required|digits:6|numeric',
            'district_id' => 'required|numeric',
            'rural_urban' => 'required|numeric',
            'blockurban' => 'required|numeric',
            'gp_ward' => 'required|numeric',
        ];
        return $rules;
    }
    public function save()
    {
        if ($this->mode === null) {
            $validated = $this->validate($this->rules());
            $applicantion = DraftBeneficiaryPersonal::first();
            DraftBeneficiaryContact::create([
                'application_id' => $applicantion->application_id,
                'district_id' => $validated['district_id'],
                'rural_urban_id' => $validated['rural_urban'],
                'block_id' => 2974,
                'municipality_id' => $validated['blockurban'],
                'ward_id' => $validated['gp_ward'],
                'panchayat_id' => 2459,
                'police_station' => $validated['policestation'],
                'house_premise_no' => 'abc',
                'village_town_city' => $validated['villtowncity'],
                'post_office' => $validated['postoffice'],
                'pincode' => $validated['pincode'],
                'residency_period' => 25,
                'created_by' => Auth::user()->id,
            ]);
        } else {
        }
    }
    public function render()
    {
        return view('livewire.contact-details');
    }
}
