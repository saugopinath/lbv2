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
    public $app_types, $genders, $castes, $mar_status = [];
    public $mode, $currentDate;
    public $app_type, $app_date, $reg_no, $ds_date, $id;
    public $name, $mobile, $email, $dob, $age, $mar_statu;
    public $ffname, $mfname, $sfname;
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
            'mfname' => 'required|string',
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
    public function mount($mode = null, $id = null)
    {
        $this->currentDate = Carbon::now()->format('d/m/Y');
        $this->mode = $mode;
        $app_typee = Codemaster::find(4);
        $this->app_types = $app_typee->children;
        $this->mar_status = Codemaster::find(3)->children()->where('id', '!=', 28)->get();
        $caste = Codemaster::find(1);
        $this->castes = $caste->children;
        if ($id != null) {
            $this->id = $id;
            $app_det = DraftBeneficiaryPersonal::with('relationships')->where('application_id', $id)->first();
            $this->app_type = $app_det->entry_type;
            $this->app_date = $app_det->created_at->format('d-m-Y');
            if ($this->app_type == 30) {
                $this->ds_date = Carbon::parse($app_det->ds_date)->format('d-m-Y');
                $this->reg_no = $app_det->ds_registration_no;
            }
            $this->name = $app_det->full_name;
            $this->mobile = $app_det->mobile_no;
            $this->dob = Carbon::parse($app_det->dob)->format('d-m-Y');
            $this->ffname = $app_det->relationships->firstWhere('code', 'fname')->full_name;
            $this->mfname = $app_det->relationships->firstWhere('code', 'mname')->full_name;
            $this->mar_statu = $app_det->marital_status;
            $this->caste = $app_det->caste;
            $this->updatedDob($this->dob);
            $this->age;
        }
    }
    public function getHideAppTypeSectionProperty()
    {
        return $this->mode == 0 && empty($this->id);
    }
    public function save()
    {
        $validated = $this->validate($this->rules());
        if ($this->mode === null) {
            // $hash = Session::get('aadhar_hash');
            $aadhaar_data = Session::get('aadhaar_data');
            $encoded = $aadhaar_data['encoded'];
            $hash = $aadhaar_data['hash'];
            $uniqueApp = UniqueAppBenId::create();
            BeneficiaryAadhaar::create([
                'application_id' => $uniqueApp->application_id,
                'aadhar_hash' => $hash,
                'created_by' => 1,
                'encoded_aadhar' => $encoded,
            ]);
            $draftbenPar = DraftBeneficiaryPersonal::create([
                'application_id' => $uniqueApp->application_id,
                'full_name' => $validated['name'],
                'dob' => $validated['dob'],
                'mobile_no' => $validated['mobile'],
                'entry_type' => $validated['app_type'],
                'caste' => $validated['caste'],
                'ds_date' => $validated['ds_date'],
                'ds_registration_no' => $validated['reg_no'],
                'district_id' => 318,
                'next_level_role_id' => 20,
                'marital_status' => 24,
                'is_final_submit' => 0,
                'is_faulty' => 0,
                'created_by' => 1,
            ]);
            DraftBeneficiaryRelationship::create([
                'application_id' => $draftbenPar->application_id,
                'full_name' => trim($validated['ffname']),
                'created_by' => 1,
                'relation_type_id' => 79,
            ]);
            DraftBeneficiaryRelationship::create([
                'application_id' => $draftbenPar->application_id,
                'full_name' => trim($validated['mfname']),
                'created_by' => 1,
                'relation_type_id' => 80,
            ]);
            Session::forget('aadhar_hash');
        } else {
            DraftBeneficiaryPersonal::where('application_id', $this->id)->update([
                'full_name' => $validated['name'],
                'dob' => $validated['dob'],
                'mobile_no' => $validated['mobile'],
                'entry_type' => $validated['app_type'],
                'caste' => $validated['caste'],
                'ds_date' => $validated['ds_date'],
                'ds_registration_no' => $validated['reg_no'],
            ]);
            DraftBeneficiaryRelationship::where('application_id', $this->id)
                ->where('code', 'fname')
                ->update([
                    'full_name' => trim($validated['ffname']),
                ]);
            DraftBeneficiaryRelationship::where('application_id', $this->id)
                ->where('code', 'mname')
                ->update([
                    'full_name' => trim($validated['mfname']),
                ]);
        }
        $this->dispatch('perDet');
    }
    public function render()
    {
        return view('livewire.personal-details');
    }
}
