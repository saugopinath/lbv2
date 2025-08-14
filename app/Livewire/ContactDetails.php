<?php

namespace App\Livewire;
use Illuminate\Support\Facades\Session;
use Livewire\Component;
use App\Models\DraftBeneficiaryPersonal;
use App\Models\DraftBeneficiaryContact;
use Illuminate\Support\Facades\Auth;
use App\Models\State;

class ContactDetails extends Component
{
    public $mode, $id;
    public $stateName, $state, $policestation, $villtowncity, $housepremiseno, $postoffice, $pincode;
    public $selectedDistrict, $selectedRuralurban, $selectedBlockurban, $selectedGpWard;
    protected $listeners = ['lgdSelectionChanged' => 'receiveLgdSelection'];
    public function receiveLgdSelection($data)
    {
        $this->selectedDistrict = $data['selectedDistrict'];
        $this->selectedRuralurban = $data['selectedRuralurban'];
        $this->selectedBlockurban = $data['selectedBlockurban'];
        $this->selectedGpWard = $data['selectedGpWard'];
    }
    public function mount($mode = null, $id = null)
    {
        $this->mode = $mode;
        $record = State::where('lgd_code', 19)->first();
        $this->state = $record->lgd_code;
        $this->stateName = $record->name;
        if ($id != null) {
            $this->id = $id;
            $app_det = DraftBeneficiaryPersonal::with('contact')->where('application_id', $id)->first();
            $this->policestation = $app_det->contact->police_station;
            $this->villtowncity = $app_det->contact->village_town_city;
            if ($app_det->contact->house_premise_no) {
                $this->housepremiseno = $app_det->contact->house_premise_no;
            }
            $this->postoffice = $app_det->contact->post_office;
            $this->pincode = trim($app_det->contact->pincode);
            $this->selectedDistrict = $app_det->contact->district_id;
            $this->selectedRuralurban = $app_det->contact->rural_urban_id;
            if (($app_det->contact->rural_urban_id) == 2) {
                $this->selectedBlockurban = $app_det->contact->block_id;
                $this->selectedGpWard = $app_det->contact->panchayat_id;
            } else {
                $this->selectedBlockurban = $app_det->contact->municipality_id;
                $this->selectedGpWard = $app_det->contact->ward_id;
            }
        }
    }
    public function rules()
    {
        $rules = [
            'state' => 'required|numeric',
            'policestation' => 'required|string',
            'villtowncity' => 'required|string',
            'postoffice' => 'required|string',
            'pincode' => 'required|digits:6|numeric',
            'selectedDistrict' => 'required|numeric',
            'selectedRuralurban' => 'required|numeric',
            'selectedBlockurban' => 'required|numeric',
            'selectedGpWard' => 'required|numeric',
            'housepremiseno' => 'nullable|string',
        ];
        return $rules;
    }
    public function save()
    {
        $validated = $this->validate($this->rules());
        if ($this->mode === null) {
            $application_id = Session::get('application_id');
            $data = [
                'application_id' => $application_id,
                'district_id' => $validated['selectedDistrict'],
                'rural_urban_id' => $validated['selectedRuralurban'],
                'police_station' => $validated['policestation'],
                'village_town_city' => $validated['villtowncity'],
                'post_office' => $validated['postoffice'],
                'pincode' => $validated['pincode'],
                'created_by' => Auth::id(),
            ];
            $data['house_premise_no'] = $validated['housepremiseno'] ?? null;
            if ($validated['selectedRuralurban'] == 2) {
                $data['block_id'] = $validated['selectedBlockurban'];
                $data['panchayat_id'] = $validated['selectedGpWard'];
            } else {
                $data['municipality_id'] = $validated['selectedBlockurban'];
                $data['ward_id'] = $validated['selectedGpWard'];
            }
            DraftBeneficiaryContact::create($data);
        } else {
            $data = [
                'district_id' => $validated['selectedDistrict'],
                'rural_urban_id' => $validated['selectedRuralurban'],
                'police_station' => $validated['policestation'],
                'village_town_city' => $validated['villtowncity'],
                'post_office' => $validated['postoffice'],
                'pincode' => $validated['pincode'],
            ];
            $data['house_premise_no'] = $validated['housepremiseno'] ?? null;
            if ($validated['selectedRuralurban'] == 2) {
                $data['block_id'] = $validated['selectedBlockurban'];
                $data['panchayat_id'] = $validated['selectedGpWard'];
            } else {
                $data['municipality_id'] = $validated['selectedBlockurban'];
                $data['ward_id'] = $validated['selectedGpWard'];
            }
            DraftBeneficiaryContact::where('application_id', $this->id)->update($data);
        }
        $this->dispatch('conDet');
    }
    public function render()
    {
        return view('livewire.contact-details');
    }
}
