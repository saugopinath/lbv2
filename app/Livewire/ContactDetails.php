<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Session;
use Livewire\Component;
use App\Models\DraftBeneficiaryPersonal;
use App\Models\DraftBeneficiaryContact;
use Illuminate\Support\Facades\Auth;
use App\Models\State;
use Illuminate\Support\Facades\DB;

class ContactDetails extends Component
{
    public $mode, $application_id;
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
    public function mount($mode = null, $application_id = null)
    {
        $this->mode = $mode;
        $record = State::where('lgd_code', 19)->first();
        $this->state = $record->lgd_code;
        $this->stateName = $record->name;
        if ($application_id != null) {
            $this->application_id = $application_id;
            $app_det = DraftBeneficiaryPersonal::with('contact')->where('application_id', $application_id)->first();
            if ($app_det->contact) {
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
    }
    public function rules()
    {
        return [
            'state' => 'required|numeric',
            'policestation' => 'required|string|regex:/^[a-zA-Z\s]+$/',
            'villtowncity' => 'required|string|regex:/^[a-zA-Z\s]+$/',
            'postoffice' => 'required|string|regex:/^[a-zA-Z\s]+$/',
            'pincode' => 'required|digits:6',
            'selectedDistrict' => 'required|numeric',
            'selectedRuralurban' => 'required|numeric',
            'selectedBlockurban' => 'required|numeric',
            'selectedGpWard' => 'required|numeric',
            'housepremiseno' => 'nullable|string',
        ];
    }
    public function messages()
    {
        return [
            'state.*' => 'Please select a state.',
            'policestation.*' => 'Please enter the police station name and must contain only letters and spaces.',
            'villtowncity.*' => 'Please enter the village/town/city name and must contain only letters and spaces.',
            'postoffice.*' => 'Please enter the post office name and must contain only letters and spaces.',
            'pincode.*' => 'Please enter a valid 6-digit pincode.',
            'selectedDistrict.*' => 'Please select a district.',
            'selectedRuralurban.*' => 'Please select Rural/Urban.',
            'selectedBlockurban.*' => 'Please select a block/urban option.',
            'selectedGpWard.*' => 'Please select GP/Ward.',
            'housepremiseno.*' => 'Please enter house/premise number.',
        ];
    }
    public function save()
    {
        $validated = $this->validate($this->rules());
        $DraftBeneficiaryContact = DraftBeneficiaryContact::find($this->application_id);
         $this->dispatch('showLoader');
        DB::beginTransaction();
        try {
            if ($this->mode === null && empty($DraftBeneficiaryContact)) {
                $application_id = $this->application_id;
                $DraftBeneficiaryContact = new DraftBeneficiaryContact;
                $DraftBeneficiaryContact->application_id = $application_id;
                $DraftBeneficiaryContact->district_id = $validated['selectedDistrict'];
                $DraftBeneficiaryContact->rural_urban_id = $validated['selectedRuralurban'];
                $DraftBeneficiaryContact->police_station = $validated['policestation'];
                $DraftBeneficiaryContact->village_town_city = $validated['villtowncity'];
                $DraftBeneficiaryContact->post_office = $validated['postoffice'];
                $DraftBeneficiaryContact->pincode = $validated['pincode'];
                $DraftBeneficiaryContact->created_by = Auth::id();
                $DraftBeneficiaryContact->house_premise_no = $validated['housepremiseno'] ?? null;
                if ($validated['selectedRuralurban'] == 2) {
                    $DraftBeneficiaryContact->block_id = $validated['selectedBlockurban'];
                    $DraftBeneficiaryContact->panchayat_id = $validated['selectedGpWard'];
                } else {
                    $DraftBeneficiaryContact->municipality_id = $validated['selectedBlockurban'];
                    $DraftBeneficiaryContact->ward_id = $validated['selectedGpWard'];
                }
                $DraftBeneficiaryContact->save();
                $this->dispatch('conDet', [
                    'message' => "Contact Details saved successfully for the application id: {$this->application_id}"
                ]);
                $this->dispatch('hideLoader');
            } else {
                $DraftBeneficiaryContact->district_id = $validated['selectedDistrict'];
                $DraftBeneficiaryContact->rural_urban_id = $validated['selectedRuralurban'];
                $DraftBeneficiaryContact->police_station = $validated['policestation'];
                $DraftBeneficiaryContact->village_town_city = $validated['villtowncity'];
                $DraftBeneficiaryContact->post_office = $validated['postoffice'];
                $DraftBeneficiaryContact->pincode = $validated['pincode'];
                $DraftBeneficiaryContact->created_by = Auth::id();
                $DraftBeneficiaryContact->house_premise_no = $validated['housepremiseno'] ?? null;
                if ($validated['selectedRuralurban'] == 2) {
                    $DraftBeneficiaryContact->block_id = $validated['selectedBlockurban'];
                    $DraftBeneficiaryContact->panchayat_id = $validated['selectedGpWard'];
                    $DraftBeneficiaryContact->municipality_id = null;
                    $DraftBeneficiaryContact->ward_id = null;
                } else {
                    $DraftBeneficiaryContact->municipality_id = $validated['selectedBlockurban'];
                    $DraftBeneficiaryContact->ward_id = $validated['selectedGpWard'];
                    $DraftBeneficiaryContact->block_id = null;
                    $DraftBeneficiaryContact->panchayat_id = null;
                }
                $DraftBeneficiaryContact->save();
                $this->dispatch('conDet', [
                    'message' => "Contact Details updated successfully for the application id: {$this->application_id}"
                ]);
                $this->dispatch('hideLoader');
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    public function render()
    {
        return view('livewire.contact-details');
    }
}
