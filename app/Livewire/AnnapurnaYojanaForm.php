<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class AnnapurnaYojanaForm extends Component
{
    public $schemeId;

    public $schemeName;

    public $grievanceId;

    public $familyId = null;

    public $appId = null;

    // Form Navigation & Tabs
    public $activeMemberIndex = 0; // 0 = HOF, 1+ = Members

    public $activeSection = 'basic'; // basic, identity, bank, income, declaration

    // Form Data array
    public array $formData = [];

    // Master lists for location dropdowns
    public $districts = [];

    public $blocks = [];

    public $gps = [];

    // Loaded from masterData.json
    public $genders = [];

    public $categories = [];

    public $rcTypes = [];

    public $liftingStatuses = [];

    public $landOwnershipTypes = [];

    public $electricityProviders = [];

    public $employmentNatures = [];

    public $documentTypes = [];

    public $schoolTypes = [];

    public $benefitSchemes = [];

    // Dynamic list for family members (max 5)
    public array $members = [];

    // Status / Messages
    public $successMessage = null;

    public $errorMessage = null;

    public bool $showSubmitModal = false;

    // Dirty tracking — true when any field has changed since last save
    public bool $isDirty = false;

    public function mount($schemeId = 21, $schemeName = 'Annapurna Yojana', $grievanceId = null)
    {
        $this->schemeId = $schemeId;
        $this->schemeName = $schemeName;
        $this->grievanceId = $grievanceId;
        $this->activeMemberIndex = 0;
        $this->activeSection = 'family_identity';

        // Clear session draft data to start fresh and avoid data retaining bugs
        session()->forget(['annapurna_form_data', 'annapurna_members', 'annapurna_family_id', 'annapurna_app_id']);

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
            'vehicles' => [], // [{reg_no: '', model: ''}, ...] — one entry per vehicle,

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

        // Load masterData.json
        $masterDataPath = public_path('js/masterData.json');
        if (file_exists($masterDataPath)) {
            $masterData = json_decode(file_get_contents($masterDataPath), true);
            $this->genders = $masterData['genders'] ?? [];
            $this->categories = $masterData['categories'] ?? [];
            $this->rcTypes = $masterData['rcTypes'] ?? [];
            $this->liftingStatuses = $masterData['liftingStatuses'] ?? [];
            $this->landOwnershipTypes = $masterData['landOwnershipTypes'] ?? [];
            $this->electricityProviders = $masterData['electricityProviders'] ?? [];
            $this->employmentNatures = $masterData['employmentNatures'] ?? [];
            $this->documentTypes = $masterData['documentTypes'] ?? [];
            $this->schoolTypes = $masterData['schoolTypes'] ?? [];
            $this->benefitSchemes = $masterData['benefitSchemes'] ?? [];
        }

        // Load all districts from master-data file
        $rawDistricts = $this->getMasterDataArray('districts.js', 'districts');
        $districts = [];
        foreach ($rawDistricts as $d) {
            $obj = new \stdClass;
            $obj->id = $d['id'];
            $obj->name = strtoupper($d['text']);
            $districts[] = $obj;
        }
        usort($districts, function ($a, $b) {
            return strcmp($a->name, $b->name);
        });
        $this->districts = $districts;

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
        $this->isDirty = true;
    }

    public function updatedFormDataRuralUrban($value)
    {
        $this->formData['blockurban'] = '';
        $this->formData['gpward'] = '';
        $this->blocks = [];
        $this->gps = [];
        $this->loadBlocks();
        $this->isDirty = true;
    }

    public function updatedFormDataBlockurban($value)
    {
        $this->formData['gpward'] = '';
        $this->gps = [];
        $this->loadGps();
        $this->isDirty = true;
    }

    public function updatedFormDataGpward($value)
    {
        if (! empty($value)) {
            $this->isDirty = true;
        }
    }

    /**
     * When num_vehicles changes, resize the vehicles array to match.
     * Existing entries are preserved; new ones are added as empty rows.
     */
    public function updatedFormDataNumVehicles($value)
    {
        $count = max(0, (int) $value);
        $current = $this->formData['vehicles'] ?? [];

        if ($count > count($current)) {
            // Add empty rows
            for ($i = count($current); $i < $count; $i++) {
                $current[] = ['reg_no' => '', 'model' => ''];
            }
        } elseif ($count < count($current)) {
            // Trim excess rows
            $current = array_slice($current, 0, $count);
        }

        $this->formData['vehicles'] = $current;
        $this->isDirty = true;
    }

    /**
     * Livewire hook: fires whenever ANY formData field changes.
     * Marks the form as dirty and implements conditional cleanup.
     */
    public function updatedFormData($value, $field)
    {
        $skipFields = ['district_id', 'rural_urban', 'blockurban', 'gpward'];
        if (! in_array($field, $skipFields)) {
            $this->isDirty = true;
        }

        if ($field === 'hof_ifsc') {
            $ifsc = strtoupper(trim($value));
            if (strlen($ifsc) === 11) {
                $path = public_path('js/bank-ifsc-master.json');
                if (file_exists($path)) {
                    $json = file_get_contents($path);
                    $banks = json_decode($json, true);
                    if (is_array($banks)) {
                        foreach ($banks as $bank) {
                            if (strtoupper($bank['ifsc'] ?? '') === $ifsc) {
                                $this->formData['hof_bank_name'] = $bank['bankName'] ?? '';
                                $this->isDirty = true;
                                break;
                            }
                        }
                    }
                }
            }
        }

        // Cleanup conditional fields
        if ($field === 'owns_4_wheeler' && $value === 'No') {
            $this->formData['num_vehicles'] = '';
            $this->formData['vehicles'] = [];
        } elseif ($field === 'has_constitutional_post' && $value === 'No') {
            $this->formData['constitutional_post_details'] = '';
        } elseif ($field === 'has_gst_reg' && $value === 'No') {
            $this->formData['gstin'] = '';
        } elseif ($field === 'has_pensioner' && $value === 'No') {
            $this->formData['pensioner_details'] = '';
        } elseif ($field === 'has_digital_ration_card' && $value === 'No') {
            $this->formData['ration_card_type'] = '';
            $this->formData['hof_ration_card_id'] = '';
        } elseif ($field === 'hof_caa_status' && $value === 'Not Applicable') {
            $this->formData['hof_caa_app_no'] = '';
            $this->formData['hof_caa_cert_no'] = '';
        } elseif ($field === 'hof_kcc_type' && (empty($value) || $value === 'None')) {
            $this->formData['hof_kcc_id_no'] = '';
            $this->formData['hof_kcc_date'] = '';
            $this->formData['hof_kcc_issuing_authority'] = '';
        } elseif ($field === 'hof_sir_status' && $value !== 'Yes') {
            $this->formData['hof_sir_case_details'] = '';
        }
    }

    /**
     * Livewire hook: fires whenever any member field changes.
     */
    public function updatedMembers($value, $field)
    {
        $this->isDirty = true;

        // $field format is like: "0.applying_for_ay", "1.bank_name", etc.
        $parts = explode('.', $field);
        if (count($parts) === 2) {
            $index = (int) $parts[0];
            $subField = $parts[1];

            if (isset($this->members[$index])) {
                if ($subField === 'ifsc') {
                    $ifsc = strtoupper(trim($value));
                    if (strlen($ifsc) === 11) {
                        $path = public_path('js/bank-ifsc-master.json');
                        if (file_exists($path)) {
                            $json = file_get_contents($path);
                            $banks = json_decode($json, true);
                            if (is_array($banks)) {
                                foreach ($banks as $bank) {
                                    if (strtoupper($bank['ifsc'] ?? '') === $ifsc) {
                                        $this->members[$index]['bank_name'] = $bank['bankName'] ?? '';
                                        break;
                                    }
                                }
                            }
                        }
                    }
                } elseif ($subField === 'applying_for_ay' && $value === 'No') {
                    $this->members[$index]['bank_name'] = '';
                    $this->members[$index]['acc_no'] = '';
                    $this->members[$index]['ifsc'] = '';
                } elseif ($subField === 'has_digital_ration_card' && $value === 'No') {
                    $this->members[$index]['ration_card_no'] = '';
                    $this->members[$index]['ration_card_type'] = '';
                } elseif ($subField === 'caa_status' && $value === 'Not Applicable') {
                    $this->members[$index]['caa_app_no'] = '';
                    $this->members[$index]['caa_cert_no'] = '';
                } elseif ($subField === 'kcc_type' && (empty($value) || $value === 'None')) {
                    $this->members[$index]['kcc_id_no'] = '';
                    $this->members[$index]['kcc_date'] = '';
                    $this->members[$index]['kcc_issuing_authority'] = '';
                } elseif ($subField === 'sir_status' && $value !== 'Yes') {
                    $this->members[$index]['sir_case_details'] = '';
                }
            }
        }
    }

    /**
     * Save draft immediately only when dirty (called after any field change).
     * Uses a debounce-like guard: skips if nothing changed.
     */
    public function saveDraftIfDirty()
    {
        if ($this->isDirty) {
            $this->saveDraft();
        }
    }

    public function loadBlocks()
    {
        $districtId = $this->formData['district_id'] ?? null;
        $ruralUrban = $this->formData['rural_urban'] ?? null;

        if (! $districtId || ! $ruralUrban) {
            return;
        }

        $blocks = [];
        if ($ruralUrban == 2) {
            $rawBlocks = $this->getMasterDataArray('blocks.js', 'blocks');
            foreach ($rawBlocks as $b) {
                if (isset($b['district_code']) && (string) $b['district_code'] === (string) $districtId) {
                    $obj = new \stdClass;
                    $obj->id = $b['id'];
                    $obj->name = strtoupper($b['text']);
                    $blocks[] = $obj;
                }
            }
        } else {
            $rawUlbs = $this->getMasterDataArray('ulbs.js', 'ulbs');
            foreach ($rawUlbs as $u) {
                if (isset($u['district_code']) && (string) $u['district_code'] === (string) $districtId) {
                    $obj = new \stdClass;
                    $obj->id = $u['id'];
                    $obj->name = strtoupper($u['text']);
                    $blocks[] = $obj;
                }
            }
        }

        usort($blocks, function ($a, $b) {
            return strcmp($a->name, $b->name);
        });

        $this->blocks = $blocks;
    }

    public function loadGps()
    {
        $blockUrbanId = $this->formData['blockurban'] ?? null;
        $ruralUrban = $this->formData['rural_urban'] ?? null;

        if (! $blockUrbanId || ! $ruralUrban) {
            return;
        }

        $gps = [];
        if ($ruralUrban == 2) {
            $rawGps = $this->getMasterDataArray('gps.js', 'gps');
            foreach ($rawGps as $g) {
                if (isset($g['block_code']) && (string) $g['block_code'] === (string) $blockUrbanId) {
                    $obj = new \stdClass;
                    $obj->id = $g['id'];
                    $obj->name = strtoupper($g['text']);
                    $gps[] = $obj;
                }
            }
        } else {
            $rawWards = $this->getMasterDataArray('ulb_wards.js', 'ulb_wards');
            foreach ($rawWards as $w) {
                if (isset($w['urban_body_code']) && (string) $w['urban_body_code'] === (string) $blockUrbanId) {
                    $obj = new \stdClass;
                    $obj->id = $w['id'];
                    $obj->name = strtoupper($w['text']);
                    $gps[] = $obj;
                }
            }
        }

        usort($gps, function ($a, $b) {
            return strcmp($a->name, $b->name);
        });

        $this->gps = $gps;
    }

    public function getEmptyMemberStructure()
    {
        return [
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

            'member_type' => 'adult', // adult or child
            'school_grade' => '',
            'school_name' => '',
            'school_type' => '',
            'school_type_other' => '',
            'vaccination_card_id' => '',
            'vaccination_status' => '',
            'vaccination_skip_reason_or_date' => '',

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
    }

    public function updatedFormDataNumFamilyMembers($value)
    {
        $value = (int) $value;
        if ($value < 1) {
            $value = 1;
            $this->formData['num_family_members'] = 1;
        }

        $neededMembers = $value - 1;
        $currentCount = count($this->members);

        if ($neededMembers > $currentCount) {
            // Add empty members
            for ($i = $currentCount; $i < $neededMembers; $i++) {
                $this->members[] = $this->getEmptyMemberStructure();
            }
        } elseif ($neededMembers < $currentCount) {
            // Truncate members
            $this->members = array_slice($this->members, 0, $neededMembers);
        }

        if ($this->activeMemberIndex > count($this->members)) {
            $this->activeMemberIndex = count($this->members);
        }
    }

    public function addMember()
    {
        // Adding a member is always a structural change — force save
        $this->isDirty = true;
        $this->saveDraft();

        $this->members[] = $this->getEmptyMemberStructure();
        $this->formData['num_family_members'] = count($this->members) + 1;

        // Auto-switch to the new member tab on basic info section
        $this->activeMemberIndex = count($this->members);
        $this->activeSection = 'family_identity';
    }

    public function removeMember($index)
    {
        unset($this->members[$index]);
        $this->members = array_values($this->members);
        $this->formData['num_family_members'] = count($this->members) + 1;

        if ($this->activeMemberIndex > count($this->members)) {
            $this->activeMemberIndex = count($this->members);
        }

        // Removing a member is always a structural change — force save
        $this->isDirty = true;
        $this->saveDraft();
    }

    public function selectMember($index)
    {
        // Only save if something actually changed
        if ($this->isDirty) {
            $this->saveDraft();
        }
        $this->activeMemberIndex = $index;

        // Ensure the active section is valid for the newly selected member
        $validSections = array_keys($this->getSections());
        if (! in_array($this->activeSection, $validSections)) {
            $this->activeSection = 'family_identity';
        }
    }

    public function addChildSchool()
    {
        $this->formData['children_school'][] = [
            'name' => '',
            'grade' => '',
            'school_name' => '',
            'school_type' => '',
            'school_type_other' => '',
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
            'reason_skipped' => '',
        ];
    }

    public function removeChildVaccination($index)
    {
        unset($this->formData['children_vaccination'][$index]);
        $this->formData['children_vaccination'] = array_values($this->formData['children_vaccination']);
    }

    public function selectSection($section)
    {
        if ($section === 'declaration' && ! $this->areAllMembersFullyFilled()) {
            return;
        }
        // Only hit DB if something actually changed
        if ($this->isDirty) {
            $this->saveDraft();
        }
        $this->activeSection = $section;
    }

    public function getSections()
    {
        $sections = [
            'family_identity' => ['label' => 'A. Family Identity', 'bengali' => 'পারিবারিক পরিচিতি'],
            'ration_subsidy' => ['label' => 'B. Ration Card / Food Subsidy', 'bengali' => 'রেশন কার্ড / খাদ্য ভর্তুকি'],
            'assets' => ['label' => 'C. Assets', 'bengali' => 'সম্পত্তি'],
            'income_profession' => ['label' => 'D. Income/Profession', 'bengali' => 'আয় ও পেশা'],
            'other_docs' => ['label' => 'E. Other Identity Documents', 'bengali' => 'অন্যান্য পরিচয়পত্র'],
            'social_dependents' => ['label' => 'F. Social Status & Dependents', 'bengali' => 'সামাজিক মর্যাদা ও নির্ভরশীল সদস্য'],
            'gov_benefits' => ['label' => 'G. Gov Benefits', 'bengali' => 'সরকারি প্রকল্পের সুবিধা'],
            'declaration' => ['label' => 'H. Declaration & Consent', 'bengali' => 'ঘোষণা ও সম্মতি'],
        ];

        $isMember = ($this->activeMemberIndex > 0);

        if ($isMember) {
            $index = $this->activeMemberIndex - 1;
            $member = $this->members[$index] ?? null;
            $isChild = $member && ($member['member_type'] ?? 'adult') === 'child';

            if ($isChild) {
                // Child members only need A, F, H
                unset($sections['ration_subsidy']);
                unset($sections['assets']);
                unset($sections['income_profession']);
                unset($sections['other_docs']);
                unset($sections['gov_benefits']);
            } else {
                // Adult members need A, B, C, D, E, G, H (F is hidden)
                unset($sections['social_dependents']);
            }
        } else {
            // HOF needs A, B, C, D, E, G, H (F is hidden)
            unset($sections['social_dependents']);
        }

        return $sections;
    }

    public function nextSection()
    {
        $this->validateSection($this->activeSection);
        // Only persist to DB if something actually changed
        if ($this->isDirty) {
            $this->saveDraft();
        }

        $sections = array_keys($this->getSections());
        $currentIndex = array_search($this->activeSection, $sections);

        if ($currentIndex !== false && $currentIndex < count($sections) - 1) {
            $nextSec = $sections[$currentIndex + 1];
            if ($nextSec === 'declaration' && ! $this->areAllMembersFullyFilled()) {
                return;
            }
            $this->activeSection = $nextSec;
        }
    }

    public function isSectionFilled($memberIndex, $section)
    {
        if ($memberIndex === 0) {
            if ($section === 'family_identity') {
                $category = $this->formData['category'] ?? '';
                $certOk = true;
                if (in_array($category, ['SC', 'ST', 'OBC'])) {
                    $certOk = ! empty($this->formData['caste_certificate_no']);
                } elseif ($category === 'UR-EWS') {
                    $certOk = ! empty($this->formData['ews_certificate_no']);
                } elseif ($category === 'PVTG') {
                    $certOk = ! empty($this->formData['pvtg_certificate_no']);
                }

                return ! empty($this->formData['hof_name']) &&
                    ! empty($this->formData['hof_dob']) &&
                    ! empty($this->formData['hof_gender']) &&
                    ! empty($this->formData['contact_no']) &&
                    strlen($this->formData['contact_no']) === 10 &&
                    ! empty($category) &&
                    $certOk &&
                    ! empty($this->formData['district_id']) &&
                    ! empty($this->formData['rural_urban']) &&
                    ! empty($this->formData['blockurban']) &&
                    ! empty($this->formData['gpward']) &&
                    ! empty($this->formData['village_town']) &&
                    ! empty($this->formData['police_station']) &&
                    ! empty($this->formData['post_office']) &&
                    ! empty($this->formData['pincode']) &&
                    strlen($this->formData['pincode']) === 6 &&
                    ! empty($this->formData['hof_aadhaar']) &&
                    strlen($this->formData['hof_aadhaar']) === 12 &&
                    ! empty($this->formData['hof_bank_name']) &&
                    ! empty($this->formData['hof_acc_no']) &&
                    ! empty($this->formData['hof_ifsc']) &&
                    strlen($this->formData['hof_ifsc']) === 11;
            }

            if ($section === 'ration_subsidy') {
                return ! empty($this->formData['has_digital_ration_card']) &&
                    ! empty($this->formData['is_lifting_ration']);
            }

            if ($section === 'assets') {
                return ! empty($this->formData['has_pucca_rooms']) &&
                    ! empty($this->formData['owns_land']) &&
                    ! empty($this->formData['owns_4_wheeler']);
            }

            if ($section === 'income_profession') {
                return ! empty($this->formData['pays_tax']) &&
                    ! empty($this->formData['total_annual_income']) &&
                    is_numeric($this->formData['total_annual_income']);
            }

            if ($section === 'declaration') {
                return (bool) ($this->formData['agree_consent'] ?? false);
            }

            return true;
        } else {
            $index = $memberIndex - 1;
            if (! isset($this->members[$index])) {
                return false;
            }
            $member = $this->members[$index];

            if ($section === 'family_identity') {
                $basicFilled = ! empty($member['member_type']) &&
                    ! empty($member['name']) &&
                    ! empty($member['dob']) &&
                    ! empty($member['gender']) &&
                    ! empty($member['relation']);

                if (! $basicFilled) {
                    return false;
                }

                if (($member['member_type'] ?? 'adult') === 'child') {
                    return true;
                }

                $aadhaar = $member['aadhaar'] ?? '';
                $aadhaarOk = empty($aadhaar) || strlen($aadhaar) === 12;

                $bankOk = true;
                if ($this->isMemberFemale25to60($index) || (($member['applying_for_ay'] ?? 'No') === 'Yes')) {
                    $bankOk = ! empty($member['bank_name']) &&
                        ! empty($member['acc_no']) &&
                        ! empty($member['ifsc']) &&
                        strlen($member['ifsc']) === 11;
                }

                return $aadhaarOk && $bankOk;
            }

            if ($section === 'declaration') {
                return (bool) ($this->formData['agree_consent'] ?? false);
            }

            return true;
        }

        return false;
    }

    public function isMemberFullyFilled($memberIndex)
    {
        if ($memberIndex === 0) {
            return $this->isSectionFilled(0, 'family_identity') &&
                $this->isSectionFilled(0, 'ration_subsidy') &&
                $this->isSectionFilled(0, 'assets') &&
                $this->isSectionFilled(0, 'income_profession') &&
                $this->isSectionFilled(0, 'other_docs') &&
                $this->isSectionFilled(0, 'gov_benefits');
        } else {
            $index = $memberIndex - 1;
            $member = $this->members[$index] ?? null;
            if (! $member) {
                return false;
            }
            if (($member['member_type'] ?? 'adult') === 'child') {
                return $this->isSectionFilled($memberIndex, 'family_identity') &&
                    $this->isSectionFilled($memberIndex, 'social_dependents');
            } else {
                return $this->isSectionFilled($memberIndex, 'family_identity') &&
                    $this->isSectionFilled($memberIndex, 'ration_subsidy') &&
                    $this->isSectionFilled($memberIndex, 'assets') &&
                    $this->isSectionFilled($memberIndex, 'income_profession') &&
                    $this->isSectionFilled($memberIndex, 'other_docs') &&
                    $this->isSectionFilled($memberIndex, 'gov_benefits');
            }
        }
    }

    public function areAllMembersFullyFilled()
    {
        if (! $this->isMemberFullyFilled(0)) {
            return false;
        }

        foreach ($this->members as $index => $member) {
            if (! $this->isMemberFullyFilled($index + 1)) {
                return false;
            }
        }

        return true;
    }

    public function previousSection()
    {
        // Only persist to DB if something actually changed
        if ($this->isDirty) {
            $this->saveDraft();
        }

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

        if ($section === 'family_identity') {
            if ($this->activeMemberIndex === 0) {
                $rules = [
                    'formData.hof_name' => 'required|string|max:255|regex:/^[\p{L}\s.\'\-]+$/u',
                    'formData.hof_dob' => 'required|date|before:today',
                    'formData.hof_gender' => 'required|in:Male,Female,Other',
                    'formData.contact_no' => 'required|digits:10',
                    'formData.category' => 'required',
                    'formData.district_id' => 'required',
                    'formData.rural_urban' => 'required',
                    'formData.blockurban' => 'required',
                    'formData.gpward' => 'required',
                    'formData.village_town' => 'required|string|max:200',
                    'formData.police_station' => 'required|string|max:200',
                    'formData.post_office' => 'required|string|max:200',
                    'formData.pincode' => 'required|digits:6',
                    'formData.hof_aadhaar' => [
                        'required',
                        'digits:12',
                        function ($attribute, $value, $fail) {
                            if (! $this->validateVerhoeff($value)) {
                                $fail('The HOF Aadhaar number is invalid (checksum validation failed).');
                            }
                        },
                    ],
                    'formData.hof_bank_name' => ['required', 'string', 'max:100', 'regex:/^[\p{L}\s.\'\-]+$/u'],
                    'formData.hof_acc_no' => 'required|digits_between:9,18',
                    'formData.hof_ifsc' => ['required', 'size:11', 'regex:/^[A-Z]{4}0[A-Z0-9]{6}$/'],
                    'formData.hof_epic_no' => ['nullable', 'regex:/^[A-Z]{3}[0-9]{7}$/'],
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
                    'formData.hof_name.regex' => 'Name should contain letters only (no numbers/special characters).',
                    'formData.hof_dob.required' => 'HOF Date of Birth is required.',
                    'formData.hof_dob.before' => 'Date of Birth must be in the past.',
                    'formData.hof_gender.required' => 'HOF Gender is required.',
                    'formData.contact_no.required' => 'Contact number is required.',
                    'formData.contact_no.digits' => 'Contact must be exactly 10 digits (numbers only).',
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
                    'formData.pincode.digits' => 'Pincode must be exactly 6 digits (numbers only).',
                    'formData.hof_aadhaar.required' => 'HOF Aadhaar number is required.',
                    'formData.hof_aadhaar.digits' => 'Aadhaar must be exactly 12 digits (numbers only).',
                    'formData.hof_bank_name.required' => 'HOF Bank Name is required.',
                    'formData.hof_bank_name.regex' => 'Bank Name should contain letters only (no numbers).',
                    'formData.hof_acc_no.required' => 'HOF Account Number is required.',
                    'formData.hof_acc_no.digits_between' => 'Account Number must be 9 to 18 digits (numbers only).',
                    'formData.hof_ifsc.required' => 'HOF IFSC Code is required.',
                    'formData.hof_ifsc.size' => 'IFSC Code must be exactly 11 characters.',
                    'formData.hof_ifsc.regex' => 'IFSC format is invalid (e.g. SBIN0001234).',
                    'formData.hof_epic_no.regex' => 'Voter ID (EPIC) format is invalid (e.g. ABC1234567).',
                ];
            } else {
                $index = $this->activeMemberIndex - 1;
                $member = $this->members[$index];
                $rules = [
                    "members.{$index}.member_type" => 'required|in:adult,child',
                    "members.{$index}.name" => ['required', 'string', 'max:255', 'regex:/^[\p{L}\s.\'\-]+$/u'],
                    "members.{$index}.dob" => 'required|date|before:today',
                    "members.{$index}.gender" => 'required',
                    "members.{$index}.relation" => 'required',
                ];
                $messages = [
                    "members.{$index}.member_type.required" => 'Member category (Adult/Child) is required.',
                    "members.{$index}.name.required" => 'Member name is required.',
                    "members.{$index}.name.regex" => 'Member name should contain letters only.',
                    "members.{$index}.dob.required" => 'Member DOB is required.',
                    "members.{$index}.dob.before" => 'Member Date of Birth must be in the past.',
                    "members.{$index}.gender.required" => 'Member Gender is required.',
                    "members.{$index}.relation.required" => 'Member Relation is required.',
                ];

                if (($member['member_type'] ?? 'adult') === 'adult') {
                    $rules["members.{$index}.aadhaar"] = [
                        'nullable',
                        'digits:12',
                        function ($attribute, $value, $fail) {
                            if (! empty($value) && ! $this->validateVerhoeff($value)) {
                                $fail('The Member Aadhaar number is invalid (checksum failed).');
                            }
                        },
                    ];
                    $rules["members.{$index}.epic_no"] = ['nullable', 'regex:/^[A-Z]{3}[0-9]{7}$/'];
                    $messages["members.{$index}.aadhaar.digits"] = 'Member Aadhaar must be 12 digits (numbers only).';
                    $messages["members.{$index}.epic_no.regex"] = 'Member Voter ID (EPIC) format is invalid (e.g. ABC1234567).';

                    if ($this->isMemberFemale25to60($index) || (($member['applying_for_ay'] ?? 'No') === 'Yes')) {
                        $rules["members.{$index}.bank_name"] = ['required', 'string', 'max:100', 'regex:/^[\p{L}\s.\'\-]+$/u'];
                        $rules["members.{$index}.acc_no"] = 'required|digits_between:9,18';
                        $rules["members.{$index}.ifsc"] = ['required', 'size:11', 'regex:/^[A-Z]{4}0[A-Z0-9]{6}$/'];

                        $messages["members.{$index}.bank_name.required"] = 'Member bank name is required since they are applying for AY.';
                        $messages["members.{$index}.bank_name.regex"] = 'Bank Name should contain letters only (no numbers).';
                        $messages["members.{$index}.acc_no.required"] = 'Member bank account number is required since they are applying for AY.';
                        $messages["members.{$index}.acc_no.digits_between"] = 'Account Number must be 9 to 18 digits (numbers only).';
                        $messages["members.{$index}.ifsc.required"] = 'Member IFSC is required since they are applying for AY.';
                        $messages["members.{$index}.ifsc.size"] = 'Member IFSC must be exactly 11 characters.';
                        $messages["members.{$index}.ifsc.regex"] = 'Member IFSC format is invalid (e.g. SBIN0001234).';
                    }
                }
            }
        } elseif ($section === 'ration_subsidy') {
            if ($this->activeMemberIndex === 0) {
                $rules = [
                    'formData.has_digital_ration_card' => 'required',
                    'formData.is_lifting_ration' => 'required',
                ];
                $messages = [
                    'formData.has_digital_ration_card.required' => 'Ration card selection is required.',
                    'formData.is_lifting_ration.required' => 'Ration lifting status selection is required.',
                ];
            }
        } elseif ($section === 'assets') {
            if ($this->activeMemberIndex === 0) {
                $rules = [
                    'formData.has_pucca_rooms' => 'required',
                    'formData.owns_land' => 'required',
                    'formData.owns_4_wheeler' => 'required',
                ];
                $messages = [
                    'formData.has_pucca_rooms.required' => 'House size selection is required.',
                    'formData.owns_land.required' => 'Land ownership selection is required.',
                    'formData.owns_4_wheeler.required' => '4-wheeler ownership selection is required.',
                ];

                if (($this->formData['owns_4_wheeler'] ?? '') === 'Yes') {
                    $rules['formData.num_vehicles'] = 'required|integer|min:1';
                    $messages['formData.num_vehicles.required'] = 'Please enter number of vehicles.';
                    $messages['formData.num_vehicles.min'] = 'Number of vehicles must be at least 1.';

                    foreach ($this->formData['vehicles'] ?? [] as $vi => $vehicle) {
                        $rules["formData.vehicles.{$vi}.reg_no"] = ['required', 'string', 'regex:/^[A-Z]{2}[ -]?[0-9]{2}[ -]?[A-Z]{1,3}[ -]?[0-9]{4}$/i'];
                        $rules["formData.vehicles.{$vi}.model"] = 'required|string|max:100';

                        $messages["formData.vehicles.{$vi}.reg_no.required"] = 'Registration number for Vehicle ' . ($vi + 1) . ' is required.';
                        $messages["formData.vehicles.{$vi}.reg_no.regex"] = 'Registration format for Vehicle ' . ($vi + 1) . ' is invalid (e.g. WB-01-AB-1234).';
                        $messages["formData.vehicles.{$vi}.model.required"] = 'Model name for Vehicle ' . ($vi + 1) . ' is required.';
                    }
                }
            }
        } elseif ($section === 'income_profession') {
            if ($this->activeMemberIndex === 0) {
                $rules = [
                    'formData.pays_tax' => 'required',
                    'formData.total_annual_income' => 'required|numeric|min:0',
                    'formData.hof_pan_no' => ['nullable', 'regex:/^[A-Z]{3}[CPHFATBLJG][A-Z][0-9]{4}[A-Z]$/'],
                ];
                $messages = [
                    'formData.pays_tax.required' => 'Income Tax payment selection is required.',
                    'formData.total_annual_income.required' => 'Annual Income is required.',
                    'formData.total_annual_income.numeric' => 'Annual Income must be a number.',
                    'formData.hof_pan_no.regex' => 'HOF PAN format is invalid (e.g. ABCDE1234F).',
                ];
            } else {
                $index = $this->activeMemberIndex - 1;
                $member = $this->members[$index];
                if (($member['member_type'] ?? 'adult') === 'adult') {
                    $rules = [
                        "members.{$index}.pan_no" => ['nullable', 'regex:/^[A-Z]{3}[CPHFATBLJG][A-Z][0-9]{4}[A-Z]$/'],
                    ];
                    $messages = [
                        "members.{$index}.pan_no.regex" => 'Member PAN format is invalid (e.g. ABCDE1234F).',
                    ];
                }
            }
        }

        if (! empty($rules)) {
            $this->validate($rules, $messages);
        }
    }

    public function resetForm()
    {
        $this->activeMemberIndex = 0;
        $this->activeSection = 'family_identity';
        $this->familyId = null;
        $this->appId = null;
        session()->forget(['annapurna_form_data', 'annapurna_members', 'annapurna_family_id', 'annapurna_app_id']);
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
            'formData.hof_name' => ['required', 'string', 'max:255', 'regex:/^[\p{L}\s.\'\-]+$/u'],
            'formData.hof_dob' => 'required|date|before:today',
            'formData.hof_gender' => 'required|in:Male,Female,Other',
            'formData.contact_no' => 'required|digits:10',
            'formData.category' => 'required',
            'formData.district_id' => 'required',
            'formData.rural_urban' => 'required',
            'formData.blockurban' => 'required',
            'formData.gpward' => 'required',
            'formData.village_town' => 'required|string|max:200',
            'formData.police_station' => 'required|string|max:200',
            'formData.post_office' => 'required|string|max:200',
            'formData.pincode' => 'required|digits:6',

            'formData.hof_aadhaar' => [
                'required',
                'digits:12',
                function ($attribute, $value, $fail) {
                    if (! $this->validateVerhoeff($value)) {
                        $fail('The HOF Aadhaar number is invalid (checksum validation failed).');
                    }
                },
            ],
            'formData.has_digital_ration_card' => 'required',
            'formData.is_lifting_ration' => 'required',

            'formData.hof_bank_name' => ['required', 'string', 'max:100', 'regex:/^[\p{L}\s.\'\-]+$/u'],
            'formData.hof_acc_no' => 'required|digits_between:9,18',
            'formData.hof_ifsc' => ['required', 'size:11', 'regex:/^[A-Z]{4}0[A-Z0-9]{6}$/'],
            'formData.hof_pan_no' => ['nullable', 'regex:/^[A-Z]{3}[CPHFATBLJG][A-Z][0-9]{4}[A-Z]$/'],
            'formData.hof_epic_no' => ['nullable', 'regex:/^[A-Z]{3}[0-9]{7}$/'],

            'formData.has_pucca_rooms' => 'required',
            'formData.owns_land' => 'required',
            'formData.owns_4_wheeler' => 'required',

            'formData.pays_tax' => 'required',
            'formData.total_annual_income' => 'required|numeric|min:0',
        ];

        if (in_array($this->formData['category'], ['SC', 'ST', 'OBC'])) {
            $rules['formData.caste_certificate_no'] = 'required|string|max:100';
        } elseif ($this->formData['category'] == 'UR-EWS') {
            $rules['formData.ews_certificate_no'] = 'required|string|max:100';
        } elseif ($this->formData['category'] == 'PVTG') {
            $rules['formData.pvtg_certificate_no'] = 'required|string|max:100';
        }

        if (($this->formData['owns_4_wheeler'] ?? '') === 'Yes') {
            $rules['formData.num_vehicles'] = 'required|integer|min:1';
            foreach ($this->formData['vehicles'] ?? [] as $vi => $vehicle) {
                $rules["formData.vehicles.{$vi}.reg_no"] = ['required', 'string', 'regex:/^[A-Z]{2}[ -]?[0-9]{2}[ -]?[A-Z]{1,3}[ -]?[0-9]{4}$/i'];
                $rules["formData.vehicles.{$vi}.model"] = 'required|string|max:100';
            }
        }

        $messages = [
            'formData.hof_name.required' => 'Head of Family name is required.',
            'formData.hof_name.regex' => 'Name should contain letters only (no numbers or special characters).',
            'formData.hof_dob.required' => 'HOF Date of Birth is required.',
            'formData.hof_dob.before' => 'Date of Birth must be in the past.',
            'formData.hof_gender.required' => 'HOF Gender is required.',
            'formData.contact_no.required' => 'Contact number is required.',
            'formData.contact_no.digits' => 'Contact must be exactly 10 digits (numbers only).',
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
            'formData.pincode.digits' => 'Pincode must be exactly 6 digits (numbers only).',

            'formData.hof_aadhaar.required' => 'HOF Aadhaar number is required.',
            'formData.hof_aadhaar.digits' => 'Aadhaar must be exactly 12 digits (numbers only).',
            'formData.has_digital_ration_card.required' => 'Ration card selection is required.',
            'formData.is_lifting_ration.required' => 'Ration lifting status selection is required.',

            'formData.hof_bank_name.required' => 'HOF Bank Name is required.',
            'formData.hof_bank_name.regex' => 'Bank Name should contain letters only (no numbers).',
            'formData.hof_acc_no.required' => 'HOF Account Number is required.',
            'formData.hof_acc_no.digits_between' => 'Account Number must be 9 to 18 digits (numbers only).',
            'formData.hof_ifsc.required' => 'HOF IFSC Code is required.',
            'formData.hof_ifsc.size' => 'IFSC Code must be exactly 11 characters.',
            'formData.hof_ifsc.regex' => 'IFSC format is invalid (e.g. SBIN0001234).',
            'formData.hof_pan_no.regex' => 'PAN format is invalid (e.g. ABCDE1234F).',
            'formData.hof_epic_no.regex' => 'Voter ID (EPIC) format is invalid (e.g. ABC1234567).',

            'formData.has_pucca_rooms.required' => 'House size selection is required.',
            'formData.owns_land.required' => 'Land ownership selection is required.',
            'formData.owns_4_wheeler.required' => '4-wheeler ownership selection is required.',
            'formData.num_vehicles.required' => 'Please enter number of vehicles.',
            'formData.num_vehicles.min' => 'Number of vehicles must be at least 1.',

            'formData.pays_tax.required' => 'Income Tax payment selection is required.',
            'formData.total_annual_income.required' => 'Annual Income is required.',
            'formData.total_annual_income.numeric' => 'Annual Income must be a number.',
        ];

        // Validate each member
        foreach ($this->members as $index => $member) {
            $rules["members.{$index}.member_type"] = 'required|in:adult,child';
            $rules["members.{$index}.name"] = ['required', 'string', 'max:255', 'regex:/^[\p{L}\s.\'\-]+$/u'];
            $rules["members.{$index}.dob"] = 'required|date|before:today';
            $rules["members.{$index}.gender"] = 'required';
            $rules["members.{$index}.relation"] = 'required';

            $messages["members.{$index}.member_type.required"] = 'Member #' . ($index + 1) . ' category is required.';
            $messages["members.{$index}.name.required"] = 'Member #' . ($index + 1) . ' name is required.';
            $messages["members.{$index}.name.regex"] = 'Member #' . ($index + 1) . ' name should contain letters only.';
            $messages["members.{$index}.dob.required"] = 'Member #' . ($index + 1) . ' DOB is required.';
            $messages["members.{$index}.dob.before"] = 'Member #' . ($index + 1) . ' Date of Birth must be in the past.';
            $messages["members.{$index}.gender.required"] = 'Member #' . ($index + 1) . ' Gender is required.';
            $messages["members.{$index}.relation.required"] = 'Member #' . ($index + 1) . ' Relation is required.';

            if (($member['member_type'] ?? 'adult') === 'adult') {
                $rules["members.{$index}.aadhaar"] = [
                    'nullable',
                    'digits:12',
                    function ($attribute, $value, $fail) {
                        if (! empty($value) && ! $this->validateVerhoeff($value)) {
                            $fail('The Member Aadhaar number is invalid (checksum failed).');
                        }
                    },
                ];
                $rules["members.{$index}.epic_no"] = ['nullable', 'regex:/^[A-Z]{3}[0-9]{7}$/'];
                $rules["members.{$index}.pan_no"] = ['nullable', 'regex:/^[A-Z]{3}[CPHFATBLJG][A-Z][0-9]{4}[A-Z]$/'];

                $messages["members.{$index}.aadhaar.digits"] = 'Member #' . ($index + 1) . ' Aadhaar must be 12 digits (numbers only).';
                $messages["members.{$index}.epic_no.regex"] = 'Member #' . ($index + 1) . ' Voter ID (EPIC) format is invalid (e.g. ABC1234567).';
                $messages["members.{$index}.pan_no.regex"] = 'Member #' . ($index + 1) . ' PAN format is invalid (e.g. ABCDE1234F).';

                if ($this->isMemberFemale25to60($index) || (($member['applying_for_ay'] ?? 'No') === 'Yes')) {
                    $rules["members.{$index}.bank_name"] = ['required', 'string', 'max:100', 'regex:/^[\p{L}\s.\'\-]+$/u'];
                    $rules["members.{$index}.acc_no"] = 'required|digits_between:9,18';
                    $rules["members.{$index}.ifsc"] = ['required', 'size:11', 'regex:/^[A-Z]{4}0[A-Z0-9]{6}$/'];

                    $messages["members.{$index}.bank_name.required"] = 'Member #' . ($index + 1) . ' bank name is required since they are applying for AY.';
                    $messages["members.{$index}.bank_name.regex"] = 'Bank Name should contain letters only (no numbers).';
                    $messages["members.{$index}.acc_no.required"] = 'Member #' . ($index + 1) . ' account number is required since they are applying for AY.';
                    $messages["members.{$index}.acc_no.digits_between"] = 'Account Number must be 9 to 18 digits (numbers only).';
                    $messages["members.{$index}.ifsc.required"] = 'Member #' . ($index + 1) . ' IFSC is required since they are applying for AY.';
                    $messages["members.{$index}.ifsc.size"] = 'Member #' . ($index + 1) . ' IFSC must be exactly 11 characters.';
                    $messages["members.{$index}.ifsc.regex"] = 'Member #' . ($index + 1) . ' IFSC format is invalid (e.g. SBIN0001234).';
                }
            }
        }

        try {
            $this->validate($rules, $messages);
            $this->showSubmitModal = true;
        } catch (ValidationException $e) {
            // Smart UX redirection to tab containing error
            $firstErrorKey = array_key_first($e->validator->errors()->toArray());
            if ($firstErrorKey) {
                if (str_starts_with($firstErrorKey, 'members.')) {
                    $parts = explode('.', $firstErrorKey);
                    if (isset($parts[1])) {
                        $this->activeMemberIndex = ((int) $parts[1]) + 1;
                        $field = $parts[2] ?? '';
                        if (in_array($field, ['member_type', 'name', 'dob', 'gender', 'relation', 'aadhaar', 'bank_name', 'acc_no', 'ifsc', 'epic_no', 'ac_part_no', 'applying_for_ay'])) {
                            $this->activeSection = 'family_identity';
                        } elseif (in_array($field, ['has_digital_ration_card', 'ration_card_no', 'ration_card_type'])) {
                            $this->activeSection = 'ration_subsidy';
                        } elseif (in_array($field, ['health_insurance_type', 'health_insurance_premium', 'health_insurance_sum_assured'])) {
                            $this->activeSection = 'assets';
                        } elseif (in_array($field, ['pan_no', 'employment_nature', 'literate_status', 'highest_qualification'])) {
                            $this->activeSection = 'income_profession';
                        } elseif (in_array($field, ['caa_status', 'caa_app_no', 'caa_cert_no', 'kcc_type', 'kcc_id_no', 'kcc_date', 'kcc_issuing_authority', 'sir_status', 'sir_case_details'])) {
                            $this->activeSection = 'other_docs';
                        } elseif (in_array($field, ['school_grade', 'school_name', 'school_type', 'school_type_other', 'vaccination_card_id', 'vaccination_status', 'vaccination_skip_reason_or_date'])) {
                            $this->activeSection = 'social_dependents';
                        }
                    }
                } elseif (str_starts_with($firstErrorKey, 'formData.')) {
                    $this->activeMemberIndex = 0; // HOF
                    $field = str_replace('formData.', '', $firstErrorKey);

                    $familyIdentityFields = [
                        'hof_name',
                        'hof_dob',
                        'hof_gender',
                        'contact_no',
                        'category',
                        'caste_certificate_no',
                        'ews_certificate_no',
                        'pvtg_certificate_no',
                        'district_id',
                        'rural_urban',
                        'blockurban',
                        'gpward',
                        'village_town',
                        'police_station',
                        'post_office',
                        'pincode',
                        'hof_aadhaar',
                        'hof_bank_name',
                        'hof_acc_no',
                        'hof_ifsc',
                        'hof_epic_no',
                        'hof_ac_part_no',
                    ];

                    $rationSubsidyFields = [
                        'has_digital_ration_card',
                        'is_lifting_ration',
                        'hof_ration_card_id',
                        'ration_card_type',
                    ];

                    $assetsFields = [
                        'has_pucca_rooms',
                        'owns_land',
                        'land_size_decimals',
                        'owns_4_wheeler',
                        'num_vehicles',
                        'vehicles',
                        'health_insurance_type',
                        'health_insurance_premium',
                        'health_insurance_sum_assured',
                    ];

                    $incomeProfessionFields = [
                        'pays_tax',
                        'hof_pan_no',
                        'hof_employment_nature',
                        'total_annual_income',
                        'has_constitutional_post',
                        'constitutional_post_details',
                        'has_gst_reg',
                        'gstin',
                        'has_pensioner',
                        'pensioner_details',
                        'num_literate_adults',
                        'num_illiterate_adults',
                        'hof_literate_status',
                        'hof_highest_qualification',
                    ];

                    if (in_array($field, $familyIdentityFields)) {
                        $this->activeSection = 'family_identity';
                    } elseif (in_array($field, $rationSubsidyFields)) {
                        $this->activeSection = 'income_profession';
                    } elseif (in_array($field, ['caa_status', 'caa_app_no', 'caa_cert_no', 'kcc_type', 'kcc_id_no', 'kcc_date', 'kcc_issuing_authority', 'sir_status', 'sir_case_details'])) {
                        $this->activeSection = 'other_docs';
                    } elseif (in_array($field, ['school_grade', 'school_name', 'school_type', 'school_type_other', 'vaccination_card_id', 'vaccination_status', 'vaccination_skip_reason_or_date'])) {
                        $this->activeSection = 'social_dependents';
                    }
                }
            }
            throw $e;
        }
    }

    public function save()
    {
        $this->successMessage = null;
        $this->errorMessage = null;

        try {
            DB::connection('pgsql_annapurna')->beginTransaction();

            // 1. Get LGD codes for locations
            $districtId = $this->formData['district_id'] ?? null;
            $lgdDistrictCode = null;
            if ($districtId) {
                $districts = $this->getMasterDataArray('districts.js', 'districts');
                foreach ($districts as $d) {
                    if ((string) $d['id'] === (string) $districtId) {
                        $lgdDistrictCode = $d['id'];
                        break;
                    }
                }
            }

            $lgdBlockMcCode = null;
            $blockUrbanId = $this->formData['blockurban'] ?? null;
            if ($blockUrbanId) {
                if (($this->formData['rural_urban'] ?? null) == 2) {
                    $blocks = $this->getMasterDataArray('blocks.js', 'blocks');
                    foreach ($blocks as $b) {
                        if ((string) $b['id'] === (string) $blockUrbanId) {
                            $lgdBlockMcCode = $b['id'];
                            break;
                        }
                    }
                } else {
                    $ulbs = $this->getMasterDataArray('ulbs.js', 'ulbs');
                    foreach ($ulbs as $u) {
                        if ((string) $u['id'] === (string) $blockUrbanId) {
                            $lgdBlockMcCode = $u['id'];
                            break;
                        }
                    }
                }
            }

            $lgdGpWardCode = null;
            $gpWardId = $this->formData['gpward'] ?? null;
            if ($gpWardId) {
                if (($this->formData['rural_urban'] ?? null) == 2) {
                    $gps = $this->getMasterDataArray('gps.js', 'gps');
                    foreach ($gps as $g) {
                        if ((string) $g['id'] === (string) $gpWardId) {
                            $lgdGpWardCode = $g['id'];
                            break;
                        }
                    }
                } else {
                    $wards = $this->getMasterDataArray('ulb_wards.js', 'ulb_wards');
                    foreach ($wards as $w) {
                        if ((string) $w['id'] === (string) $gpWardId) {
                            $lgdGpWardCode = $w['id'];
                            break;
                        }
                    }
                }
            }

            $lgdDistrictCode = $lgdDistrictCode ? (int) $lgdDistrictCode : 0;
            $lgdBlockMcCode = $lgdBlockMcCode ? (int) $lgdBlockMcCode : 0;
            $lgdGpWardCode = $lgdGpWardCode ? (int) $lgdGpWardCode : 0;

            // 2. Construct address string
            $address = trim(($this->formData['house_no'] ? $this->formData['house_no'] . ', ' : '') . $this->formData['village_town'] . ', P.O. ' . $this->formData['post_office'] . ', P.S. ' . $this->formData['police_station'] . ', PIN ' . $this->formData['pincode']);

            // 3. Generate or reuse UUID for application_id
            if (! $this->appId) {
                $this->appId = (string) Str::uuid();
            }
            $appId = $this->appId;

            // 4. Pre-clean conditional fields
            $hasDigitalRationCard = (($this->formData['has_digital_ration_card'] ?? '') === 'Yes');
            $rationCardHouseholdId = $hasDigitalRationCard ? ($this->formData['hof_ration_card_id'] ?? null) : null;
            $rationCardType = $hasDigitalRationCard ? ($this->formData['ration_card_type'] ?? null) : null;
            $liftingMonthlyRation = $hasDigitalRationCard && (($this->formData['is_lifting_ration'] ?? '') === 'Yes');

            $hasConstitutionalPost = (($this->formData['has_constitutional_post'] ?? '') === 'Yes');
            $constitutionalPostDetails = $hasConstitutionalPost ? ($this->formData['constitutional_post_details'] ?? null) : null;

            $hasGstReg = (($this->formData['has_gst_reg'] ?? '') === 'Yes');
            $gstin = $hasGstReg ? ($this->formData['gstin'] ?? null) : null;

            $hasPensioner = (($this->formData['has_pensioner'] ?? '') === 'Yes');
            $pensionerDetails = $hasPensioner ? ($this->formData['pensioner_details'] ?? null) : null;

            $caaStatus = $this->formData['hof_caa_status'] ?? 'Not Applicable';
            $caaAppNo = $caaStatus === 'Applied' ? ($this->formData['hof_caa_app_no'] ?? null) : null;
            $caaCertNo = $caaStatus === 'Issued' ? ($this->formData['hof_caa_cert_no'] ?? null) : null;

            $sirStatus = $this->formData['hof_sir_status'] ?? 'Not Applicable';
            $sirCaseDetails = $sirStatus === 'Yes' ? ($this->formData['hof_sir_case_details'] ?? null) : null;

            $healthInsuranceType = $this->formData['health_insurance_type'] ?? 'None';
            $hasHealthInsurance = ($healthInsuranceType !== 'None');
            $healthInsurancePremium = ($hasHealthInsurance && !empty($this->formData['health_insurance_premium'])) ? (float) $this->formData['health_insurance_premium'] : null;
            $healthInsuranceSumAssured = ($hasHealthInsurance && !empty($this->formData['health_insurance_sum_assured'])) ? (float) $this->formData['health_insurance_sum_assured'] : null;

            $ownsLand = (($this->formData['owns_land'] ?? '') === 'Yes');
            $landSizeDecimals = ($ownsLand && !empty($this->formData['land_size_decimals'])) ? (float) $this->formData['land_size_decimals'] : null;

            $hasFourWheeler = (($this->formData['owns_4_wheeler'] ?? '') === 'Yes');
            $vehicleCount = $hasFourWheeler && ! empty($this->formData['num_vehicles']) ? (int) $this->formData['num_vehicles'] : null;
            $vehicleReg = $hasFourWheeler && ! empty($this->formData['vehicles']) ? json_encode(array_column($this->formData['vehicles'], 'reg_no')) : null;
            $vehicleModel = $hasFourWheeler && ! empty($this->formData['vehicles']) ? json_encode(array_column($this->formData['vehicles'], 'model')) : null;

            // 5. Update or Insert family details into dbt_apy.families
            $familyData = [
                'application_id' => $appId,
                'total_family_members' => (int) ($this->formData['num_family_members'] ?? 1),
                'lifting_monthly_ration' => $liftingMonthlyRation,
                'has_electricity_connection' => false,
                'is_agreed' => (bool) ($this->formData['agree_consent'] ?? false),
                'application_status' => 'SUBMITTED',
                'lgd_district_code' => $lgdDistrictCode,
                'lgd_block_mc_code' => $lgdBlockMcCode,
                'lgd_gp_ward_code' => $lgdGpWardCode,
                'address' => $address,
                'has_digital_ration_card' => $hasDigitalRationCard,
                'ration_card_household_id' => $rationCardHouseholdId,
                'no_of_illiterate_adults' => ! empty($this->formData['num_illiterate_adults']) ? (int) $this->formData['num_illiterate_adults'] : null,
                'no_of_literate_adults' => ! empty($this->formData['num_literate_adults']) ? (int) $this->formData['num_literate_adults'] : null,
                'total_annual_family_income' => ! empty($this->formData['total_annual_income']) ? (int) $this->formData['total_annual_income'] : null,
                'area_type' => $this->formData['rural_urban'] == 2 ? 'RURAL' : 'URBAN',
                'ulb' => $this->formData['rural_urban'] == 1 ? (int) $this->formData['blockurban'] : null,
                'updated_at' => now(),
            ];

            if ($this->familyId) {
                DB::connection('pgsql_annapurna')->table('dbt_apy.families')->where('id', $this->familyId)->update($familyData);
                $familyId = $this->familyId;
            } else {
                $familyData['created_at'] = now();
                $familyId = DB::connection('pgsql_annapurna')->table('dbt_apy.families')->insertGetId($familyData, 'id');
                $this->familyId = $familyId;
            }

            // Clear old entries to avoid duplicates on final submission
            $existingMemberIds = DB::connection('pgsql_annapurna')->table('dbt_apy.family_members')->where('family_id', $familyId)->pluck('id')->toArray();
            if (! empty($existingMemberIds)) {
                DB::connection('pgsql_annapurna')->table('dbt_apy.member_employment_natures')->whereIn('family_member_id', $existingMemberIds)->delete();
                DB::connection('pgsql_annapurna')->table('dbt_apy.member_govt_schemes')->whereIn('family_member_id', $existingMemberIds)->delete();
                DB::connection('pgsql_annapurna')->table('dbt_apy.member_other_ids')->whereIn('family_member_id', $existingMemberIds)->delete();
                DB::connection('pgsql_annapurna')->table('dbt_apy.family_members')->where('family_id', $familyId)->delete();
            }

            // 6. Insert HOF into dbt_apy.family_members
            $hofMemberId = DB::connection('pgsql_annapurna')->table('dbt_apy.family_members')->insertGetId([
                'family_id' => $familyId,
                'is_hof' => true,
                'member_name' => $this->formData['hof_name'] ?? '',
                'aadhaar_no' => $this->formData['hof_aadhaar'] ?? '',
                'mobile_no' => $this->formData['contact_no'] ?? null,
                'date_of_birth' => !empty($this->formData['hof_dob']) ? $this->formData['hof_dob'] : null,
                'gender' => $this->formData['hof_gender'] ?? null,
                'digital_ration_card_no' => $rationCardHouseholdId,
                'digital_ration_card_type' => $rationCardType,
                'social_category' => $this->formData['category'] ?? null,
                'bank_name' => $this->formData['hof_bank_name'] ?? null,
                'bank_account_no' => $this->formData['hof_acc_no'] ?? null,
                'ifsc_code' => $this->formData['hof_ifsc'] ?? null,
                'epic_no' => $this->formData['hof_epic_no'] ?? null,
                'part_no' => $this->formData['hof_ac_part_no'] ?? null,
                'caa_application_status' => $caaStatus,
                'caa_application_no' => $caaAppNo,
                'caa_certificate_no' => $caaCertNo,
                'sir2026tribunal_status' => $sirStatus,
                'sir2026case_details' => $sirCaseDetails,
                'has_four_wheeler' => $hasFourWheeler,
                'vehicle_count' => $vehicleCount,
                'vehicle_registration_no' => $vehicleReg,
                'vehicle_model' => $vehicleModel,
                'has_health_insurance' => $hasHealthInsurance,
                'health_insurance_type' => $healthInsuranceType === 'None' ? null : $healthInsuranceType,
                'health_insurance_sum_assured' => $healthInsuranceSumAssured,
                'health_insurance_annual_premium' => $healthInsurancePremium,
                'literacy_status' => $this->formData['hof_literate_status'] ?? null,
                'highest_educational_qualifications' => $this->formData['hof_highest_qualification'] ?? null,
                'gross_annual_income' => ! empty($this->formData['total_annual_income']) ? (float) $this->formData['total_annual_income'] : null,
                'pays_income_or_professional_tax' => (($this->formData['pays_tax'] ?? '') === 'Yes'),
                'pan_no' => $this->formData['hof_pan_no'] ?? null,
                'holds_constitutional_post' => $hasConstitutionalPost,
                'constitutional_post_member_no' => $constitutionalPostDetails,
                'is_registered_gst' => $hasGstReg,
                'gstin' => $gstin,
                'is_child' => false,
                'is_govt_pensioner' => $hasPensioner,
                'govt_pensioner_member_no' => $pensionerDetails,
                'relation_with_head_of_family' => 'Self',
                'applying_for_annapurna_bhandar' => $this->isHofFemale25to60() || (($this->formData['hof_applying_for_ay'] ?? '') === 'Yes'),
                'has_pan_card' => ! empty($this->formData['hof_pan_no']),
                'has_three_pucca_rooms' => (($this->formData['has_pucca_rooms'] ?? '') === 'Yes'),
                'owns_land' => $ownsLand,
                'landholding_size_decimals' => $landSizeDecimals,
                'lgd_district_code' => $lgdDistrictCode,
                'lgd_block_mc_code' => $lgdBlockMcCode,
                'lgd_gp_ward_code' => $lgdGpWardCode,
            ], 'id');

            // HOF employment nature
            if (! empty($this->formData['hof_employment_nature'])) {
                DB::connection('pgsql_annapurna')->table('dbt_apy.member_employment_natures')->insert([
                    'family_member_id' => $hofMemberId,
                    'employment_type' => $this->formData['hof_employment_nature'],
                    'lgd_district_code' => $lgdDistrictCode,
                ]);
            }

            // HOF govt schemes
            if (($this->formData['hof_has_dbt_benefits'] ?? 'No') === 'Yes') {
                foreach ($this->formData['hof_dbt_benefits'] as $benefit) {
                    if (! empty($benefit['scheme_name'])) {
                        DB::connection('pgsql_annapurna')->table('dbt_apy.member_govt_schemes')->insert([
                            'family_member_id' => $hofMemberId,
                            'scheme_name' => $benefit['scheme_name'],
                            'opt_out' => (bool) ($benefit['opt_out'] ?? false),
                            'lgd_district_code' => $lgdDistrictCode,
                        ]);
                    }
                }
            }

            // HOF credit card / other id
            if (! empty($this->formData['hof_kcc_type']) && $this->formData['hof_kcc_type'] !== 'None') {
                DB::connection('pgsql_annapurna')->table('dbt_apy.member_other_ids')->insert([
                    'family_member_id' => $hofMemberId,
                    'id_type' => $this->formData['hof_kcc_type'],
                    'issue_date' => $this->formData['hof_kcc_date'] ?? '',
                    'lgd_district_code' => $lgdDistrictCode,
                ]);
            }

            // 7. Insert other family members into dbt_apy.family_members
            foreach ($this->members as $index => $member) {
                $isChild = (($member['member_type'] ?? 'adult') === 'child');

                $mHasDigitalRationCard = ! $isChild && (($member['has_digital_ration_card'] ?? '') === 'Yes');
                $mRationCardNo = $mHasDigitalRationCard ? ($member['ration_card_no'] ?? null) : null;
                $mRationCardType = $mHasDigitalRationCard ? ($member['ration_card_type'] ?? null) : null;

                $mApplyingForAY = ! $isChild && ($this->isMemberFemale25to60($index) || (($member['applying_for_ay'] ?? 'No') === 'Yes'));
                $mBankName = $member['bank_name'] ?? null;
                $mAccNo = $member['acc_no'] ?? null;
                $mIfsc = $member['ifsc'] ?? null;

                $mCaaStatus = $isChild ? 'Not Applicable' : ($member['caa_status'] ?? 'Not Applicable');
                $mCaaAppNo = ! $isChild && $mCaaStatus === 'Applied' ? ($member['caa_app_no'] ?? null) : null;
                $mCaaCertNo = ! $isChild && $mCaaStatus === 'Issued' ? ($member['caa_cert_no'] ?? null) : null;

                $mSirStatus = $isChild ? 'Not Applicable' : ($member['sir_status'] ?? 'Not Applicable');
                $mSirCaseDetails = ! $isChild && $mSirStatus === 'Yes' ? ($member['sir_case_details'] ?? null) : null;

                $mHealthInsuranceType = $isChild ? 'No' : ($member['health_insurance_type'] ?? 'No');
                $mHasHealthInsurance = ! $isChild && ($mHealthInsuranceType !== 'No');
                $mHealthInsurancePremium = ($mHasHealthInsurance && !empty($member['health_insurance_premium'])) ? (float) $member['health_insurance_premium'] : null;
                $mHealthInsuranceSumAssured = ($mHasHealthInsurance && !empty($member['health_insurance_sum_assured'])) ? (float) $member['health_insurance_sum_assured'] : null;

                $memberId = DB::connection('pgsql_annapurna')->table('dbt_apy.family_members')->insertGetId([
                    'family_id' => $familyId,
                    'is_hof' => false,
                    'member_name' => $member['name'] ?? '',
                    'aadhaar_no' => $member['aadhaar'] ?? '',
                    'mobile_no' => null,
                    'date_of_birth' => !empty($member['dob']) ? $member['dob'] : null,
                    'gender' => !empty($member['gender']) ? $member['gender'] : null,
                    'digital_ration_card_no' => $mRationCardNo,
                    'digital_ration_card_type' => $mRationCardType,
                    'social_category' => $this->formData['category'] ?? null,
                    'bank_name' => $mBankName,
                    'bank_account_no' => $mAccNo,
                    'ifsc_code' => $mIfsc,
                    'epic_no' => $isChild ? null : (!empty($member['epic_no']) ? $member['epic_no'] : null),
                    'part_no' => $isChild ? null : (!empty($member['ac_part_no']) ? $member['ac_part_no'] : null),
                    'caa_application_status' => $mCaaStatus,
                    'caa_application_no' => $mCaaAppNo,
                    'caa_certificate_no' => $mCaaCertNo,
                    'sir2026tribunal_status' => $mSirStatus,
                    'sir2026case_details' => $mSirCaseDetails,
                    'has_four_wheeler' => false,
                    'has_health_insurance' => $mHasHealthInsurance,
                    'health_insurance_type' => $mHealthInsuranceType === 'No' ? null : $mHealthInsuranceType,
                    'health_insurance_sum_assured' => $mHealthInsuranceSumAssured,
                    'health_insurance_annual_premium' => $mHealthInsurancePremium,
                    'literacy_status' => $isChild ? null : (!empty($member['literate_status']) ? $member['literate_status'] : null),
                    'highest_educational_qualifications' => $isChild ? null : (!empty($member['highest_qualification']) ? $member['highest_qualification'] : null),
                    'gross_annual_income' => null,
                    'pays_income_or_professional_tax' => false,
                    'pan_no' => $isChild ? null : (!empty($member['pan_no']) ? $member['pan_no'] : null),
                    'holds_constitutional_post' => false,
                    'is_registered_gst' => false,
                    'is_child' => $isChild,
                    'is_govt_pensioner' => false,
                    'relation_with_head_of_family' => !empty($member['relation']) ? $member['relation'] : null,
                    'applying_for_annapurna_bhandar' => $mApplyingForAY,
                    'has_pan_card' => ! $isChild && ! empty($member['pan_no']),
                    'lgd_district_code' => $lgdDistrictCode,
                    'lgd_block_mc_code' => $lgdBlockMcCode,
                    'lgd_gp_ward_code' => $lgdGpWardCode,
                    'school_grade' => $isChild ? (!empty($member['school_grade']) ? $member['school_grade'] : null) : null,
                    'school_name' => $isChild ? (!empty($member['school_name']) ? $member['school_name'] : null) : null,
                    'school_type' => $isChild ? (!empty($member['school_type']) ? $member['school_type'] : null) : null,
                    'school_type_other' => $isChild ? (!empty($member['school_type_other']) ? $member['school_type_other'] : null) : null,
                    'vaccination_card_id' => $isChild ? (!empty($member['vaccination_card_id']) ? $member['vaccination_card_id'] : null) : null,
                    'vaccination_status' => $isChild ? (!empty($member['vaccination_status']) ? $member['vaccination_status'] : null) : null,
                    'vaccination_skip_reason_or_date' => $isChild ? (!empty($member['vaccination_skip_reason_or_date']) ? $member['vaccination_skip_reason_or_date'] : null) : null,
                ], 'id');

                // Member employment nature
                if (! $isChild && ! empty($member['employment_nature'])) {
                    DB::connection('pgsql_annapurna')->table('dbt_apy.member_employment_natures')->insert([
                        'family_member_id' => $memberId,
                        'employment_type' => $member['employment_nature'],
                        'lgd_district_code' => $lgdDistrictCode,
                    ]);
                }

                // Member govt schemes
                if (! $isChild && ($member['has_dbt_benefits'] ?? 'No') === 'Yes') {
                    foreach ($member['dbt_benefits'] as $benefit) {
                        if (! empty($benefit['scheme_name'])) {
                            DB::connection('pgsql_annapurna')->table('dbt_apy.member_govt_schemes')->insert([
                                'family_member_id' => $memberId,
                                'scheme_name' => $benefit['scheme_name'],
                                'opt_out' => (bool) ($benefit['opt_out'] ?? false),
                                'lgd_district_code' => $lgdDistrictCode,
                            ]);
                        }
                    }
                }

                // Member credit card / other id
                if (! $isChild && ! empty($member['kcc_type']) && $member['kcc_type'] !== 'None') {
                    DB::connection('pgsql_annapurna')->table('dbt_apy.member_other_ids')->insert([
                        'family_member_id' => $memberId,
                        'id_type' => $member['kcc_type'],
                        'issue_date' => $member['kcc_date'] ?? '',
                        'lgd_district_code' => $lgdDistrictCode,
                    ]);
                }
            }

            DB::connection('pgsql_annapurna')->commit();

            $this->successMessage = 'Application submitted successfully! Application ID: ' . $appId;
            $this->showSubmitModal = false;

            // Reset form
            $this->resetForm();
        } catch (\Exception $e) {
            DB::connection('pgsql_annapurna')->rollBack();
            Log::error('Error saving Annapurna Yojana application: ' . $e->getMessage());
            $this->errorMessage = 'An error occurred while saving the application: ' . $e->getMessage();
            $this->showSubmitModal = false;
        }
    }

    public function saveDraft()
    {
        // Don't save draft if HOF name and contact are both completely blank
        if (empty($this->formData['hof_name']) && empty($this->formData['contact_no'])) {
            return;
        }

        try {
            DB::connection('pgsql_annapurna')->beginTransaction();

            // 1. Get LGD codes for locations
            $districtId = $this->formData['district_id'] ?? null;
            $lgdDistrictCode = null;
            if ($districtId) {
                $districts = $this->getMasterDataArray('districts.js', 'districts');
                foreach ($districts as $d) {
                    if ((string) $d['id'] === (string) $districtId) {
                        $lgdDistrictCode = $d['id'];
                        break;
                    }
                }
            }

            $lgdBlockMcCode = null;
            $blockUrbanId = $this->formData['blockurban'] ?? null;
            if ($blockUrbanId) {
                if (($this->formData['rural_urban'] ?? null) == 2) {
                    $blocks = $this->getMasterDataArray('blocks.js', 'blocks');
                    foreach ($blocks as $b) {
                        if ((string) $b['id'] === (string) $blockUrbanId) {
                            $lgdBlockMcCode = $b['id'];
                            break;
                        }
                    }
                } else {
                    $ulbs = $this->getMasterDataArray('ulbs.js', 'ulbs');
                    foreach ($ulbs as $u) {
                        if ((string) $u['id'] === (string) $blockUrbanId) {
                            $lgdBlockMcCode = $u['id'];
                            break;
                        }
                    }
                }
            }

            $lgdGpWardCode = null;
            $gpWardId = $this->formData['gpward'] ?? null;
            if ($gpWardId) {
                if (($this->formData['rural_urban'] ?? null) == 2) {
                    $gps = $this->getMasterDataArray('gps.js', 'gps');
                    foreach ($gps as $g) {
                        if ((string) $g['id'] === (string) $gpWardId) {
                            $lgdGpWardCode = $g['id'];
                            break;
                        }
                    }
                } else {
                    $wards = $this->getMasterDataArray('ulb_wards.js', 'ulb_wards');
                    foreach ($wards as $w) {
                        if ((string) $w['id'] === (string) $gpWardId) {
                            $lgdGpWardCode = $w['id'];
                            break;
                        }
                    }
                }
            }

            $lgdDistrictCode = $lgdDistrictCode ? (int) $lgdDistrictCode : 0;
            $lgdBlockMcCode = $lgdBlockMcCode ? (int) $lgdBlockMcCode : 0;
            $lgdGpWardCode = $lgdGpWardCode ? (int) $lgdGpWardCode : 0;

            // 2. Construct address string
            $address = trim(($this->formData['house_no'] ? $this->formData['house_no'] . ', ' : '') . $this->formData['village_town'] . ', P.O. ' . $this->formData['post_office'] . ', P.S. ' . $this->formData['police_station'] . ', PIN ' . $this->formData['pincode']);

            // 3. Generate or reuse UUID for application_id
            if (! $this->appId) {
                $this->appId = (string) Str::uuid();
            }
            $appId = $this->appId;

            // 4. Pre-clean conditional fields
            $hasDigitalRationCard = (($this->formData['has_digital_ration_card'] ?? '') === 'Yes');
            $rationCardHouseholdId = $hasDigitalRationCard ? ($this->formData['hof_ration_card_id'] ?? null) : null;
            $rationCardType = $hasDigitalRationCard ? ($this->formData['ration_card_type'] ?? null) : null;
            $liftingMonthlyRation = $hasDigitalRationCard && (($this->formData['is_lifting_ration'] ?? '') === 'Yes');

            $hasConstitutionalPost = (($this->formData['has_constitutional_post'] ?? '') === 'Yes');
            $constitutionalPostDetails = $hasConstitutionalPost ? ($this->formData['constitutional_post_details'] ?? null) : null;

            $hasGstReg = (($this->formData['has_gst_reg'] ?? '') === 'Yes');
            $gstin = $hasGstReg ? ($this->formData['gstin'] ?? null) : null;

            $hasPensioner = (($this->formData['has_pensioner'] ?? '') === 'Yes');
            $pensionerDetails = $hasPensioner ? ($this->formData['pensioner_details'] ?? null) : null;

            $caaStatus = $this->formData['hof_caa_status'] ?? 'Not Applicable';
            $caaAppNo = $caaStatus === 'Applied' ? ($this->formData['hof_caa_app_no'] ?? null) : null;
            $caaCertNo = $caaStatus === 'Issued' ? ($this->formData['hof_caa_cert_no'] ?? null) : null;

            $sirStatus = $this->formData['hof_sir_status'] ?? 'Not Applicable';
            $sirCaseDetails = $sirStatus === 'Yes' ? ($this->formData['hof_sir_case_details'] ?? null) : null;

            $healthInsuranceType = $this->formData['health_insurance_type'] ?? 'None';
            $hasHealthInsurance = ($healthInsuranceType !== 'None');
            $healthInsurancePremium = ($hasHealthInsurance && !empty($this->formData['health_insurance_premium'])) ? (float) $this->formData['health_insurance_premium'] : null;
            $healthInsuranceSumAssured = ($hasHealthInsurance && !empty($this->formData['health_insurance_sum_assured'])) ? (float) $this->formData['health_insurance_sum_assured'] : null;

            $ownsLand = (($this->formData['owns_land'] ?? '') === 'Yes');
            $landSizeDecimals = ($ownsLand && !empty($this->formData['land_size_decimals'])) ? (float) $this->formData['land_size_decimals'] : null;

            $hasFourWheeler = (($this->formData['owns_4_wheeler'] ?? '') === 'Yes');
            $vehicleCount = $hasFourWheeler && ! empty($this->formData['num_vehicles']) ? (int) $this->formData['num_vehicles'] : null;
            $vehicleReg = $hasFourWheeler && ! empty($this->formData['vehicles']) ? json_encode(array_column($this->formData['vehicles'], 'reg_no')) : null;
            $vehicleModel = $hasFourWheeler && ! empty($this->formData['vehicles']) ? json_encode(array_column($this->formData['vehicles'], 'model')) : null;

            // 5. Update or Insert family details into dbt_apy.families
            $familyData = [
                'application_id' => $appId,
                'total_family_members' => (int) ($this->formData['num_family_members'] ?? 1),
                'lifting_monthly_ration' => $liftingMonthlyRation,
                'has_electricity_connection' => false,
                'is_agreed' => (bool) ($this->formData['agree_consent'] ?? false),
                'application_status' => 'DRAFT',
                'lgd_district_code' => $lgdDistrictCode,
                'lgd_block_mc_code' => $lgdBlockMcCode,
                'lgd_gp_ward_code' => $lgdGpWardCode,
                'address' => $address,
                'has_digital_ration_card' => $hasDigitalRationCard,
                'ration_card_household_id' => $rationCardHouseholdId,
                'no_of_illiterate_adults' => ! empty($this->formData['num_illiterate_adults']) ? (int) $this->formData['num_illiterate_adults'] : null,
                'no_of_literate_adults' => ! empty($this->formData['num_literate_adults']) ? (int) $this->formData['num_literate_adults'] : null,
                'total_annual_family_income' => ! empty($this->formData['total_annual_income']) ? (int) $this->formData['total_annual_income'] : null,
                'area_type' => ($this->formData['rural_urban'] ?? '') == 2 ? 'RURAL' : (($this->formData['rural_urban'] ?? '') == 1 ? 'URBAN' : null),
                'ulb' => ($this->formData['rural_urban'] ?? '') == 1 ? (int) ($this->formData['blockurban'] ?? null) : null,
                'updated_at' => now(),
            ];

            if ($this->familyId) {
                DB::connection('pgsql_annapurna')->table('dbt_apy.families')->where('id', $this->familyId)->update($familyData);
                $familyId = $this->familyId;
            } else {
                $familyData['created_at'] = now();
                $familyId = DB::connection('pgsql_annapurna')->table('dbt_apy.families')->insertGetId($familyData, 'id');
                $this->familyId = $familyId;
            }

            // Clear old entries to avoid duplicates on draft transitions
            $existingMemberIds = DB::connection('pgsql_annapurna')->table('dbt_apy.family_members')->where('family_id', $familyId)->pluck('id')->toArray();
            if (! empty($existingMemberIds)) {
                DB::connection('pgsql_annapurna')->table('dbt_apy.member_employment_natures')->whereIn('family_member_id', $existingMemberIds)->delete();
                DB::connection('pgsql_annapurna')->table('dbt_apy.member_govt_schemes')->whereIn('family_member_id', $existingMemberIds)->delete();
                DB::connection('pgsql_annapurna')->table('dbt_apy.member_other_ids')->whereIn('family_member_id', $existingMemberIds)->delete();
                DB::connection('pgsql_annapurna')->table('dbt_apy.family_members')->where('family_id', $familyId)->delete();
            }

            // 6. Insert HOF into dbt_apy.family_members
            $hofMemberId = DB::connection('pgsql_annapurna')->table('dbt_apy.family_members')->insertGetId([
                'family_id' => $familyId,
                'is_hof' => true,
                'member_name' => $this->formData['hof_name'] ?? '',
                'aadhaar_no' => $this->formData['hof_aadhaar'] ?? '',
                'mobile_no' => $this->formData['contact_no'] ?? null,
                'date_of_birth' => !empty($this->formData['hof_dob']) ? $this->formData['hof_dob'] : null,
                'gender' => $this->formData['hof_gender'] ?? null,
                'digital_ration_card_no' => $rationCardHouseholdId,
                'digital_ration_card_type' => $rationCardType,
                'social_category' => $this->formData['category'] ?? null,
                'bank_name' => $this->formData['hof_bank_name'] ?? null,
                'bank_account_no' => $this->formData['hof_acc_no'] ?? null,
                'ifsc_code' => $this->formData['hof_ifsc'] ?? null,
                'epic_no' => $this->formData['hof_epic_no'] ?? null,
                'part_no' => $this->formData['hof_ac_part_no'] ?? null,
                'caa_application_status' => $caaStatus,
                'caa_application_no' => $caaAppNo,
                'caa_certificate_no' => $caaCertNo,
                'sir2026tribunal_status' => $sirStatus,
                'sir2026case_details' => $sirCaseDetails,
                'has_four_wheeler' => $hasFourWheeler,
                'vehicle_count' => $vehicleCount,
                'vehicle_registration_no' => $vehicleReg,
                'vehicle_model' => $vehicleModel,
                'has_health_insurance' => $hasHealthInsurance,
                'health_insurance_type' => $healthInsuranceType === 'None' ? null : $healthInsuranceType,
                'health_insurance_sum_assured' => $healthInsuranceSumAssured,
                'health_insurance_annual_premium' => $healthInsurancePremium,
                'literacy_status' => $this->formData['hof_literate_status'] ?? null,
                'highest_educational_qualifications' => $this->formData['hof_highest_qualification'] ?? null,
                'gross_annual_income' => ! empty($this->formData['total_annual_income']) ? (float) $this->formData['total_annual_income'] : null,
                'pays_income_or_professional_tax' => (($this->formData['pays_tax'] ?? '') === 'Yes'),
                'pan_no' => $this->formData['hof_pan_no'] ?? null,
                'holds_constitutional_post' => $hasConstitutionalPost,
                'constitutional_post_member_no' => $constitutionalPostDetails,
                'is_registered_gst' => $hasGstReg,
                'gstin' => $gstin,
                'is_child' => false,
                'is_govt_pensioner' => $hasPensioner,
                'govt_pensioner_member_no' => $pensionerDetails,
                'relation_with_head_of_family' => 'Self',
                'applying_for_annapurna_bhandar' => $this->isHofFemale25to60() || (($this->formData['hof_applying_for_ay'] ?? '') === 'Yes'),
                'has_pan_card' => ! empty($this->formData['hof_pan_no']),
                'has_three_pucca_rooms' => (($this->formData['has_pucca_rooms'] ?? '') === 'Yes'),
                'owns_land' => $ownsLand,
                'landholding_size_decimals' => $landSizeDecimals,
                'lgd_district_code' => $lgdDistrictCode,
                'lgd_block_mc_code' => $lgdBlockMcCode,
                'lgd_gp_ward_code' => $lgdGpWardCode,
            ], 'id');

            // HOF employment nature
            if (! empty($this->formData['hof_employment_nature'])) {
                DB::connection('pgsql_annapurna')->table('dbt_apy.member_employment_natures')->insert([
                    'family_member_id' => $hofMemberId,
                    'employment_type' => $this->formData['hof_employment_nature'],
                    'lgd_district_code' => $lgdDistrictCode,
                ]);
            }

            // HOF govt schemes
            if (($this->formData['hof_has_dbt_benefits'] ?? 'No') === 'Yes') {
                foreach ($this->formData['hof_dbt_benefits'] as $benefit) {
                    if (! empty($benefit['scheme_name'])) {
                        DB::connection('pgsql_annapurna')->table('dbt_apy.member_govt_schemes')->insert([
                            'family_member_id' => $hofMemberId,
                            'scheme_name' => $benefit['scheme_name'],
                            'opt_out' => (bool) ($benefit['opt_out'] ?? false),
                            'lgd_district_code' => $lgdDistrictCode,
                        ]);
                    }
                }
            }

            // HOF credit card / other id
            if (! empty($this->formData['hof_kcc_type']) && $this->formData['hof_kcc_type'] !== 'None') {
                DB::connection('pgsql_annapurna')->table('dbt_apy.member_other_ids')->insert([
                    'family_member_id' => $hofMemberId,
                    'id_type' => $this->formData['hof_kcc_type'],
                    'issue_date' => $this->formData['hof_kcc_date'] ?? '',
                    'lgd_district_code' => $lgdDistrictCode,
                ]);
            }

            // 6. Insert other family members into dbt_apy.family_members
            foreach ($this->members as $index => $member) {
                $isChild = (($member['member_type'] ?? 'adult') === 'child');

                $mHasDigitalRationCard = ! $isChild && (($member['has_digital_ration_card'] ?? '') === 'Yes');
                $mRationCardNo = $mHasDigitalRationCard ? ($member['ration_card_no'] ?? null) : null;
                $mRationCardType = $mHasDigitalRationCard ? ($member['ration_card_type'] ?? null) : null;

                $mApplyingForAY = ! $isChild && ($this->isMemberFemale25to60($index) || (($member['applying_for_ay'] ?? 'No') === 'Yes'));
                $mBankName = $member['bank_name'] ?? null;
                $mAccNo = $member['acc_no'] ?? null;
                $mIfsc = $member['ifsc'] ?? null;

                $mCaaStatus = $isChild ? 'Not Applicable' : ($member['caa_status'] ?? 'Not Applicable');
                $mCaaAppNo = ! $isChild && $mCaaStatus === 'Applied' ? ($member['caa_app_no'] ?? null) : null;
                $mCaaCertNo = ! $isChild && $mCaaStatus === 'Issued' ? ($member['caa_cert_no'] ?? null) : null;

                $mSirStatus = $isChild ? 'Not Applicable' : ($member['sir_status'] ?? 'Not Applicable');
                $mSirCaseDetails = ! $isChild && $mSirStatus === 'Yes' ? ($member['sir_case_details'] ?? null) : null;

                $mHealthInsuranceType = $isChild ? 'No' : ($member['health_insurance_type'] ?? 'No');
                $mHasHealthInsurance = ! $isChild && ($mHealthInsuranceType !== 'No');
                $mHealthInsurancePremium = ($mHasHealthInsurance && !empty($member['health_insurance_premium'])) ? (float) $member['health_insurance_premium'] : null;
                $mHealthInsuranceSumAssured = ($mHasHealthInsurance && !empty($member['health_insurance_sum_assured'])) ? (float) $member['health_insurance_sum_assured'] : null;

                $memberId = DB::connection('pgsql_annapurna')->table('dbt_apy.family_members')->insertGetId([
                    'family_id' => $familyId,
                    'is_hof' => false,
                    'member_name' => $member['name'] ?? '',
                    'aadhaar_no' => $member['aadhaar'] ?? '',
                    'mobile_no' => null,
                    'date_of_birth' => !empty($member['dob']) ? $member['dob'] : null,
                    'gender' => !empty($member['gender']) ? $member['gender'] : null,
                    'digital_ration_card_no' => $mRationCardNo,
                    'digital_ration_card_type' => $mRationCardType,
                    'social_category' => $this->formData['category'] ?? null,
                    'bank_name' => $mBankName,
                    'bank_account_no' => $mAccNo,
                    'ifsc_code' => $mIfsc,
                    'epic_no' => $isChild ? null : (!empty($member['epic_no']) ? $member['epic_no'] : null),
                    'part_no' => $isChild ? null : (!empty($member['ac_part_no']) ? $member['ac_part_no'] : null),
                    'caa_application_status' => $mCaaStatus,
                    'caa_application_no' => $mCaaAppNo,
                    'caa_certificate_no' => $mCaaCertNo,
                    'sir2026tribunal_status' => $mSirStatus,
                    'sir2026case_details' => $mSirCaseDetails,
                    'has_four_wheeler' => false,
                    'has_health_insurance' => $mHasHealthInsurance,
                    'health_insurance_type' => $mHealthInsuranceType === 'No' ? null : $mHealthInsuranceType,
                    'health_insurance_sum_assured' => $mHealthInsuranceSumAssured,
                    'health_insurance_annual_premium' => $mHealthInsurancePremium,
                    'literacy_status' => $isChild ? null : (!empty($member['literate_status']) ? $member['literate_status'] : null),
                    'highest_educational_qualifications' => $isChild ? null : (!empty($member['highest_qualification']) ? $member['highest_qualification'] : null),
                    'gross_annual_income' => null,
                    'pays_income_or_professional_tax' => false,
                    'pan_no' => $isChild ? null : (!empty($member['pan_no']) ? $member['pan_no'] : null),
                    'holds_constitutional_post' => false,
                    'is_registered_gst' => false,
                    'is_child' => $isChild,
                    'is_govt_pensioner' => false,
                    'relation_with_head_of_family' => !empty($member['relation']) ? $member['relation'] : null,
                    'applying_for_annapurna_bhandar' => $mApplyingForAY,
                    'has_pan_card' => ! $isChild && ! empty($member['pan_no']),
                    'lgd_district_code' => $lgdDistrictCode,
                    'lgd_block_mc_code' => $lgdBlockMcCode,
                    'lgd_gp_ward_code' => $lgdGpWardCode,
                    'school_grade' => $isChild ? (!empty($member['school_grade']) ? $member['school_grade'] : null) : null,
                    'school_name' => $isChild ? (!empty($member['school_name']) ? $member['school_name'] : null) : null,
                    'school_type' => $isChild ? (!empty($member['school_type']) ? $member['school_type'] : null) : null,
                    'school_type_other' => $isChild ? (!empty($member['school_type_other']) ? $member['school_type_other'] : null) : null,
                    'vaccination_card_id' => $isChild ? (!empty($member['vaccination_card_id']) ? $member['vaccination_card_id'] : null) : null,
                    'vaccination_status' => $isChild ? (!empty($member['vaccination_status']) ? $member['vaccination_status'] : null) : null,
                    'vaccination_skip_reason_or_date' => $isChild ? (!empty($member['vaccination_skip_reason_or_date']) ? $member['vaccination_skip_reason_or_date'] : null) : null,
                ], 'id');

                // Member employment nature
                if (! $isChild && ! empty($member['employment_nature'])) {
                    DB::connection('pgsql_annapurna')->table('dbt_apy.member_employment_natures')->insert([
                        'family_member_id' => $memberId,
                        'employment_type' => $member['employment_nature'],
                        'lgd_district_code' => $lgdDistrictCode,
                    ]);
                }

                // Member govt schemes
                if (! $isChild && ($member['has_dbt_benefits'] ?? 'No') === 'Yes') {
                    foreach ($member['dbt_benefits'] as $benefit) {
                        if (! empty($benefit['scheme_name'])) {
                            DB::connection('pgsql_annapurna')->table('dbt_apy.member_govt_schemes')->insert([
                                'family_member_id' => $memberId,
                                'scheme_name' => $benefit['scheme_name'],
                                'opt_out' => (bool) ($benefit['opt_out'] ?? false),
                                'lgd_district_code' => $lgdDistrictCode,
                            ]);
                        }
                    }
                }

                // Member credit card / other id
                if (! $isChild && ! empty($member['kcc_type']) && $member['kcc_type'] !== 'None') {
                    DB::connection('pgsql_annapurna')->table('dbt_apy.member_other_ids')->insert([
                        'family_member_id' => $memberId,
                        'id_type' => $member['kcc_type'],
                        'issue_date' => $member['kcc_date'] ?? '',
                        'lgd_district_code' => $lgdDistrictCode,
                    ]);
                }
            }

            // Update session data
            session([
                'annapurna_form_data' => $this->formData,
                'annapurna_members' => $this->members,
                'annapurna_family_id' => $familyId,
                'annapurna_app_id' => $this->appId,
            ]);

            DB::connection('pgsql_annapurna')->commit();

            // Mark as clean — no unsaved changes anymore
            $this->isDirty = false;
        } catch (\Exception $e) {
            DB::connection('pgsql_annapurna')->rollBack();
            Log::error('Error saving draft of Annapurna Yojana: ' . $e->getMessage());
            session()->flash('error', 'Draft save failed. Please try again.');
        }
    }

    private function getMasterDataArray($filename, $varName)
    {
        $filePath = public_path('js/master-data/' . $filename);
        if (! file_exists($filePath)) {
            $filePath = base_path('public/js/master-data/' . $filename);
            if (! file_exists($filePath)) {
                return [];
            }
        }

        $content = file_get_contents($filePath);
        $startPos = strpos($content, '[');
        $endPos = strrpos($content, ']');
        if ($startPos === false || $endPos === false) {
            return [];
        }

        $jsArrayStr = substr($content, $startPos, $endPos - $startPos + 1);

        // Normalize JavaScript keys to valid double-quoted JSON keys
        $jsonStr = preg_replace('/([{,]\s*)([a-zA-Z_][a-zA-Z0-9_]*)\s*:/', '$1"$2":', $jsArrayStr);
        // Remove trailing commas before closing braces/brackets
        $jsonStr = preg_replace('/,\s*([}\]])/', '$1', $jsonStr);
        // Strip JS comments
        $jsonStr = preg_replace('!/\*.*?\*/!s', '', $jsonStr);
        $jsonStr = preg_replace('!//.*?[\r\n]!', '', $jsonStr);

        $decoded = json_decode($jsonStr, true);

        return is_array($decoded) ? $decoded : [];
    }

    private function validateVerhoeff($aadhaar)
    {
        if (! preg_match('/^\d{12}$/', $aadhaar)) {
            return false;
        }

        $d = [
            [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
            [1, 2, 3, 4, 0, 6, 7, 8, 9, 5],
            [2, 3, 4, 0, 1, 7, 8, 9, 5, 6],
            [3, 4, 0, 1, 2, 8, 9, 5, 6, 7],
            [4, 0, 1, 2, 3, 9, 5, 6, 7, 8],
            [5, 9, 8, 7, 6, 0, 4, 3, 2, 1],
            [6, 5, 9, 8, 7, 1, 0, 4, 3, 2],
            [7, 6, 5, 9, 8, 2, 1, 0, 4, 3],
            [8, 7, 6, 5, 9, 3, 2, 1, 0, 4],
            [9, 8, 7, 6, 5, 4, 3, 2, 1, 0],
        ];

        $p = [
            [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
            [1, 5, 7, 6, 2, 8, 3, 0, 9, 4],
            [5, 8, 0, 3, 7, 9, 6, 1, 4, 2],
            [8, 9, 1, 6, 0, 4, 3, 5, 2, 7],
            [9, 4, 5, 3, 1, 2, 6, 8, 7, 0],
            [4, 2, 8, 6, 5, 7, 3, 9, 0, 1],
            [2, 7, 9, 3, 8, 0, 6, 4, 1, 5],
            [7, 0, 4, 6, 9, 1, 3, 2, 5, 8],
        ];

        $digits = array_reverse(str_split($aadhaar));
        $c = 0;
        foreach ($digits as $i => $digit) {
            $c = $d[$c][$p[$i % 8][$digit]];
        }

        return $c === 0;
    }

    public function getAgeFromDob($dob)
    {
        if (empty($dob)) {
            return 0;
        }
        try {
            $birthDate = new \DateTime($dob);
            $today = new \DateTime;

            return $today->diff($birthDate)->y;
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function isHofFemale25to60()
    {
        $gender = $this->formData['hof_gender'] ?? '';
        $dob = $this->formData['hof_dob'] ?? '';
        $age = $this->getAgeFromDob($dob);

        return $gender === 'Female' && $age >= 25 && $age <= 60;
    }

    public function isMemberFemale25to60($index)
    {
        if (! isset($this->members[$index])) {
            return false;
        }
        $member = $this->members[$index];
        $gender = $member['gender'] ?? '';
        $dob = $member['dob'] ?? '';
        $age = $this->getAgeFromDob($dob);

        return $gender === 'Female' && $age >= 25 && $age <= 60;
    }

    public function render()
    {
        return view('livewire.annapurna-yojana-form');
    }
}
