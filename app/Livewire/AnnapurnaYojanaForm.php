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
    public $applicationId;

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

    public function mount($schemeId = 21, $schemeName = 'Annapurna Yojana', $grievanceId = null)
    {
        $this->schemeId = $schemeId;
        $this->schemeName = $schemeName;
        $this->grievanceId = $grievanceId;
        $this->applicationId = null;
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

            // For Official Use (Enquiry Report)
            'official_verified_by' => '',
            'official_designation' => '',
            'official_place' => '',
            'official_date' => '',
            'official_status' => '', // Correct / Incorrect
            'official_incorrect_details' => '',
            'official_recommendation' => '', // Acceptance / Rejection
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
                if (count($this->blocks) === 0) {
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
        $member = $this->members[$index] ?? null;
        if ($member && isset($member['db_id'])) {
            try {
                DB::connection('pgsql_apy_uat')
                    ->table('family_members')
                    ->where('id', $member['db_id'])
                    ->delete();
            } catch (\Exception $e) {
                Log::error('Error deleting member from UAT DB: ' . $e->getMessage());
                $this->errorMessage = 'Failed to delete member from database. Please try again.';
            }
        }

        unset($this->members[$index]);
        $this->members = array_values($this->members);
        $this->formData['num_family_members'] = count($this->members) + 1;

        if ($this->activeMemberIndex > count($this->members)) {
            $this->activeMemberIndex = 0;
        }

        $this->saveIncremental();
    }

    public function selectMember($index)
    {
        if ($index > $this->activeMemberIndex) {
            $this->validateSection($this->activeSection);
            $this->saveIncremental();
        } else {
            try {
                $this->validateSection($this->activeSection);
                $this->saveIncremental();
            } catch (\Illuminate\Validation\ValidationException $e) {
                // Allow backward navigation even if current tab is invalid
            }
        }
        $this->activeMemberIndex = $index;
    }

    public function selectSection($section)
    {
        $sections = array_keys($this->getSections());
        $currentIndex = array_search($this->activeSection, $sections);
        $targetIndex = array_search($section, $sections);

        if ($targetIndex > $currentIndex) {
            $this->validateSection($this->activeSection);
            $this->saveIncremental();
        } else {
            try {
                $this->validateSection($this->activeSection);
                $this->saveIncremental();
            } catch (\Illuminate\Validation\ValidationException $e) {
                // Allow backward navigation even if current tab is invalid
            }
        }
        $this->activeSection = $section;
    }

    public function getSections()
    {
        return [
            'basic' => ['label' => 'A. Family Identity', 'bengali' => 'পারিবারিক পরিচিতি'],
            'identity' => ['label' => 'B. Ration Card / Food Subsidy', 'bengali' => 'রেশন কার্ড ও খাদ্য ভর্তুকি'],
            'health' => ['label' => 'Health & Insurance', 'bengali' => 'স্বাস্থ্য ও বীমা'],
            'education' => ['label' => 'Education', 'bengali' => 'শিক্ষা'],
            'income' => ['label' => 'Income & Assets', 'bengali' => 'আয় ও সম্পদ'],
            'declaration' => ['label' => 'Declaration', 'bengali' => 'ঘোষণা ও সম্মতি'],
        ];
    }

    public function nextSection()
    {
        $this->validateSection($this->activeSection);
        $this->saveIncremental();
        
        $sections = array_keys($this->getSections());
        $currentIndex = array_search($this->activeSection, $sections);
        
        if ($currentIndex !== false && $currentIndex < count($sections) - 1) {
            $this->activeSection = $sections[$currentIndex + 1];
        }
    }

    public function previousSection()
    {
        try {
            $this->validateSection($this->activeSection);
            $this->saveIncremental();
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Allow going back even with validation failures
        }

        $sections = array_keys($this->getSections());
        $currentIndex = array_search($this->activeSection, $sections);
        
        if ($currentIndex !== false && $currentIndex > 0) {
            $this->activeSection = $sections[$currentIndex - 1];
        }
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

    public function getFormattedAddress()
    {
        $parts = [];
        if (!empty($this->formData['house_no'])) {
            $parts[] = 'House No: ' . $this->formData['house_no'];
        }
        if (!empty($this->formData['village_town'])) {
            $parts[] = $this->formData['village_town'];
        }
        if (!empty($this->formData['police_station'])) {
            $parts[] = 'PS: ' . $this->formData['police_station'];
        }
        if (!empty($this->formData['post_office'])) {
            $parts[] = 'PO: ' . $this->formData['post_office'];
        }
        if (!empty($this->formData['pincode'])) {
            $parts[] = 'PIN: ' . $this->formData['pincode'];
        }
        if (!empty($this->formData['state'])) {
            $parts[] = $this->formData['state'];
        }
        return implode(', ', $parts);
    }

    public function saveIncremental()
    {
        if (empty($this->applicationId)) {
            $this->applicationId = (string) \Illuminate\Support\Str::uuid();
        }

        try {
            DB::connection('pgsql_apy_uat')->beginTransaction();

            $activeSection = $this->activeSection;

            // Define which fields in familyData belong to which section
            $familySectionFields = [
                'basic' => [
                    'application_id', 'total_family_members', 'area_type', 'ulb', 
                    'address', 'lgd_district_code', 'lgd_block_mc_code', 'lgd_gp_ward_code', 
                    'application_status'
                ],
                'identity' => [
                    'has_digital_ration_card', 'ration_card_household_id', 'lifting_monthly_ration'
                ],
                'health' => [],
                'education' => [],
                'income' => [
                    'has_electricity_connection', 'electricity_consumer_id', 'power_units_consumed', 
                    'no_of_illiterate_adults', 'no_of_literate_adults', 'total_annual_family_income'
                ],
                'declaration' => [
                    'is_agreed', 'application_status'
                ],
            ];

            // Define HOF family_members fields belonging to which section
            $hofSectionFields = [
                'basic' => [
                    'member_name', 'aadhaar_no', 'mobile_no', 'date_of_birth', 'gender', 
                    'social_category', 'bank_name', 'bank_account_no', 'ifsc_code', 
                    'epic_no', 'part_no', 'relation_with_head_of_family', 
                    'lgd_district_code', 'lgd_block_mc_code', 'lgd_gp_ward_code'
                ],
                'identity' => [
                    'digital_ration_card_no', 'digital_ration_card_type', 'caa_application_status', 
                    'caa_application_no', 'caa_certificate_no', 'sir2026tribunal_status', 'sir2026case_details'
                ],
                'health' => [
                    'has_health_insurance', 'health_insurance_type', 
                    'health_insurance_sum_assured', 'health_insurance_annual_premium'
                ],
                'education' => [
                    'literacy_status', 'highest_educational_qualifications'
                ],
                'income' => [
                    'has_four_wheeler', 'vehicle_registration_no', 'vehicle_model', 'vehicle_count', 
                    'govt_employment_type', 'gross_annual_income', 'pays_income_or_professional_tax', 
                    'pan_no', 'holds_constitutional_post', 'is_registered_gst', 'gstin', 'is_govt_pensioner', 
                    'has_three_pucca_rooms', 'landholding_size_decimals', 'owns_land'
                ],
                'declaration' => [],
            ];

            // Define other family_members fields belonging to which section
            $memberSectionFields = [
                'basic' => [
                    'member_name', 'aadhaar_no', 'mobile_no', 'date_of_birth', 'gender', 
                    'social_category', 'bank_name', 'bank_account_no', 'ifsc_code', 
                    'epic_no', 'part_no', 'relation_with_head_of_family', 
                    'lgd_district_code', 'lgd_block_mc_code', 'lgd_gp_ward_code', 
                    'applying_for_annapurna_bhandar', 'is_child', 'is_hof'
                ],
                'identity' => [
                    'digital_ration_card_no', 'digital_ration_card_type', 'caa_application_status', 
                    'caa_application_no', 'caa_certificate_no', 'sir2026tribunal_status', 'sir2026case_details'
                ],
                'health' => [
                    'has_health_insurance', 'health_insurance_type', 
                    'health_insurance_sum_assured', 'health_insurance_annual_premium'
                ],
                'education' => [
                    'literacy_status', 'highest_educational_qualifications', 
                    'school_grade', 'school_name', 'school_type', 'school_type_other', 
                    'vaccination_card_id', 'vaccination_status', 'vaccination_skip_reason_or_date'
                ],
                'income' => [
                    'gross_annual_income', 'govt_employment_type', 'pan_no'
                ],
                'declaration' => [],
            ];

            // Default values for families (insert)
            $defaultFamilyData = [
                'application_id' => $this->applicationId,
                'total_family_members' => count($this->members) + 1,
                'lifting_monthly_ration' => false,
                'has_electricity_connection' => false,
                'electricity_consumer_id' => null,
                'power_units_consumed' => null,
                'is_agreed' => false,
                'application_status' => 'Draft',
                'lgd_district_code' => 0,
                'lgd_block_mc_code' => 0,
                'lgd_gp_ward_code' => 0,
                'address' => '',
                'has_digital_ration_card' => false,
                'ration_card_household_id' => null,
                'no_of_illiterate_adults' => null,
                'no_of_literate_adults' => null,
                'total_annual_family_income' => null,
                'area_type' => null,
                'ulb' => null,
            ];

            // Form data values for families
            $populatedFamilyValues = [
                'application_id' => $this->applicationId,
                'total_family_members' => count($this->members) + 1,
                'lifting_monthly_ration' => ($this->formData['is_lifting_ration'] ?? null) === 'Yes',
                'has_electricity_connection' => ($this->formData['has_electricity_connection'] ?? null) === 'Yes',
                'electricity_consumer_id' => $this->formData['electricity_consumer_id'] ?? null,
                'power_units_consumed' => ($this->formData['power_units_consumed'] ?? '') !== '' ? (float)$this->formData['power_units_consumed'] : null,
                'is_agreed' => ($this->formData['agree_consent'] ?? false) === true,
                'application_status' => $this->formData['application_status'] ?? 'Draft',
                'lgd_district_code' => ($this->formData['district_id'] ?? '') !== '' ? (int)$this->formData['district_id'] : 0,
                'lgd_block_mc_code' => ($this->formData['blockurban'] ?? '') !== '' ? (int)$this->formData['blockurban'] : 0,
                'lgd_gp_ward_code' => ($this->formData['gpward'] ?? '') !== '' ? (int)$this->formData['gpward'] : 0,
                'address' => $this->getFormattedAddress(),
                'has_digital_ration_card' => ($this->formData['has_digital_ration_card'] ?? null) === 'Yes',
                'ration_card_household_id' => $this->formData['hof_ration_card_id'] ?? null,
                'no_of_illiterate_adults' => ($this->formData['num_illiterate_adults'] ?? '') !== '' ? (int)$this->formData['num_illiterate_adults'] : null,
                'no_of_literate_adults' => ($this->formData['num_literate_adults'] ?? '') !== '' ? (int)$this->formData['num_literate_adults'] : null,
                'total_annual_family_income' => ($this->formData['total_annual_income'] ?? '') !== '' ? (float)$this->formData['total_annual_income'] : null,
                'area_type' => ($this->formData['rural_urban'] ?? null) == 2 ? 'Rural' : (($this->formData['rural_urban'] ?? null) == 1 ? 'Urban' : null),
                'ulb' => ($this->formData['rural_urban'] ?? null) == 1 ? $this->formData['blockurban'] : null,
            ];

            // Default values for HOF (insert)
            $defaultHofMemberData = [
                'is_hof' => true,
                'member_name' => '',
                'aadhaar_no' => '',
                'mobile_no' => null,
                'date_of_birth' => null,
                'gender' => null,
                'digital_ration_card_no' => null,
                'digital_ration_card_type' => null,
                'social_category' => null,
                'bank_name' => null,
                'bank_account_no' => null,
                'ifsc_code' => null,
                'epic_no' => null,
                'part_no' => null,
                'caa_application_status' => 'Not Applicable',
                'caa_application_no' => null,
                'caa_certificate_no' => null,
                'sir2026tribunal_status' => 'Not Applicable',
                'sir2026case_details' => null,
                'has_four_wheeler' => false,
                'vehicle_registration_no' => null,
                'vehicle_model' => null,
                'vehicle_count' => null,
                'has_health_insurance' => false,
                'health_insurance_type' => 'None',
                'health_insurance_sum_assured' => null,
                'health_insurance_annual_premium' => null,
                'literacy_status' => null,
                'highest_educational_qualifications' => null,
                'govt_employment_type' => null,
                'gross_annual_income' => null,
                'pays_income_or_professional_tax' => false,
                'pan_no' => null,
                'holds_constitutional_post' => false,
                'is_registered_gst' => false,
                'gstin' => null,
                'is_child' => false,
                'is_govt_pensioner' => false,
                'relation_with_head_of_family' => 'Self',
                'has_three_pucca_rooms' => false,
                'landholding_size_decimals' => null,
                'owns_land' => false,
                'lgd_district_code' => 0,
                'lgd_block_mc_code' => 0,
                'lgd_gp_ward_code' => 0,
            ];

            // Form data values for HOF
            $populatedHofValues = [
                'member_name' => $this->formData['hof_name'] ?? '',
                'aadhaar_no' => $this->formData['hof_aadhaar'] ?? '',
                'mobile_no' => $this->formData['contact_no'] ?? null,
                'date_of_birth' => $this->formData['hof_dob'] ?? null,
                'gender' => $this->formData['hof_gender'] ?? null,
                'digital_ration_card_no' => $this->formData['hof_ration_card_id'] ?? null,
                'digital_ration_card_type' => $this->formData['ration_card_type'] ?? null,
                'social_category' => $this->formData['category'] ?? null,
                'bank_name' => $this->formData['hof_bank_name'] ?? null,
                'bank_account_no' => $this->formData['hof_acc_no'] ?? null,
                'ifsc_code' => $this->formData['hof_ifsc'] ?? null,
                'epic_no' => $this->formData['hof_epic_no'] ?? null,
                'part_no' => $this->formData['hof_ac_part_no'] ?? null,
                'caa_application_status' => $this->formData['hof_caa_status'] ?? 'Not Applicable',
                'caa_application_no' => $this->formData['hof_caa_app_no'] ?? null,
                'caa_certificate_no' => $this->formData['hof_caa_cert_no'] ?? null,
                'sir2026tribunal_status' => $this->formData['hof_sir_status'] ?? 'Not Applicable',
                'sir2026case_details' => $this->formData['hof_sir_case_details'] ?? null,
                'has_four_wheeler' => ($this->formData['owns_4_wheeler'] ?? null) === 'Yes',
                'vehicle_registration_no' => $this->formData['vehicle_reg_no'] ?? null,
                'vehicle_model' => $this->formData['vehicle_model'] ?? null,
                'vehicle_count' => ($this->formData['num_vehicles'] ?? '') !== '' ? (int)$this->formData['num_vehicles'] : null,
                'has_health_insurance' => ($this->formData['health_insurance_type'] ?? 'None') !== 'None',
                'health_insurance_type' => $this->formData['health_insurance_type'] ?? 'None',
                'health_insurance_sum_assured' => ($this->formData['health_insurance_sum_assured'] ?? '') !== '' ? (float)$this->formData['health_insurance_sum_assured'] : null,
                'health_insurance_annual_premium' => ($this->formData['health_insurance_premium'] ?? '') !== '' ? (float)$this->formData['health_insurance_premium'] : null,
                'literacy_status' => $this->formData['hof_literate_status'] ?? null,
                'highest_educational_qualifications' => $this->formData['hof_highest_qualification'] ?? null,
                'govt_employment_type' => $this->formData['hof_employment_nature'] ?? null,
                'gross_annual_income' => ($this->formData['total_annual_income'] ?? '') !== '' ? (float)$this->formData['total_annual_income'] : null,
                'pays_income_or_professional_tax' => ($this->formData['pays_tax'] ?? null) === 'Yes',
                'pan_no' => $this->formData['hof_pan_no'] ?? null,
                'holds_constitutional_post' => ($this->formData['has_constitutional_post'] ?? null) === 'Yes',
                'is_registered_gst' => ($this->formData['has_gst_reg'] ?? null) === 'Yes',
                'gstin' => $this->formData['gstin'] ?? null,
                'is_child' => false,
                'is_govt_pensioner' => ($this->formData['has_pensioner'] ?? null) === 'Yes',
                'relation_with_head_of_family' => 'Self',
                'has_three_pucca_rooms' => ($this->formData['has_pucca_rooms'] ?? null) === 'Yes',
                'landholding_size_decimals' => ($this->formData['land_size_decimals'] ?? '') !== '' ? (float)$this->formData['land_size_decimals'] : null,
                'owns_land' => ($this->formData['owns_land'] ?? null) === 'Yes',
                'lgd_district_code' => ($this->formData['district_id'] ?? '') !== '' ? (int)$this->formData['district_id'] : 0,
                'lgd_block_mc_code' => ($this->formData['blockurban'] ?? '') !== '' ? (int)$this->formData['blockurban'] : 0,
                'lgd_gp_ward_code' => ($this->formData['gpward'] ?? '') !== '' ? (int)$this->formData['gpward'] : 0,
            ];

            // 1. Process Family row
            $familyRecord = DB::connection('pgsql_apy_uat')
                ->table('families')
                ->where('application_id', $this->applicationId)
                ->first();

            if ($familyRecord) {
                $familyId = $familyRecord->id;
                $familyData = [];
                $activeFields = $familySectionFields[$activeSection] ?? [];
                foreach ($activeFields as $field) {
                    if (array_key_exists($field, $populatedFamilyValues)) {
                        $familyData[$field] = $populatedFamilyValues[$field];
                    }
                }
                $familyData['updated_at'] = now();

                if (!empty($familyData)) {
                    DB::connection('pgsql_apy_uat')
                        ->table('families')
                        ->where('id', $familyId)
                        ->update($familyData);
                }
            } else {
                // First save (insert) basic info + defaults
                $familyData = array_merge($defaultFamilyData, $populatedFamilyValues);
                $familyData['created_at'] = now();
                $familyData['updated_at'] = now();

                $familyId = DB::connection('pgsql_apy_uat')
                    ->table('families')
                    ->insertGetId($familyData);
            }

            // 2. Process HOF family member row
            $hofMemberRecord = DB::connection('pgsql_apy_uat')
                ->table('family_members')
                ->where('family_id', $familyId)
                ->where('is_hof', true)
                ->first();

            if ($hofMemberRecord) {
                $hofMemberData = [];
                $activeFields = $hofSectionFields[$activeSection] ?? [];
                foreach ($activeFields as $field) {
                    if (array_key_exists($field, $populatedHofValues)) {
                        $hofMemberData[$field] = $populatedHofValues[$field];
                    }
                }
                $hofMemberData['updated_at'] = now();

                if (!empty($hofMemberData)) {
                    DB::connection('pgsql_apy_uat')
                        ->table('family_members')
                        ->where('id', $hofMemberRecord->id)
                        ->update($hofMemberData);
                }
            } else {
                // First save (insert) HOF details
                $hofMemberData = array_merge($defaultHofMemberData, $populatedHofValues);
                $hofMemberData['family_id'] = $familyId;
                $hofMemberData['created_at'] = now();
                $hofMemberData['updated_at'] = now();

                DB::connection('pgsql_apy_uat')
                    ->table('family_members')
                    ->insert($hofMemberData);
            }

            // 3. Process other family members
            $defaultMemberData = [
                'family_id' => $familyId,
                'is_hof' => false,
                'member_name' => '',
                'aadhaar_no' => '',
                'mobile_no' => null,
                'date_of_birth' => null,
                'gender' => null,
                'digital_ration_card_no' => null,
                'digital_ration_card_type' => null,
                'social_category' => null,
                'bank_name' => null,
                'bank_account_no' => null,
                'ifsc_code' => null,
                'epic_no' => null,
                'part_no' => null,
                'caa_application_status' => 'Not Applicable',
                'caa_application_no' => null,
                'caa_certificate_no' => null,
                'sir2026tribunal_status' => 'Not Applicable',
                'sir2026case_details' => null,
                'has_four_wheeler' => false,
                'has_health_insurance' => false,
                'health_insurance_type' => 'No',
                'health_insurance_sum_assured' => null,
                'health_insurance_annual_premium' => null,
                'pan_no' => null,
                'govt_employment_type' => null,
                'literacy_status' => null,
                'highest_educational_qualifications' => null,
                'is_child' => false,
                'relation_with_head_of_family' => null,
                'applying_for_annapurna_bhandar' => false,
                'school_grade' => null,
                'school_name' => null,
                'school_type' => null,
                'school_type_other' => null,
                'vaccination_card_id' => null,
                'vaccination_status' => null,
                'vaccination_skip_reason_or_date' => null,
                'lgd_district_code' => 0,
                'lgd_block_mc_code' => 0,
                'lgd_gp_ward_code' => 0,
                'pays_income_or_professional_tax' => false,
                'holds_constitutional_post' => false,
                'is_registered_gst' => false,
                'is_govt_pensioner' => false,
            ];

            foreach ($this->members as $index => $member) {
                $schoolGrade = null;
                $schoolName = null;
                $schoolType = null;
                $schoolTypeOther = null;
                $vaccCardId = null;
                $vaccStatus = null;
                $vaccSkip = null;

                $memberNameNormalized = strtolower(trim($member['name'] ?? ''));

                if ($memberNameNormalized !== '') {
                    if (!empty($this->formData['children_school'])) {
                        foreach ($this->formData['children_school'] as $sch) {
                            if (strtolower(trim($sch['name'] ?? '')) === $memberNameNormalized) {
                                $schoolGrade = $sch['grade'] ?? null;
                                $schoolName = $sch['school_name'] ?? null;
                                $schoolType = $sch['school_type'] ?? null;
                                $schoolTypeOther = $sch['school_type_other'] ?? null;
                                break;
                            }
                        }
                    }

                    if (!empty($this->formData['children_vaccination'])) {
                        foreach ($this->formData['children_vaccination'] as $vac) {
                            if (strtolower(trim($vac['name'] ?? '')) === $memberNameNormalized) {
                                $vaccCardId = $vac['card_id'] ?? null;
                                $vaccStatus = $vac['status'] ?? null;
                                if (($vac['status'] ?? '') === 'Yes') {
                                    $vaccSkip = $vac['last_vaccination_date'] ?? null;
                                } else {
                                    $vaccSkip = $vac['reason_skipped'] ?? null;
                                }
                                break;
                            }
                        }
                    }
                }

                $isChild = false;
                if (!empty($member['dob'])) {
                    try {
                        $age = \Carbon\Carbon::parse($member['dob'])->age;
                        if ($age < 18) {
                            $isChild = true;
                        }
                    } catch (\Exception $e) {
                    }
                }

                $populatedMemberValues = [
                    'member_name' => $member['name'] ?? '',
                    'aadhaar_no' => $member['aadhaar'] ?? '',
                    'mobile_no' => $this->formData['contact_no'] ?? null,
                    'date_of_birth' => $member['dob'] ?? null,
                    'gender' => $member['gender'] ?? null,
                    'digital_ration_card_no' => $member['ration_card_no'] ?? null,
                    'digital_ration_card_type' => $member['ration_card_type'] ?? null,
                    'social_category' => $this->formData['category'] ?? null,
                    'bank_name' => $member['bank_name'] ?? null,
                    'bank_account_no' => $member['acc_no'] ?? null,
                    'ifsc_code' => $member['ifsc'] ?? null,
                    'epic_no' => $member['epic_no'] ?? null,
                    'part_no' => $member['ac_part_no'] ?? null,
                    'caa_application_status' => $member['caa_status'] ?? 'Not Applicable',
                    'caa_application_no' => $member['caa_app_no'] ?? null,
                    'caa_certificate_no' => $member['caa_cert_no'] ?? null,
                    'sir2026tribunal_status' => $member['sir_status'] ?? 'Not Applicable',
                    'sir2026case_details' => $member['sir_case_details'] ?? null,
                    'has_four_wheeler' => false,
                    'has_health_insurance' => ($member['health_insurance_type'] ?? 'No') !== 'No',
                    'health_insurance_type' => $member['health_insurance_type'] ?? 'No',
                    'health_insurance_sum_assured' => ($member['health_insurance_sum_assured'] ?? '') !== '' ? (float)$member['health_insurance_sum_assured'] : null,
                    'health_insurance_annual_premium' => ($member['health_insurance_premium'] ?? '') !== '' ? (float)$member['health_insurance_premium'] : null,
                    'pan_no' => $member['pan_no'] ?? null,
                    'govt_employment_type' => $member['employment_nature'] ?? null,
                    'literacy_status' => $member['literate_status'] ?? null,
                    'highest_educational_qualifications' => $member['highest_qualification'] ?? null,
                    'is_child' => $isChild,
                    'relation_with_head_of_family' => $member['relation'] ?? null,
                    'applying_for_annapurna_bhandar' => ($member['applying_for_ay'] ?? 'No') === 'Yes',
                    'school_grade' => $schoolGrade,
                    'school_name' => $schoolName,
                    'school_type' => $schoolType,
                    'school_type_other' => $schoolTypeOther,
                    'vaccination_card_id' => $vaccCardId,
                    'vaccination_status' => $vaccStatus,
                    'vaccination_skip_reason_or_date' => $vaccSkip,
                    'lgd_district_code' => ($this->formData['district_id'] ?? '') !== '' ? (int)$this->formData['district_id'] : 0,
                    'lgd_block_mc_code' => ($this->formData['blockurban'] ?? '') !== '' ? (int)$this->formData['blockurban'] : 0,
                    'lgd_gp_ward_code' => ($this->formData['gpward'] ?? '') !== '' ? (int)$this->formData['gpward'] : 0,
                    'pays_income_or_professional_tax' => false,
                    'holds_constitutional_post' => false,
                    'is_registered_gst' => false,
                    'is_govt_pensioner' => false,
                ];

                $dbId = $member['db_id'] ?? null;
                if ($dbId) {
                    $memberData = [];
                    $activeFields = $memberSectionFields[$activeSection] ?? [];
                    foreach ($activeFields as $field) {
                        if (array_key_exists($field, $populatedMemberValues)) {
                            $memberData[$field] = $populatedMemberValues[$field];
                        }
                    }
                    $memberData['updated_at'] = now();

                    if (!empty($memberData)) {
                        DB::connection('pgsql_apy_uat')
                            ->table('family_members')
                            ->where('id', $dbId)
                            ->update($memberData);
                    }
                } else {
                    $memberData = array_merge($defaultMemberData, $populatedMemberValues);
                    $memberData['family_id'] = $familyId;
                    $memberData['created_at'] = now();
                    $memberData['updated_at'] = now();

                    $newId = DB::connection('pgsql_apy_uat')
                        ->table('family_members')
                        ->insertGetId($memberData);
                    $this->members[$index]['db_id'] = $newId;
                }
            }

            DB::connection('pgsql_apy_uat')->commit();
        } catch (\Exception $e) {
            DB::connection('pgsql_apy_uat')->rollBack();
            Log::error('Error saving Annapurna Yojana application incrementally: ' . $e->getMessage());
            $this->errorMessage = 'An error occurred while saving the application. Please try again.';
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
                    'formData.hof_aadhaar' => 'required|digits:12',
                    'formData.hof_bank_name' => 'required',
                    'formData.hof_acc_no' => 'required',
                    'formData.hof_ifsc' => 'required|size:11',
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
                    'formData.hof_bank_name.required' => 'HOF Bank Name is required.',
                    'formData.hof_acc_no.required' => 'HOF Account Number is required.',
                    'formData.hof_ifsc.required' => 'HOF IFSC is required.',
                    'formData.hof_ifsc.size' => 'HOF IFSC must be 11 characters.',
                ];
            } else {
                $index = $this->activeMemberIndex - 1;
                $rules = [
                    "members.{$index}.name" => 'required|string|max:255',
                    "members.{$index}.dob" => 'required|date',
                    "members.{$index}.gender" => 'required',
                    "members.{$index}.relation" => 'required',
                    "members.{$index}.aadhaar" => 'nullable|digits:12',
                ];
                $messages = [
                    "members.{$index}.name.required" => 'Member name is required.',
                    "members.{$index}.dob.required" => 'Member DOB is required.',
                    "members.{$index}.gender.required" => 'Member Gender is required.',
                    "members.{$index}.relation.required" => 'Member Relation is required.',
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
        } elseif ($section === 'identity') {
            if ($this->activeMemberIndex === 0) {
                $rules = [
                    'formData.has_digital_ration_card' => 'required',
                    'formData.is_lifting_ration' => 'required',
                ];
                $messages = [
                    'formData.has_digital_ration_card.required' => 'Ration card selection is required.',
                    'formData.is_lifting_ration.required' => 'Ration lifting status selection is required.',
                ];
            } else {
                $rules = [];
                $messages = [];
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

    public function save()
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

        $this->validate($rules, $messages);

        $this->formData['application_status'] = 'Submitted';
        $this->saveIncremental();

        if (empty($this->errorMessage)) {
            $this->successMessage = "Application submitted successfully! HOF Application ID: " . $this->applicationId;
            $this->resetForm();
        }
    }

    public function render()
    {
        return view('livewire.annapurna-yojana-form');
    }
}
