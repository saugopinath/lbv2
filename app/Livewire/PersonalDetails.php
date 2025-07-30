<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Carbon;
use App\Models\Codemaster;
use App\Models\UniqueAppBenId;
use App\Models\BeneficiaryAadhaar;
use App\Models\DraftBeneficiaryPersonal;
use App\Models\DraftBeneficiaryRelationship;

class PersonalDetails extends Component
{
    public $app_types, $genders, $castes = [];
    public $mode;
    public $app_type, $app_date, $reg_no, $ds_date;
    public $name, $mobile, $email, $dob, $age;
    public $ffname, $fmname, $flname;
    public $mfname, $mmname, $mlname;
    public $sfname, $smname, $slname;
    public $caste, $cas_cer_no;
    public function updatedDob($value)
    {
        try {
            $this->age = Carbon::createFromFormat('d-m-Y', $value)->age;
        } catch (\Exception $e) {
            $this->age = null;
        }
    }
    public function rules()
    {
        $rules = [
            'app_type' => 'required',
            'app_date' => 'required|date',
            'name' => 'required|string',
            'mobile' => 'required|digits:10',
            'dob' => 'required|date',
            'age' => 'required|numeric',
            'ffname' => 'required|string',
            'flname' => 'required|string',
            'mfname' => 'required|string',
            'mlname' => 'required|string',
            'caste' => 'required',
        ];
        if ($this->app_type == '30') {
            $rules['reg_no'] = 'required|string';
            $rules['ds_date'] = 'required|date';
        }
        if ($this->caste != '19') {
            $rules['cas_cer_no'] = 'required|string';
        }
        return $rules;
    }
    public function mount($mode = null)
    {
        $this->mode = $mode;
        $app_type = Codemaster::find(4);
        $this->app_types = $app_type->children;
        $caste = Codemaster::find(1);
        $this->castes = $caste->children;
    }
    public function save($actiontype)
    {
        if ($actiontype == 1) {
            $hash = Session::get('aadhar_hash');
            $validated = $this->validate($this->rules());
            $uniqueApp = UniqueAppBenId::create();
            BeneficiaryAadhaar::create([
                'application_id' => $uniqueApp->application_id,
                'aadhar_hash' => $hash,
                'created_by' => 1,
                'encoded_aadhar' => 1,
            ]);
            $draftbenPar = DraftBeneficiaryPersonal::create([
                'application_id' => $uniqueApp->application_id,
                'full_name' => $this->name,
                'dob' => $this->dob,
                'mobile_no' => $this->mobile,
                'entry_type' => $this->app_type,
                'caste' => $this->caste,
                'district_id' => 318,
                'gender' => 32,
                'next_level_role_id' => 20,
                'marital_status' => 24,
                'is_final_submit' => 0,
                'is_faulty' => 0,
                'created_by' => 1,
            ]);
            DraftBeneficiaryRelationship::create([
                'application_id' => $draftbenPar->application_id,
                'full_name' => trim($this->ffname . ' ' . $this->flname),
                'created_by' => 1,
                'relation_type_id' => 79,
                'code' => 'fname'
            ]);
            DraftBeneficiaryRelationship::create([
                'application_id' => $draftbenPar->application_id,
                'full_name' => trim($this->mfname . ' ' . $this->mlname),
                'created_by' => 1,
                'relation_type_id' => 80,
                'code' => 'mname'
            ]);
            Session::forget('aadhar_hash');
            $this->dispatch('perDet');
        } else {
        }
    }
    public function render()
    {
        return view('livewire.personal-details');
    }
}
