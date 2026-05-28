<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\District;
use App\Models\Block;
use App\Models\Municipality;
use App\Models\Panchayat;
use App\Models\Ward;
use App\Models\UniqueAppBenId;
use App\Models\AnnapurnaYojanaApplication;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AnnapurnaYojanaForm extends Component
{
    public $schemeId;
    public $schemeName;
    public $grievanceId;

    // Form Navigation
    public $currentStep = 1;
    public $totalSteps = 4;

    // Form Data array
    public array $formData = [];

    // Master lists for location dropdowns
    public $districts = [];
    public $blocks = []; 
    public $gps = [];    

    // Dynamic list for family members (max 5)
    public array $members = [];

    // Status / Messages
    public $successMessage = null;
    public $errorMessage = null;

    public function mount($schemeId = 21, $schemeName = 'Annapurna Yojana', $grievanceId = null)
    {
        $this->schemeId = $schemeId;
        $this->schemeName = $schemeName;
        $this->grievanceId = $grievanceId;
        $this->currentStep = 1;

        // Initialize form structures
        $this->formData = [
            // HOF Details
            'hof_name' => '',
            'hof_dob' => '',
            'hof_gender' => '',
            'hof_aadhaar' => '',
            'hof_ration_card_id' => '',
            'num_family_members' => 1,
            'contact_no' => '',
            'category' => '', // UR, UR-EWS, SC, ST, OBC, PVTG
            'caste_certificate_no' => '',
            'ews_certificate_no' => '',
            'pvtg_certificate_no' => '',
            
            // Address details
            'state' => 'West Bengal',
            'district_id' => '',
            'rural_urban' => '', // 1 = Urban, 2 = Rural
            'blockurban' => '',
            'gpward' => '',
            'police_station' => '',
            'post_office' => '',
            'village_town' => '',
            'house_no' => '',
            'pincode' => '',

            // HOF Bank & EPIC details
            'hof_bank_name' => '',
            'hof_acc_no' => '',
            'hof_ifsc' => '',
            'hof_epic_no' => '',
            'hof_ac_part_no' => '',

            // Ration details (Tab 2)
            'has_digital_ration_card' => '',
            'ration_card_type' => '', 
            'is_lifting_ration' => '',

            // Assets details (Tab 2)
            'has_pucca_rooms' => '',
            'owns_land' => '',
            'land_size_decimals' => '',
            'owns_4_wheeler' => '',
            'num_vehicles' => '',
            'vehicle_reg_no' => '',
            'vehicle_model' => '',

            // Income / Profession details (Tab 2)
            'pays_tax' => '',
            'hof_pan_no' => '',
            'hof_employment_nature' => '',
            'num_literate_adults' => '',
            'num_illiterate_adults' => '',
            'hof_education' => '',
            'has_constitutional_post' => '',
            'constitutional_post_details' => '',
            'has_pensioner' => '',
            'pensioner_details' => '',
            'has_gst_reg' => '',
            'gstin' => '',
            'total_annual_income' => '',

            // Other identity documents (Tab 3)
            'hof_caa_status' => 'Not Applicable',
            'hof_caa_no' => '',
            'hof_kcc_status' => '',
            'hof_kcc_no' => '',
            'hof_kcc_date' => '',
            'hof_sir_tribunal_pending' => 'Not Applicable',
            'health_insurance_type' => '',
            'health_insurance_premium' => '',
            'health_insurance_sum_assured' => '',

            // Declaration (Tab 4)
            'agree_consent' => false,
        ];

        // Load all districts
        $this->districts = District::orderBy('name', 'asc')->get();

        // Start with empty members list
        $this->members = [];
    }

    public function updatedFormDataDistrictId($value)
    {
        $this->formData['blockurban'] = '';
        $this->formData['gpward'] = '';
        $this->blocks = [];
        $this->gps = [];
        $this->loadBlocks();
    }

    public function updatedFormDataRuralUrban($value)
    {
        $this->formData['blockurban'] = '';
        $this->formData['gpward'] = '';
        $this->blocks = [];
        $this->gps = [];
        $this->loadBlocks();
    }

    public function updatedFormDataBlockurban($value)
    {
        $this->formData['gpward'] = '';
        $this->gps = [];
        $this->loadGps();
    }

    public function loadBlocks()
    {
        $districtId = $this->formData['district_id'] ?? null;
        $ruralUrban = $this->formData['rural_urban'] ?? null;

        if (!$districtId || !$ruralUrban) {
            return;
        }

        if ($ruralUrban == 2) {
            $this->blocks = Block::where('district_id', $districtId)->orderBy('name', 'asc')->get();
        } else {
            try {
                $this->blocks = Municipality::where('district_id', $districtId)->orderBy('name', 'asc')->get();
                if ($this->blocks->isEmpty()) {
                    $subdivisionIds = DB::table('public.subdivisions')->where('district_id', $districtId)->pluck('id');
                    $this->blocks = Municipality::whereIn('sub_division_id', $subdivisionIds)->orderBy('name', 'asc')->get();
                }
            } catch (\Exception $e) {
                Log::error('Error loading municipalities: ' . $e->getMessage());
                $this->blocks = [];
            }
        }
    }

    public function loadGps()
    {
        $blockUrbanId = $this->formData['blockurban'] ?? null;
        $ruralUrban = $this->formData['rural_urban'] ?? null;

        if (!$blockUrbanId || !$ruralUrban) {
            return;
        }

        if ($ruralUrban == 2) {
            $this->gps = Panchayat::where('block_id', $blockUrbanId)->orderBy('name', 'asc')->get();
        } else {
            $this->gps = Ward::where('municipality_id', $blockUrbanId)->orderBy('name', 'asc')->get();
        }
    }

    public function addMember()
    {
        if (count($this->members) >= 5) {
            session()->flash('member_limit', 'Maximum 5 members can be added.');
            return;
        }

        $this->members[] = [
            'name' => '',
            'dob' => '',
            'gender' => '',
            'relation' => '',
            'aadhaar' => '',
            'applying_for_ay' => 'No',
            'bank_name' => '',
            'acc_no' => '',
            'ifsc' => '',
            'epic_no' => '',
            'ac_part_no' => '',
            'pan_no' => '',
            'employment_nature' => '',
            'education' => '',
            'caa_status' => 'Not Applicable',
            'caa_no' => '',
            'kcc_status' => '',
            'kcc_no' => '',
            'kcc_date' => '',
            'sir_tribunal_pending' => 'Not Applicable',
            'health_insurance_type' => '',
            'health_insurance_premium' => '',
            'health_insurance_sum_assured' => '',
        ];
        $this->formData['num_family_members'] = count($this->members) + 1;
    }

    public function removeMember($index)
    {
        unset($this->members[$index]);
        $this->members = array_values($this->members);
        $this->formData['num_family_members'] = count($this->members) + 1;
    }

    public function nextStep()
    {
        $this->validateStep();
        $this->currentStep++;
    }

    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function validateStep()
    {
        if ($this->currentStep == 1) {
            $rules = [
                'formData.hof_name' => 'required|string|max:255',
                'formData.hof_dob' => 'required|date',
                'formData.hof_gender' => 'required',
                'formData.hof_aadhaar' => 'required|digits:12',
                'formData.contact_no' => 'required|digits:10',
                'formData.category' => 'required',
                'formData.district_id' => 'required',
                'formData.rural_urban' => 'required',
                'formData.blockurban' => 'required',
                'formData.gpward' => 'required',
                'formData.village_town' => 'required',
                'formData.police_station' => 'required',
                'formData.post_office' => 'required',
                'formData.pincode' => 'required|digits:6',
                'formData.hof_bank_name' => 'required',
                'formData.hof_acc_no' => 'required',
                'formData.hof_ifsc' => 'required|size:11',
            ];

            // Conditional validation for category certificates
            if (in_array($this->formData['category'], ['SC', 'ST', 'OBC'])) {
                $rules['formData.caste_certificate_no'] = 'required|string|max:100';
            } elseif ($this->formData['category'] == 'UR-EWS') {
                $rules['formData.ews_certificate_no'] = 'required|string|max:100';
            } elseif ($this->formData['category'] == 'PVTG') {
                $rules['formData.pvtg_certificate_no'] = 'required|string|max:100';
            }

            // Dynamic Members Validation
            foreach ($this->members as $index => $member) {
                $rules["members.{$index}.name"] = 'required|string|max:255';
                $rules["members.{$index}.dob"] = 'required|date';
                $rules["members.{$index}.gender"] = 'required';
                $rules["members.{$index}.relation"] = 'required';
                $rules["members.{$index}.aadhaar"] = 'nullable|digits:12';
                $rules["members.{$index}.bank_name"] = 'required';
                $rules["members.{$index}.acc_no"] = 'required';
                $rules["members.{$index}.ifsc"] = 'required|size:11';
            }

            $messages = [
                'formData.hof_name.required' => 'Head of Family name is required.',
                'formData.hof_dob.required' => 'HOF Date of Birth is required.',
                'formData.hof_gender.required' => 'HOF Gender is required.',
                'formData.hof_aadhaar.required' => 'HOF Aadhaar number is required.',
                'formData.hof_aadhaar.digits' => 'HOF Aadhaar must be 12 digits.',
                'formData.contact_no.required' => 'Contact number is required.',
                'formData.contact_no.digits' => 'Contact must be 10 digits.',
                'formData.category.required' => 'Category is required.',
                'formData.caste_certificate_no.required' => 'Caste Certificate number is required for SC/ST/OBC.',
                'formData.ews_certificate_no.required' => 'EWS Certificate number is required for General (EWS).',
                'formData.pvtg_certificate_no.required' => 'PVTG Certificate number is required for PVTG.',
                'formData.district_id.required' => 'District is required.',
                'formData.rural_urban.required' => 'Rural/Urban is required.',
                'formData.blockurban.required' => 'Block/Municipality is required.',
                'formData.gpward.required' => 'GP/Ward is required.',
                'formData.village_town.required' => 'Village/Town is required.',
                'formData.police_station.required' => 'Police Station is required.',
                'formData.post_office.required' => 'Post Office is required.',
                'formData.pincode.required' => 'Pincode is required.',
                'formData.pincode.digits' => 'Pincode must be 6 digits.',
                'formData.hof_bank_name.required' => 'HOF Bank Name is required.',
                'formData.hof_acc_no.required' => 'HOF Account Number is required.',
                'formData.hof_ifsc.required' => 'HOF IFSC is required.',
                'formData.hof_ifsc.size' => 'HOF IFSC must be 11 characters.',
                'members.*.name.required' => 'Member name is required.',
                'members.*.dob.required' => 'Member DOB is required.',
                'members.*.gender.required' => 'Member Gender is required.',
                'members.*.relation.required' => 'Member Relation is required.',
                'members.*.aadhaar.digits' => 'Member Aadhaar must be 12 digits.',
                'members.*.bank_name.required' => 'Member bank name is required.',
                'members.*.acc_no.required' => 'Member bank account number is required.',
                'members.*.ifsc.required' => 'Member IFSC is required.',
                'members.*.ifsc.size' => 'Member IFSC must be 11 characters.',
            ];

            $this->validate($rules, $messages);
        } elseif ($this->currentStep == 2) {
            $this->validate([
                'formData.has_digital_ration_card' => 'required',
                'formData.is_lifting_ration' => 'required',
                'formData.owns_4_wheeler' => 'required',
                'formData.total_annual_income' => 'required|numeric|min:0',
            ], [
                'formData.has_digital_ration_card.required' => 'Ration card selection is required.',
                'formData.is_lifting_ration.required' => 'Ration lifting status selection is required.',
                'formData.owns_4_wheeler.required' => '4-wheeler ownership selection is required.',
                'formData.total_annual_income.required' => 'Annual Income is required.',
                'formData.total_annual_income.numeric' => 'Annual Income must be a number.',
            ]);
        }
    }

    public function save()
    {
        $this->successMessage = null;
        $this->errorMessage = null;

        // Step 4 is Declaration/Consent validation
        $this->validate([
            'formData.agree_consent' => 'accepted',
        ], [
            'formData.agree_consent.accepted' => 'You must accept the declaration and consent.',
        ]);

        try {
            DB::beginTransaction();

            // Generate unique application ID
            $uniqueRow = UniqueAppBenId::create([
                'scheme_id' => $this->schemeId,
            ]);
            $applicationId = $uniqueRow->application_id;

            // Merge members into formData to save all together
            $fullData = $this->formData;
            $fullData['members'] = $this->members;
            $fullData['application_id'] = $applicationId;
            $fullData['scheme_id'] = $this->schemeId;

            // Save in our simple JSON table
            AnnapurnaYojanaApplication::create([
                'application_id' => $applicationId,
                'scheme_id' => $this->schemeId,
                'form_data' => $fullData
            ]);

            DB::commit();

            $this->successMessage = "Application submitted successfully! Application ID: " . $applicationId;
            
            // Reset form
            $this->mount($this->schemeId, $this->schemeName, $this->grievanceId);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving Annapurna Yojana application: ' . $e->getMessage());
            $this->errorMessage = "An error occurred while saving the application: " . $e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.annapurna-yojana-form');
    }
}
