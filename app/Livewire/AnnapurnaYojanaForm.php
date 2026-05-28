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

    // Form Navigation & Tabs
    public $activeMemberIndex = 0; // 0 = HOF, 1+ = Members
    public $activeSection = 'basic'; // basic, identity, bank, income, declaration

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
    public bool $showSubmitModal = false;

    public function mount($schemeId = 21, $schemeName = 'Annapurna Yojana', $grievanceId = null)
    {
        $this->schemeId = $schemeId;
        $this->schemeName = $schemeName;
        $this->grievanceId = $grievanceId;
        $this->activeMemberIndex = 0;
        $this->activeSection = 'basic';

        // Initialize form structures
        $this->formData = [
            // HOF Details
            'hof_name' => '',
            'hof_dob' => '',
            'hof_gender' => '',
            'hof_aadhaar' => '',
            'hof_applying_for_ay' => 'Yes',
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

            // Ration details (Section B)
            'has_digital_ration_card' => '',
            'ration_card_type' => '', 
            'is_lifting_ration' => '',

            // Assets details (Section C)
            'has_pucca_rooms' => '',
            'owns_land' => '',
            'land_size_decimals' => '',
            'owns_4_wheeler' => '',
            'num_vehicles' => '',
            'vehicle_reg_no' => '',
            'vehicle_model' => '',

            // HOF Assets (Health Insurance)
            'health_insurance_type' => 'None', // None / Government / Private
            'health_insurance_premium' => '',
            'health_insurance_sum_assured' => '',

            // Income / Profession details (Section D)
            'pays_tax' => '',
            'hof_pan_no' => '',
            'hof_employment_nature' => '',
            'num_literate_adults' => '',
            'num_illiterate_adults' => '',
            'hof_literate_status' => '', // Literate / Illiterate
            'hof_highest_qualification' => '',
            'has_constitutional_post' => '',
            'constitutional_post_details' => '',
            'has_pensioner' => '',
            'pensioner_details' => '',
            'has_gst_reg' => '',
            'gstin' => '',
            'total_annual_income' => '',

            // Other identity documents (Section E)
            'hof_caa_status' => 'Not Applicable', // Not Applicable / Applied / Issued
            'hof_caa_app_no' => '',
            'hof_caa_cert_no' => '',
            
            // HOF Other Credit Cards (KCC, Student CC, etc.)
            'hof_kcc_type' => '', // None / KCC / KCC ARD / Artisan Credit Card / MJCC / Student CC / Others
            'hof_kcc_id_no' => '',
            'hof_kcc_date' => '',
            'hof_kcc_issuing_authority' => '',
            
            // HOF SIR status
            'hof_sir_status' => 'Not Applicable', // Not Applicable / No / Yes
            'hof_sir_case_details' => '',

            // Children details (Section F)
            'children_school' => [
                ['name' => '', 'grade' => '', 'school_name' => '', 'school_type' => '', 'school_type_other' => ''],
            ],
            'children_vaccination' => [
                ['name' => '', 'status' => '', 'card_id' => '', 'last_vaccination_date' => '', 'reason_skipped' => ''],
            ],

            // Government Scheme Benefits (Section G)
            'hof_has_dbt_benefits' => 'No',
            'hof_dbt_benefits' => [
                ['scheme_name' => '', 'opt_out' => false],
                ['scheme_name' => '', 'opt_out' => false],
                ['scheme_name' => '', 'opt_out' => false],
                ['scheme_name' => '', 'opt_out' => false],
                ['scheme_name' => '', 'opt_out' => false],
            ],

            // Declaration & Consent (Section H)
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
            
            // Section B (Ration Card / Food Subsidy)
            'has_digital_ration_card' => '',
            'ration_card_no' => '',
            'ration_card_type' => '',
            'is_lifting_ration' => '',
            
            // Section C (Health Insurance)
            'health_insurance_type' => 'No',
            'health_insurance_premium' => '',
            'health_insurance_sum_assured' => '',

            // Section D (Income/PAN/Education)
            'pan_no' => '',
            'employment_nature' => '',
            'literate_status' => '',
            'highest_qualification' => '',

            // Section E (CAA / KCC / SIR)
            'caa_status' => 'Not Applicable',
            'caa_app_no' => '',
            'caa_cert_no' => '',
            'kcc_type' => '',
            'kcc_id_no' => '',
            'kcc_date' => '',
            'kcc_issuing_authority' => '',
            'sir_status' => 'Not Applicable',
            'sir_case_details' => '',

            // Section G (Government Scheme Benefits)
            'has_dbt_benefits' => 'No',
            'dbt_benefits' => [
                ['scheme_name' => '', 'opt_out' => false],
                ['scheme_name' => '', 'opt_out' => false],
                ['scheme_name' => '', 'opt_out' => false],
                ['scheme_name' => '', 'opt_out' => false],
                ['scheme_name' => '', 'opt_out' => false],
            ],
        ];
        $this->formData['num_family_members'] = count($this->members) + 1;
        
        // Auto-switch to the new member tab on basic info section
        $this->activeMemberIndex = count($this->members);
        $this->activeSection = 'basic';
    }

    public function removeMember($index)
    {
        unset($this->members[$index]);
        $this->members = array_values($this->members);
        $this->formData['num_family_members'] = count($this->members) + 1;

        if ($this->activeMemberIndex > count($this->members)) {
            $this->activeMemberIndex = 0;
        }
    }

    public function selectMember($index)
    {
        $this->activeMemberIndex = $index;
    }

    public function addChildSchool()
    {
        $this->formData['children_school'][] = [
            'name' => '',
            'grade' => '',
            'school_name' => '',
            'school_type' => '',
            'school_type_other' => ''
        ];
    }

    public function removeChildSchool($index)
    {
        unset($this->formData['children_school'][$index]);
        $this->formData['children_school'] = array_values($this->formData['children_school']);
    }

    public function addChildVaccination()
    {
        $this->formData['children_vaccination'][] = [
            'name' => '',
            'status' => '',
            'card_id' => '',
            'last_vaccination_date' => '',
            'reason_skipped' => ''
        ];
    }

    public function removeChildVaccination($index)
    {
        unset($this->formData['children_vaccination'][$index]);
        $this->formData['children_vaccination'] = array_values($this->formData['children_vaccination']);
    }

    public function selectSection($section)
    {
        $this->activeSection = $section;
    }

    public function getSections()
    {
        return [
            'basic' => ['label' => 'Basic Info', 'bengali' => 'প্রাথমিক তথ্য'],
            'identity' => ['label' => 'Identity Docs', 'bengali' => 'পরিচয়পত্র'],
            'health' => ['label' => 'Health & Insurance', 'bengali' => 'স্বাস্থ্য ও বীমা'],
            'education' => ['label' => 'Education', 'bengali' => 'শিক্ষা'],
            'income' => ['label' => 'Income & Assets', 'bengali' => 'আয় ও সম্পদ'],
            'declaration' => ['label' => 'Declaration', 'bengali' => 'ঘোষণা ও সম্মতি'],
        ];
    }

    public function nextSection()
    {
        $this->validateSection($this->activeSection);
        
        $sections = array_keys($this->getSections());
        $currentIndex = array_search($this->activeSection, $sections);
        
        if ($currentIndex !== false && $currentIndex < count($sections) - 1) {
            $this->activeSection = $sections[$currentIndex + 1];
        }
    }

    public function previousSection()
    {
        $sections = array_keys($this->getSections());
        $currentIndex = array_search($this->activeSection, $sections);
        
        if ($currentIndex !== false && $currentIndex > 0) {
            $this->activeSection = $sections[$currentIndex - 1];
        }
    }

    public function validateSection($section)
    {
        $this->successMessage = null;
        $this->errorMessage = null;

        $rules = [];
        $messages = [];

        if ($section === 'basic') {
            if ($this->activeMemberIndex === 0) {
                $rules = [
                    'formData.hof_name' => 'required|string|max:255',
                    'formData.hof_dob' => 'required|date',
                    'formData.hof_gender' => 'required',
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
                ];

                if (in_array($this->formData['category'], ['SC', 'ST', 'OBC'])) {
                    $rules['formData.caste_certificate_no'] = 'required|string|max:100';
                } elseif ($this->formData['category'] == 'UR-EWS') {
                    $rules['formData.ews_certificate_no'] = 'required|string|max:100';
                } elseif ($this->formData['category'] == 'PVTG') {
                    $rules['formData.pvtg_certificate_no'] = 'required|string|max:100';
                }

                $messages = [
                    'formData.hof_name.required' => 'Head of Family name is required.',
                    'formData.hof_dob.required' => 'HOF Date of Birth is required.',
                    'formData.hof_gender.required' => 'HOF Gender is required.',
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
                ];
            } else {
                $index = $this->activeMemberIndex - 1;
                $rules = [
                    "members.{$index}.name" => 'required|string|max:255',
                    "members.{$index}.dob" => 'required|date',
                    "members.{$index}.gender" => 'required',
                    "members.{$index}.relation" => 'required',
                ];
                $messages = [
                    "members.{$index}.name.required" => 'Member name is required.',
                    "members.{$index}.dob.required" => 'Member DOB is required.',
                    "members.{$index}.gender.required" => 'Member Gender is required.',
                    "members.{$index}.relation.required" => 'Member Relation is required.',
                ];
            }
        } elseif ($section === 'identity') {
            if ($this->activeMemberIndex === 0) {
                $rules = [
                    'formData.hof_aadhaar' => 'required|digits:12',
                    'formData.has_digital_ration_card' => 'required',
                    'formData.is_lifting_ration' => 'required',
                    'formData.hof_bank_name' => 'required',
                    'formData.hof_acc_no' => 'required',
                    'formData.hof_ifsc' => 'required|size:11',
                ];
                $messages = [
                    'formData.hof_aadhaar.required' => 'HOF Aadhaar number is required.',
                    'formData.hof_aadhaar.digits' => 'HOF Aadhaar must be 12 digits.',
                    'formData.has_digital_ration_card.required' => 'Ration card selection is required.',
                    'formData.is_lifting_ration.required' => 'Ration lifting status selection is required.',
                    'formData.hof_bank_name.required' => 'HOF Bank Name is required.',
                    'formData.hof_acc_no.required' => 'HOF Account Number is required.',
                    'formData.hof_ifsc.required' => 'HOF IFSC is required.',
                    'formData.hof_ifsc.size' => 'HOF IFSC must be 11 characters.',
                ];
            } else {
                $index = $this->activeMemberIndex - 1;
                $rules = [
                    "members.{$index}.aadhaar" => 'nullable|digits:12',
                ];
                $messages = [
                    "members.{$index}.aadhaar.digits" => 'Member Aadhaar must be 12 digits.',
                ];

                $member = $this->members[$index];
                if (($member['applying_for_ay'] ?? 'No') === 'Yes') {
                    $rules["members.{$index}.bank_name"] = 'required';
                    $rules["members.{$index}.acc_no"] = 'required';
                    $rules["members.{$index}.ifsc"] = 'required|size:11';

                    $messages["members.{$index}.bank_name.required"] = 'Member bank name is required since they are applying for AY.';
                    $messages["members.{$index}.acc_no.required"] = 'Member bank account number is required since they are applying for AY.';
                    $messages["members.{$index}.ifsc.required"] = 'Member IFSC is required since they are applying for AY.';
                    $messages["members.{$index}.ifsc.size"] = 'Member IFSC must be 11 characters.';
                }
            }
        } elseif ($section === 'health') {
            $rules = [];
            $messages = [];
        } elseif ($section === 'education') {
            $rules = [];
            $messages = [];
        } elseif ($section === 'income') {
            if ($this->activeMemberIndex === 0) {
                $rules = [
                    'formData.owns_4_wheeler' => 'required',
                    'formData.total_annual_income' => 'required|numeric|min:0',
                ];
                $messages = [
                    'formData.owns_4_wheeler.required' => '4-wheeler ownership selection is required.',
                    'formData.total_annual_income.required' => 'Annual Income is required.',
                    'formData.total_annual_income.numeric' => 'Annual Income must be a number.',
                ];
            }
        }

        if (!empty($rules)) {
            $this->validate($rules, $messages);
        }
    }

    public function resetForm()
    {
        $this->activeMemberIndex = 0;
        $this->activeSection = 'basic';
        $this->mount($this->schemeId, $this->schemeName, $this->grievanceId);
    }

    public function showConfirmation()
    {
        $this->successMessage = null;
        $this->errorMessage = null;

        // 1. Validate declaration consent first
        $this->validate([
            'formData.agree_consent' => 'accepted',
        ], [
            'formData.agree_consent.accepted' => 'You must accept the declaration and consent.',
        ]);

        // 2. Validate all sections for HOF (index 0)
        $rules = [
            'formData.hof_name' => 'required|string|max:255',
            'formData.hof_dob' => 'required|date',
            'formData.hof_gender' => 'required',
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
            
            'formData.hof_aadhaar' => 'required|digits:12',
            'formData.has_digital_ration_card' => 'required',
            'formData.is_lifting_ration' => 'required',

            'formData.hof_bank_name' => 'required',
            'formData.hof_acc_no' => 'required',
            'formData.hof_ifsc' => 'required|size:11',

            'formData.owns_4_wheeler' => 'required',
            'formData.total_annual_income' => 'required|numeric|min:0',
        ];

        if (in_array($this->formData['category'], ['SC', 'ST', 'OBC'])) {
            $rules['formData.caste_certificate_no'] = 'required|string|max:100';
        } elseif ($this->formData['category'] == 'UR-EWS') {
            $rules['formData.ews_certificate_no'] = 'required|string|max:100';
        } elseif ($this->formData['category'] == 'PVTG') {
            $rules['formData.pvtg_certificate_no'] = 'required|string|max:100';
        }

        $messages = [
            'formData.hof_name.required' => 'Head of Family name is required.',
            'formData.hof_dob.required' => 'HOF Date of Birth is required.',
            'formData.hof_gender.required' => 'HOF Gender is required.',
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
            
            'formData.hof_aadhaar.required' => 'HOF Aadhaar number is required.',
            'formData.hof_aadhaar.digits' => 'HOF Aadhaar must be 12 digits.',
            'formData.has_digital_ration_card.required' => 'Ration card selection is required.',
            'formData.is_lifting_ration.required' => 'Ration lifting status selection is required.',
            
            'formData.hof_bank_name.required' => 'HOF Bank Name is required.',
            'formData.hof_acc_no.required' => 'HOF Account Number is required.',
            'formData.hof_ifsc.required' => 'HOF IFSC is required.',
            'formData.hof_ifsc.size' => 'HOF IFSC must be 11 characters.',

            'formData.owns_4_wheeler.required' => '4-wheeler ownership selection is required.',
            'formData.total_annual_income.required' => 'Annual Income is required.',
            'formData.total_annual_income.numeric' => 'Annual Income must be a number.',
        ];

        // Validate each member
        foreach ($this->members as $index => $member) {
            $rules["members.{$index}.name"] = 'required|string|max:255';
            $rules["members.{$index}.dob"] = 'required|date';
            $rules["members.{$index}.gender"] = 'required';
            $rules["members.{$index}.relation"] = 'required';
            $rules["members.{$index}.aadhaar"] = 'nullable|digits:12';
            
            $messages["members.{$index}.name.required"] = 'Member #' . ($index + 1) . ' name is required.';
            $messages["members.{$index}.dob.required"] = 'Member #' . ($index + 1) . ' DOB is required.';
            $messages["members.{$index}.gender.required"] = 'Member #' . ($index + 1) . ' Gender is required.';
            $messages["members.{$index}.relation.required"] = 'Member #' . ($index + 1) . ' Relation is required.';
            $messages["members.{$index}.aadhaar.digits"] = 'Member #' . ($index + 1) . ' Aadhaar must be 12 digits.';

            if (($member['applying_for_ay'] ?? 'No') === 'Yes') {
                $rules["members.{$index}.bank_name"] = 'required';
                $rules["members.{$index}.acc_no"] = 'required';
                $rules["members.{$index}.ifsc"] = 'required|size:11';

                $messages["members.{$index}.bank_name.required"] = 'Member #' . ($index + 1) . ' bank name is required since they are applying for AY.';
                $messages["members.{$index}.acc_no.required"] = 'Member #' . ($index + 1) . ' account number is required since they are applying for AY.';
                $messages["members.{$index}.ifsc.required"] = 'Member #' . ($index + 1) . ' IFSC is required since they are applying for AY.';
                $messages["members.{$index}.ifsc.size"] = 'Member #' . ($index + 1) . ' IFSC must be 11 characters.';
            }
        }

        try {
            $this->validate($rules, $messages);
            $this->showSubmitModal = true;
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Smart UX redirection to tab containing error
            $firstErrorKey = array_key_first($e->validator->errors()->toArray());
            if ($firstErrorKey) {
                if (str_starts_with($firstErrorKey, 'members.')) {
                    $parts = explode('.', $firstErrorKey);
                    if (isset($parts[1])) {
                        $this->activeMemberIndex = ((int)$parts[1]) + 1;
                        $field = $parts[2] ?? '';
                        if (in_array($field, ['name', 'dob', 'gender', 'relation'])) {
                            $this->activeSection = 'basic';
                        } else {
                            $this->activeSection = 'identity';
                        }
                    }
                } elseif (str_starts_with($firstErrorKey, 'formData.')) {
                    $this->activeMemberIndex = 0; // HOF
                    $field = str_replace('formData.', '', $firstErrorKey);
                    
                    $basicFields = [
                        'hof_name', 'hof_dob', 'hof_gender', 'contact_no', 'category',
                        'caste_certificate_no', 'ews_certificate_no', 'pvtg_certificate_no',
                        'district_id', 'rural_urban', 'blockurban', 'gpward',
                        'village_town', 'police_station', 'post_office', 'pincode'
                    ];
                    
                    $identityFields = [
                        'hof_aadhaar', 'has_digital_ration_card', 'is_lifting_ration',
                        'hof_bank_name', 'hof_acc_no', 'hof_ifsc'
                    ];
                    
                    if (in_array($field, $basicFields)) {
                        $this->activeSection = 'basic';
                    } elseif (in_array($field, $identityFields)) {
                        $this->activeSection = 'identity';
                    } else {
                        $this->activeSection = 'income';
                    }
                }
            }
            throw $e;
        }
    }

    public function closeSubmitModal()
    {
        $this->showSubmitModal = false;
    }

    public function save()
    {
        $this->successMessage = null;
        $this->errorMessage = null;

        try {
            DB::beginTransaction();

            // 1. Generate unique application ID and beneficiary ID from lb_scheme.unique_app_ben_ids
            $applicationId = DB::table('lb_scheme.unique_app_ben_ids')->insertGetId([
                'scheme_id' => $this->schemeId,
                'created_at' => now(),
                'updated_at' => now()
            ], 'application_id');
            
            $uniqueRow = DB::table('lb_scheme.unique_app_ben_ids')->where('application_id', $applicationId)->first();
            $beneficiaryId = $uniqueRow->beneficiary_id;

            // 2. Map caste category to integer
            $casteId = 19; // Default General
            if (($this->formData['category'] ?? '') === 'SC') {
                $casteId = 17;
            } elseif (($this->formData['category'] ?? '') === 'ST') {
                $casteId = 18;
            }

            // 3. Insert family details into lb_scheme.annapurna_yojana_family_details
            DB::table('lb_scheme.annapurna_yojana_family_details')->insert([
                'scheme_id' => $this->schemeId,
                'application_id' => $applicationId,
                'beneficiary_id' => $beneficiaryId,
                'application_type' => 'New',
                'application_date' => now()->format('Y-m-d'),
                'ds_registration_no' => null,
                'ds_date' => null,
                'applicant_name' => $this->formData['hof_name'] ?? null,
                'dob' => !empty($this->formData['hof_dob']) ? $this->formData['hof_dob'] : null,
                'age' => !empty($this->formData['hof_dob']) ? \Illuminate\Support\Carbon::parse($this->formData['hof_dob'])->age : null,
                'gender' => $this->formData['hof_gender'] ?? null,
                'father_husband_name' => null,
                'mother_name' => null,
                'marital_status' => null,
                'spouse_name' => null,
                'caste' => $casteId,
                'caste_certificate_no' => (!empty($this->formData['caste_certificate_no']) ? $this->formData['caste_certificate_no'] : null)
                    ?? (!empty($this->formData['ews_certificate_no']) ? $this->formData['ews_certificate_no'] : null)
                    ?? (!empty($this->formData['pvtg_certificate_no']) ? $this->formData['pvtg_certificate_no'] : null),
                'religion' => null,
                'education' => $this->formData['hof_highest_qualification'] ?? null,
                'occupation' => $this->formData['hof_employment_nature'] ?? null,
                'aadhaar_no' => $this->formData['hof_aadhaar'] ?? null,
                'epic_voter_id' => $this->formData['hof_epic_no'] ?? null,
                'pan_no' => $this->formData['hof_pan_no'] ?? null,
                'mobile_no' => $this->formData['contact_no'] ?? null,
                'email' => null,
                'district_id' => !empty($this->formData['district_id']) ? (int)$this->formData['district_id'] : null,
                'rural_urban' => !empty($this->formData['rural_urban']) ? (int)$this->formData['rural_urban'] : null,
                'blockurban' => !empty($this->formData['blockurban']) ? (int)$this->formData['blockurban'] : null,
                'gpward' => !empty($this->formData['gpward']) ? (int)$this->formData['gpward'] : null,
                'sub_division_id' => null,
                'police_station' => $this->formData['police_station'] ?? null,
                'post_office' => $this->formData['post_office'] ?? null,
                'village_town' => $this->formData['village_town'] ?? null,
                'house_no' => $this->formData['house_no'] ?? null,
                'pincode' => $this->formData['pincode'] ?? null,
                'years_dwelling_wb' => null,
                'ration_card_type' => $this->formData['ration_card_type'] ?? null,
                'ration_card_no' => $this->formData['hof_ration_card_id'] ?? null,
                'ahl_tin' => null,
                'total_family_members' => (int)($this->formData['num_family_members'] ?? 1),
                'monthly_family_income' => !empty($this->formData['total_annual_income']) ? round((float)$this->formData['total_annual_income'] / 12, 2) : null,
                'bpl_seq_no' => null,
                'bpl_id_no' => null,
                'bpl_total_score' => null,
                'land_type' => null,
                'land_area_bigha' => !empty($this->formData['land_size_decimals']) ? round((float)$this->formData['land_size_decimals'] / 33, 2) : null,
                'bank_name' => $this->formData['hof_bank_name'] ?? null,
                'bank_branch' => null,
                'ifsc_code' => $this->formData['hof_ifsc'] ?? null,
                'account_no' => $this->formData['hof_acc_no'] ?? null,
                'account_holder_name' => $this->formData['hof_name'] ?? null,
                'other_details' => json_encode($this->formData),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 4. Insert family members into lb_scheme.annapurna_yojana_family_members
            foreach ($this->members as $member) {
                DB::table('lb_scheme.annapurna_yojana_family_members')->insert([
                    'application_id' => $applicationId,
                    'member_name' => $member['name'] ?? null,
                    'member_dob' => !empty($member['dob']) ? $member['dob'] : null,
                    'member_age' => !empty($member['dob']) ? \Illuminate\Support\Carbon::parse($member['dob'])->age : null,
                    'member_gender' => $member['gender'] ?? null,
                    'member_relation' => $member['relation'] ?? null,
                    'member_aadhaar' => $member['aadhaar'] ?? null,
                    'member_occupation' => $member['employment_nature'] ?? null,
                    'member_income' => null,
                    'is_disabled' => 0,
                    'disability_type' => null,
                    'is_student' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();

            $this->successMessage = "Application submitted successfully! Application ID: " . $applicationId;
            $this->showSubmitModal = false;

            // Reset form
            $this->resetForm();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving Annapurna Yojana application: ' . $e->getMessage());
            $this->errorMessage = "An error occurred while saving the application: " . $e->getMessage();
            $this->showSubmitModal = false;
        }
    }

    public function render()
    {
        return view('livewire.annapurna-yojana-form');
    }
}
