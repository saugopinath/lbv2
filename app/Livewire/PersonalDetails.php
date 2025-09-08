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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use App\Traits\WithLiveValidation;
use Illuminate\Support\Facades\DB;

class PersonalDetails extends Component
{
    public $app_types, $genders, $castes, $mar_status = [];
    public $mode, $currentDate, $minDOB, $maxDOB, $cdate, $pdate;
    public $app_type, $app_date, $reg_no, $ds_date, $application_id;
    public $name, $mobile, $email, $dob, $age, $mar_statu;
    public $ffname, $mfname, $sfname;
    public $caste, $cas_cer_no, $encoded, $hash;
    public function updatedDob($value)
    {
        try {
            $this->age = Carbon::createFromFormat('Y-m-d', $value)->age;
        } catch (\Exception $e) {
            $this->age = null;
        }
    }
    public function rules()
    {
        $rules = [
            'app_type'   => 'required',
            'app_date'   => 'required|date',
            'name'       => 'required|string|regex:/^[a-zA-Z\s]+$/',
            'mobile'     => 'required',
            'dob'        => "required|date|after_or_equal:{$this->minDOB}|before_or_equal:{$this->maxDOB}",
            'age'        => 'required|integer|between:25,60',
            'ffname'     => 'required|string|regex:/^[a-zA-Z\s]+$/',
            'mfname'     => 'required|string|regex:/^[a-zA-Z\s]+$/',
            'caste'      => 'required',
            'mar_statu'  => 'required',
        ];
        if ($this->app_type == Codemaster::getIdByCode(42)) {
            $rules['reg_no'] = 'required|string';
            $rules['ds_date'] = 'required|date';
        }
        if ($this->caste != Codemaster::getIdByCode(173)) {
            $rules['cas_cer_no'] = 'required|string';
        }
        if (
            $this->mar_statu == Codemaster::getIdByCode(32) ||
            $this->mar_statu == Codemaster::getIdByCode(34)
        ) {
            $rules['sfname'] = 'required|string|regex:/^[a-zA-Z\s]+$/';
        }
        if (!empty($this->email)) {
            $rules['email'] = 'email';
        }
        return $rules;
    }
    public function messages()
    {
        return [
            'app_type.*'   => 'Please select an application type.',
            'app_date.*'   => 'Please enter a valid application date.',
            'name.*'       => 'Full name is required and must contain only letters and spaces.',
            'mobile.*'     => 'Please enter a valid 10-digit mobile number.',
            'dob.*'        => "Date of birth must be between {$this->minDOB} and {$this->maxDOB}.",
            'age.*'        => 'Please enter a valid age between 25 and 60 years.',
            'ffname.*'     => 'Father\'s name is required and must contain only letters and spaces.',
            'mfname.*'     => 'Mother\'s name is required and must contain only letters and spaces.',
            'caste.*'      => 'Please select caste.',
            'mar_statu.*'  => 'Please select marital status.',
            'reg_no.*'     => 'Registration number is required.',
            'ds_date.*'    => 'DS date is required.',
            'cas_cer_no.*' => 'Caste certificate number is required.',
            'sfname.*'     => 'Spouse name is required and must contain only letters and spaces.',
            'email.*'      => 'Please enter a valid email address.',
        ];
    }
    public function mount($mode = null, $application_id = null, $aadhaarData = null)
    {
        if ($aadhaarData) {
            $this->encoded = $aadhaarData['encoded'];
            $this->hash = $aadhaarData['hash'];
        }
        $this->currentDate = Carbon::now()->format('d/m/Y');
        $this->minDOB = now()->subYears(60)->format('Y-m-d');
        $this->maxDOB = now()->subYears(25)->format('Y-m-d');
        $this->cdate = Carbon::now()->format('Y-m-d');
        $this->pdate = Carbon::now()->subYears(2)->format('Y-m-d');
        $this->mode = $mode;
        $this->app_types = Codemaster::where('code', 4)->first()->children()->get();
        $this->mar_status = Codemaster::where('code', 3)->first()->children()->where('code', '!=', 35)->get();
        $this->castes = Codemaster::where('code', 17)->first()->children()->get();
        if ($application_id != null) {
            $this->application_id = $application_id;
            $app_det = DraftBeneficiaryPersonal::with('relationships')->where('application_id', $application_id)->first();
            $this->app_type = $app_det->entry_type;
            $this->app_date = $app_det->created_at->format('d-m-Y');
            if ($this->app_type == Codemaster::getIdByCode(42)) {
                $this->ds_date = Carbon::parse($app_det->ds_date)->format('d-m-Y');
                $this->reg_no = $app_det->ds_registration_no;
            }
            $this->name = $app_det->full_name;
            $this->mobile = $app_det->mobile_no;
            $this->email = $app_det->email;
            $this->dob = Carbon::parse($app_det->dob)->format('Y-m-d');
            $this->ffname = $app_det->relationships->firstWhere('relation_type_id', Codemaster::getIdByCode(131))->full_name;
            $this->mfname = $app_det->relationships->firstWhere('relation_type_id', Codemaster::getIdByCode(132))->full_name;
            $this->mar_statu = $app_det->marital_status;
            if ($this->mar_statu == Codemaster::getIdByCode(32) || $this->mar_statu == Codemaster::getIdByCode(34)) {
                $this->sfname = $app_det->relationships->firstWhere('relation_type_id', Codemaster::getIdByCode(133))->full_name;
            }
            $this->caste = $app_det->caste;
            if ($this->caste != Codemaster::getIdByCode(173)) {
                $this->cas_cer_no = $app_det->caste_certificate_no;
            }
            $this->updatedDob($this->dob);
            $this->age;
        }
    }
    public function getHideAppTypeSectionProperty()
    {
        return $this->mode == 0 && empty($this->application_id);
    }
    public function save()
    {
        $validated = $this->validate($this->rules());
        DB::beginTransaction();
        try {
            if ($this->mode === null && $this->application_id === null) {
                $uniqueApp = UniqueAppBenId::create();
                $data = [
                    'application_id' => $uniqueApp->application_id,
                    'full_name' => $validated['name'],
                    'dob' => $validated['dob'],
                    'mobile_no' => $validated['mobile'],
                    'entry_type' => $validated['app_type'],
                    'caste' => $validated['caste'],
                    'district_id' => Crypt::decryptString(Session::get('lgd_session.district_id')),
                    'next_level_role_id' => Codemaster::getIdByCode(21),
                    'marital_status' => $validated['mar_statu'],
                    'is_final_submit' => 0,
                    'is_faulty' => 0,
                    'created_by' => Auth::id(),
                ];
                if (Crypt::decryptString(Session::get('lgd_session.office_type_id')) == 153) {
                    $data['block_id'] = Crypt::decryptString(Session::get('lgd_session.block_id'));
                } else {
                    $data['sub_division_id'] = Crypt::decryptString(Session::get('lgd_session.subdivision_id'));
                }
                if (!empty($validated['email'])) {
                    $data['email'] = $validated['email'];
                }
                if ($validated['app_type'] == Codemaster::getIdByCode(42)) {
                    $data['ds_date'] = $validated['ds_date'];
                    $data['ds_registration_no'] = $validated['reg_no'];
                }
                if ($validated['caste'] != Codemaster::getIdByCode(173)) {
                    $data['caste_certificate_no'] = $validated['cas_cer_no'];
                }
                $draftbenPar = DraftBeneficiaryPersonal::create($data);
                DraftBeneficiaryRelationship::create([
                    'application_id' => $draftbenPar->application_id,
                    'full_name' => trim($validated['ffname']),
                    'created_by' => Auth::id(),
                    'relation_type_id' => Codemaster::getIdByCode(131),
                ]);
                DraftBeneficiaryRelationship::create([
                    'application_id' => $draftbenPar->application_id,
                    'full_name' => trim($validated['mfname']),
                    'created_by' => Auth::id(),
                    'relation_type_id' => Codemaster::getIdByCode(132),
                ]);
                if ($validated['mar_statu'] == Codemaster::getIdByCode(32) || $validated['mar_statu'] == Codemaster::getIdByCode(34)) {
                    DraftBeneficiaryRelationship::create([
                        'application_id' => $draftbenPar->application_id,
                        'full_name' => trim($validated['sfname']),
                        'created_by' => Auth::id(),
                        'relation_type_id' => Codemaster::getIdByCode(133),
                    ]);
                }
                BeneficiaryAadhaar::create([
                    'application_id' => $uniqueApp->application_id,
                    'aadhar_hash' => $this->hash,
                    'created_by' => Auth::id(),
                    'encoded_aadhar' => $this->encoded,
                ]);
                $this->dispatch('perDet', [
                    'application_id' => $draftbenPar->application_id,
                    'message' => "Personal Details saved successfully and the application id is: {$draftbenPar->application_id}"
                ]);
            } else {
                $data = [
                    'full_name' => $validated['name'],
                    'dob' => $validated['dob'],
                    'mobile_no' => $validated['mobile'],
                    'entry_type' => $validated['app_type'],
                    'caste' => $validated['caste'],
                    'marital_status' => $validated['mar_statu'],
                ];
                if (!empty($validated['email'])) {
                    $data['email'] = $validated['email'];
                }
                if ($validated['app_type'] == Codemaster::getIdByCode(42)) {
                    $data['ds_date'] = $validated['ds_date'];
                    $data['ds_registration_no'] = $validated['reg_no'];
                }
                if ($validated['caste'] != Codemaster::getIdByCode(173)) {
                    $data['caste_certificate_no'] = $validated['cas_cer_no'];
                }
                DraftBeneficiaryPersonal::where('application_id', $this->application_id)->update($data);
                DraftBeneficiaryRelationship::where('application_id', $this->application_id)
                    ->where('relation_type_id', Codemaster::getIdByCode(131))
                    ->update([
                        'full_name' => trim($validated['ffname']),
                    ]);
                DraftBeneficiaryRelationship::where('application_id', $this->application_id)
                    ->where('relation_type_id', Codemaster::getIdByCode(132))
                    ->update([
                        'full_name' => trim($validated['mfname']),
                    ]);
                if ($validated['mar_statu'] == Codemaster::getIdByCode(32) || $validated['mar_statu'] == Codemaster::getIdByCode(34)) {
                    $relation = DraftBeneficiaryRelationship::where('application_id', $this->application_id)
                        ->where('relation_type_id', Codemaster::getIdByCode(133))
                        ->first();
                    if ($relation) {
                        $relation->update([
                            'full_name' => trim($validated['sfname']),
                        ]);
                    } else {
                        DraftBeneficiaryRelationship::create([
                            'application_id' => $this->application_id,
                            'full_name' => trim($validated['sfname']),
                            'created_by' => Auth::id(),
                            'relation_type_id' => Codemaster::getIdByCode(133),
                        ]);
                    }
                } else {
                    DraftBeneficiaryRelationship::where('application_id', $this->application_id)
                        ->where('relation_type_id', Codemaster::getIdByCode(133))
                        ->delete();
                }
                $this->dispatch('perDet', [
                    'application_id' => $this->application_id,
                    'message' => "Personal Details updated successfully for the application id: {$this->application_id}"
                ]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    public function render()
    {
        return view('livewire.personal-details');
    }
}
