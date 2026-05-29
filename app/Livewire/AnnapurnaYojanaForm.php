<?php

namespace App\Livewire;

use App\Services\AnnapurnaYojanaService;
use Illuminate\Support\Facades\Crypt;
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

    public $createdByDistCode = null;

    public $createdByLocalBodyCode = null;

    // Form Navigation & Tabs
    public $activeMemberIndex = 0; // 0 = HOF, 1+ = Members

    public $activeSection = 'basic'; // basic, identity, bank, income, declaration

    // Form Data array
    public array $formData = [];

    // Master lists for location dropdowns
    public $districts = [];

    public $blocks = [];

    public $gps = [];

    public $assemblies = [];

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

    public $relations = [];

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
            'hof_assembly_constituency' => '',
            'hof_part_no' => '',

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
            'has_health_insurance' => '',
            'health_insurance_type' => '',
            'health_insurance_premium' => '',
            'health_insurance_sum_assured' => '',

            // Income / Profession details (Section D)
            'pays_tax' => '',
            'has_pan_card' => '',
            'hof_pan_name' => '',
            'hof_pan_no' => '',
            'hof_employment_nature' => [],
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
            'hof_kcc_cards' => [
                ['type' => '', 'id_no' => '', 'date' => '', 'issuing_authority' => ''],
            ],

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
            $this->relations = $masterData['relations'] ?? [];
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

        // Load all assemblies from master-data file
        $this->assemblies = $this->getMasterDataArray('assemblies.js', 'assemblies');

        // Start with empty members list
        $this->members = [];

        // Initialize created_by location codes from session
        $selectLgd = session('lgd_session');
        if (! empty($selectLgd['district_id'])) {
            $this->createdByDistCode = (int) Crypt::decryptString($selectLgd['district_id']);
        }
        if (! empty($selectLgd['block_id'])) {
            $this->createdByLocalBodyCode = (int) Crypt::decryptString($selectLgd['block_id']);
        }
        if (! empty($selectLgd['subdivision_id'])) {
            $this->createdByLocalBodyCode = (int) Crypt::decryptString($selectLgd['subdivision_id']);
        }
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
        } elseif ($field === 'owns_land' && $value === 'No') {
            $this->formData['land_size_decimals'] = '';
        } elseif ($field === 'has_pan_card' && $value === 'No') {
            $this->formData['hof_pan_name'] = '';
            $this->formData['hof_pan_no'] = '';
        } elseif ($field === 'has_health_insurance' && $value === 'No') {
            $this->formData['health_insurance_type'] = '';
            $this->formData['health_insurance_premium'] = '';
            $this->formData['health_insurance_sum_assured'] = '';
        } elseif ($field === 'has_constitutional_post' && $value !== 'Yes') {
            $this->formData['constitutional_post_details'] = '';
        } elseif ($field === 'has_gst_reg' && $value !== 'Yes') {
            $this->formData['gstin'] = '';
        } elseif ($field === 'has_pensioner' && $value !== 'Yes') {
            $this->formData['pensioner_details'] = '';
        } elseif ($field === 'has_digital_ration_card' && $value === 'No') {
            $this->formData['ration_card_type'] = '';
            $this->formData['hof_ration_card_id'] = '';
        } elseif ($field === 'hof_caa_status' && $value === 'Not Applicable') {
            $this->formData['hof_caa_app_no'] = '';
            $this->formData['hof_caa_cert_no'] = '';
        } elseif (str_starts_with($field, 'hof_kcc_cards.')) {
            $parts = explode('.', $field);
            if (count($parts) === 3 && $parts[2] === 'type') {
                $cIndex = (int) $parts[1];
                if (empty($value)) {
                    $this->formData['hof_kcc_cards'][$cIndex]['id_no'] = '';
                    $this->formData['hof_kcc_cards'][$cIndex]['date'] = '';
                    $this->formData['hof_kcc_cards'][$cIndex]['issuing_authority'] = '';
                }
            }
        } elseif ($field === 'hof_sir_status' && $value !== 'Yes') {
            $this->formData['hof_sir_case_details'] = '';
        } elseif ($field === 'hof_has_dbt_benefits' && $value !== 'Yes') {
            $this->formData['hof_dbt_benefits'] = [
                ['scheme_name' => '', 'opt_out' => false],
            ];
        }

        if ($field === 'hof_gender' || $field === 'hof_dob') {
            if (!$this->isHofFemale25to60()) {
                $this->formData['hof_applying_for_ay'] = 'Yes';
            }
        }

        if ($field === 'hof_literate_status') {
            if ($value !== 'Literate') {
                $this->formData['hof_highest_qualification'] = '';
            }
            $this->calculateAdultLiteracyCounts();
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

        if (count($parts) === 4 && $parts[1] === 'kcc_cards' && $parts[3] === 'type') {
            $index = (int) $parts[0];
            $cIndex = (int) $parts[2];
            if (empty($value) && isset($this->members[$index]['kcc_cards'][$cIndex])) {
                $this->members[$index]['kcc_cards'][$cIndex]['id_no'] = '';
                $this->members[$index]['kcc_cards'][$cIndex]['date'] = '';
                $this->members[$index]['kcc_cards'][$cIndex]['issuing_authority'] = '';
            }
        }

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
                } elseif ($subField === 'has_health_insurance' && $value === 'No') {
                    $this->members[$index]['health_insurance_type'] = '';
                    $this->members[$index]['health_insurance_premium'] = '';
                    $this->members[$index]['health_insurance_sum_assured'] = '';
                } elseif ($subField === 'has_pan_card' && $value === 'No') {
                    $this->members[$index]['pan_name'] = '';
                    $this->members[$index]['pan_no'] = '';
                } elseif ($subField === 'caa_status' && $value === 'Not Applicable') {
                    $this->members[$index]['caa_app_no'] = '';
                    $this->members[$index]['caa_cert_no'] = '';
                } elseif ($subField === 'sir_status' && $value !== 'Yes') {
                    $this->members[$index]['sir_case_details'] = '';
                } elseif ($subField === 'vaccination_status') {
                    if ($value === 'Yes') {
                        $this->members[$index]['vaccination_skip_reason_or_date'] = '';
                    } elseif ($value === 'No') {
                        $this->members[$index]['vaccination_card_id'] = '';
                    } elseif (empty($value)) {
                        $this->members[$index]['vaccination_card_id'] = '';
                        $this->members[$index]['vaccination_skip_reason_or_date'] = '';
                    }
                } elseif ($subField === 'literate_status') {
                    if ($value !== 'Literate') {
                        $this->members[$index]['highest_qualification'] = '';
                    }
                    $this->calculateAdultLiteracyCounts();
                } elseif ($subField === 'member_type') {
                    if ($value === 'child') {
                        $this->members[$index]['literate_status'] = '';
                        $this->members[$index]['highest_qualification'] = '';
                    } else {
                        $this->members[$index]['school_grade'] = '';
                        $this->members[$index]['school_name'] = '';
                        $this->members[$index]['school_type'] = '';
                        $this->members[$index]['school_type_other'] = '';
                        $this->members[$index]['vaccination_status'] = '';
                        $this->members[$index]['vaccination_card_id'] = '';
                        $this->members[$index]['vaccination_skip_reason_or_date'] = '';
                    }
                    $this->calculateAdultLiteracyCounts();
                } elseif ($subField === 'has_dbt_benefits' && $value !== 'Yes') {
                    $this->members[$index]['dbt_benefits'] = [
                        ['scheme_name' => '', 'opt_out' => false],
                    ];
                } elseif ($subField === 'dob' || $subField === 'gender') {
                    if (!$this->isMemberFemale25to60($index)) {
                        $this->members[$index]['applying_for_ay'] = 'No';
                        $this->members[$index]['bank_name'] = '';
                        $this->members[$index]['acc_no'] = '';
                        $this->members[$index]['ifsc'] = '';
                    }
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

    public function getFilteredAssemblies()
    {
        $assemblies = $this->getMasterDataArray('assemblies.js', 'assemblies');
        usort($assemblies, function ($a, $b) {
            return strcmp($a['text'] ?? '', $b['text'] ?? '');
        });
        return array_values($assemblies);
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
            'assembly_constituency' => '',
            'part_no' => '',

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
            'has_health_insurance' => '',
            'health_insurance_type' => '',
            'health_insurance_premium' => '',
            'health_insurance_sum_assured' => '',

            // Section D (Income/PAN/Education)
            'has_pan_card' => '',
            'pan_name' => '',
            'pan_no' => '',
            'employment_nature' => '',
            'literate_status' => '',
            'highest_qualification' => '',

            // Section E (CAA / KCC / SIR)
            'caa_status' => 'Not Applicable',
            'caa_app_no' => '',
            'caa_cert_no' => '',
            'kcc_cards' => [
                ['type' => '', 'id_no' => '', 'date' => '', 'issuing_authority' => ''],
            ],
            'sir_status' => 'Not Applicable',
            'sir_case_details' => '',

            // Section G (Government Scheme Benefits)
            'has_dbt_benefits' => 'No',
            'dbt_benefits' => [
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
        $this->calculateAdultLiteracyCounts();
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

        // Recalculate literacy counts
        $this->calculateAdultLiteracyCounts();

        // Removing a member is always a structural change — force save
        $this->isDirty = true;
        $this->saveDraft();
    }

    public function addHofKccCard()
    {
        $this->formData['hof_kcc_cards'][] = [
            'type' => '',
            'id_no' => '',
            'date' => '',
            'issuing_authority' => '',
        ];
        $this->isDirty = true;
    }

    public function removeHofKccCard($index)
    {
        if (count($this->formData['hof_kcc_cards']) > 1) {
            unset($this->formData['hof_kcc_cards'][$index]);
            $this->formData['hof_kcc_cards'] = array_values($this->formData['hof_kcc_cards']);
        } else {
            $this->formData['hof_kcc_cards'] = [
                ['type' => '', 'id_no' => '', 'date' => '', 'issuing_authority' => ''],
            ];
        }
        $this->isDirty = true;
    }

    public function addMemberKccCard($mIndex)
    {
        if (isset($this->members[$mIndex])) {
            $this->members[$mIndex]['kcc_cards'][] = [
                'type' => '',
                'id_no' => '',
                'date' => '',
                'issuing_authority' => '',
            ];
            $this->isDirty = true;
        }
    }

    public function removeMemberKccCard($mIndex, $cIndex)
    {
        if (isset($this->members[$mIndex])) {
            if (count($this->members[$mIndex]['kcc_cards']) > 1) {
                unset($this->members[$mIndex]['kcc_cards'][$cIndex]);
                $this->members[$mIndex]['kcc_cards'] = array_values($this->members[$mIndex]['kcc_cards']);
            } else {
                $this->members[$mIndex]['kcc_cards'] = [
                    ['type' => '', 'id_no' => '', 'date' => '', 'issuing_authority' => ''],
                ];
            }
            $this->isDirty = true;
        }
    }

    public function addHofDbtBenefit()
    {
        $this->formData['hof_dbt_benefits'][] = ['scheme_name' => '', 'opt_out' => false];
        $this->isDirty = true;
    }

    public function removeHofDbtBenefit($index)
    {
        if (count($this->formData['hof_dbt_benefits']) > 1) {
            unset($this->formData['hof_dbt_benefits'][$index]);
            $this->formData['hof_dbt_benefits'] = array_values($this->formData['hof_dbt_benefits']);
        } else {
            $this->formData['hof_dbt_benefits'] = [
                ['scheme_name' => '', 'opt_out' => false],
            ];
        }
        $this->isDirty = true;
    }

    public function addMemberDbtBenefit($mIndex)
    {
        if (isset($this->members[$mIndex])) {
            $this->members[$mIndex]['dbt_benefits'][] = ['scheme_name' => '', 'opt_out' => false];
            $this->isDirty = true;
        }
    }

    public function removeMemberDbtBenefit($mIndex, $bIndex)
    {
        if (isset($this->members[$mIndex])) {
            if (count($this->members[$mIndex]['dbt_benefits']) > 1) {
                unset($this->members[$mIndex]['dbt_benefits'][$bIndex]);
                $this->members[$mIndex]['dbt_benefits'] = array_values($this->members[$mIndex]['dbt_benefits']);
            } else {
                $this->members[$mIndex]['dbt_benefits'] = [
                    ['scheme_name' => '', 'opt_out' => false],
                ];
            }
            $this->isDirty = true;
        }
    }

    public function selectMember($index)
    {
        if ($index > $this->activeMemberIndex) {
            // Going forwards: validate intermediate members
            for ($m = $this->activeMemberIndex; $m < $index; $m++) {
                $this->validateMember($m);
            }
        }

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

    public function selectSection($section)
    {
        $sectionsList = array_keys($this->getSections());
        $indexX = array_search($this->activeSection, $sectionsList);
        $indexY = array_search($section, $sectionsList);

        if ($indexX !== false && $indexY !== false && $indexY > $indexX) {
            // Going forwards: validate intermediate sections for the current member
            for ($i = $indexX; $i < $indexY; $i++) {
                $sectionToValidate = $sectionsList[$i];
                try {
                    $this->validateSection($sectionToValidate);
                } catch (\Illuminate\Validation\ValidationException $e) {
                    $this->activeSection = $sectionToValidate;
                    throw $e;
                }
            }
        }

        if ($section === 'declaration' && ! $this->areAllMembersFullyFilled()) {
            return;
        }
        // Only hit DB if something actually changed
        if ($this->isDirty) {
            $this->saveDraft();
        }
        $this->activeSection = $section;
    }

    private function validateMember($memberIndex)
    {
        $originalMemberIndex = $this->activeMemberIndex;
        $originalSection = $this->activeSection;

        $this->activeMemberIndex = $memberIndex;

        $sectionsList = array_keys($this->getSections());
        foreach ($sectionsList as $sec) {
            if ($sec === 'declaration') {
                continue;
            }
            try {
                $this->validateSection($sec);
            } catch (\Illuminate\Validation\ValidationException $e) {
                $this->activeSection = $sec;
                throw $e;
            }
        }

        $this->activeMemberIndex = $originalMemberIndex;
        $this->activeSection = $originalSection;
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
                $hasIns = ! empty($this->formData['has_health_insurance']);
                $insOk = $hasIns && ($this->formData['has_health_insurance'] === 'No' || ! empty($this->formData['health_insurance_type']));
                return ! empty($this->formData['has_pucca_rooms']) &&
                    ! empty($this->formData['owns_land']) &&
                    ! empty($this->formData['owns_4_wheeler']) &&
                    $insOk;
            }

            if ($section === 'income_profession') {
                return ! empty($this->formData['pays_tax']) &&
                    ! empty($this->formData['total_annual_income']) &&
                    is_numeric($this->formData['total_annual_income']);
            }

            if ($section === 'other_docs') {
                $caaStatus = $this->formData['hof_caa_status'] ?? 'Not Applicable';
                if ($caaStatus === 'Applied' && empty($this->formData['hof_caa_app_no'])) {
                    return false;
                }
                if ($caaStatus === 'Issued' && empty($this->formData['hof_caa_cert_no'])) {
                    return false;
                }
                $sirStatus = $this->formData['hof_sir_status'] ?? 'Not Applicable';
                if ($sirStatus === 'Yes' && empty($this->formData['hof_sir_case_details'])) {
                    return false;
                }
                foreach ($this->formData['hof_kcc_cards'] ?? [] as $card) {
                    if (!empty($card['type']) && empty($card['id_no'])) {
                        return false;
                    }
                }
                return true;
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

            if ($section === 'other_docs') {
                if (($member['member_type'] ?? 'adult') === 'child') {
                    return true;
                }
                $caaStatus = $member['caa_status'] ?? 'Not Applicable';
                if ($caaStatus === 'Applied' && empty($member['caa_app_no'])) {
                    return false;
                }
                if ($caaStatus === 'Issued' && empty($member['caa_cert_no'])) {
                    return false;
                }
                $sirStatus = $member['sir_status'] ?? 'Not Applicable';
                if ($sirStatus === 'Yes' && empty($member['sir_case_details'])) {
                    return false;
                }
                foreach ($member['kcc_cards'] ?? [] as $card) {
                    if (!empty($card['type']) && empty($card['id_no'])) {
                        return false;
                    }
                }
                return true;
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

    /**
     * Get validation rules for a section, or the full form if null.
     */
    protected function getValidationRules(?string $section = null): array
    {
        $rules = [];

        if ($section !== null) {
            // Section-specific validation (for validateSection)
            if ($section === 'family_identity') {
                if ($this->activeMemberIndex === 0) {
                    $rules['formData.hof_name'] = ['required', 'string', 'max:255', 'regex:/^[\p{L}\s.\'\-]+$/u'];
                    $rules['formData.hof_dob'] = 'required|date|before:today';
                    $rules['formData.hof_gender'] = 'required|in:Male,Female,Other';
                    $rules['formData.contact_no'] = 'required|digits:10';
                    $rules['formData.category'] = 'required';
                    $rules['formData.district_id'] = 'required';
                    $rules['formData.rural_urban'] = 'required';
                    $rules['formData.blockurban'] = 'required';
                    $rules['formData.gpward'] = 'required';
                    $rules['formData.village_town'] = 'required|string|max:200';
                    $rules['formData.police_station'] = 'required|string|max:200';
                    $rules['formData.post_office'] = 'required|string|max:200';
                    $rules['formData.pincode'] = 'required|digits:6';
                    $rules['formData.hof_aadhaar'] = [
                        'required',
                        'digits:12',
                        function ($attribute, $value, $fail) {
                            if (! $this->validateVerhoeff($value)) {
                                $fail('The HOF Aadhaar number is invalid (checksum validation failed).');
                            }
                        },
                    ];
                    $rules['formData.hof_bank_name'] = ['required', 'string', 'max:100', 'regex:/^[\p{L}\s.\'\-]+$/u'];
                    $rules['formData.hof_acc_no'] = 'required|digits_between:9,18';
                    $rules['formData.hof_ifsc'] = ['required', 'size:11', 'regex:/^[A-Z]{4}0[A-Z0-9]{6}$/'];
                    $rules['formData.hof_epic_no'] = ['nullable', 'regex:/^[A-Z]{3}[0-9]{7}$/'];
                    if (!empty($this->formData['hof_epic_no'])) {
                        $rules['formData.hof_assembly_constituency'] = 'required';
                        $rules['formData.hof_part_no'] = 'required|string|max:100';
                    }

                    $category = $this->formData['category'] ?? '';
                    if (in_array($category, ['SC', 'ST', 'OBC'])) {
                        $rules['formData.caste_certificate_no'] = 'required|string|max:100';
                    } elseif ($category == 'UR-EWS') {
                        $rules['formData.ews_certificate_no'] = 'required|string|max:100';
                    } elseif ($category == 'PVTG') {
                        $rules['formData.pvtg_certificate_no'] = 'required|string|max:100';
                    }
                } else {
                    $index = $this->activeMemberIndex - 1;
                    $member = $this->members[$index] ?? [];
                    $rules["members.{$index}.member_type"] = 'required|in:adult,child';
                    $rules["members.{$index}.name"] = ['required', 'string', 'max:255', 'regex:/^[\p{L}\s.\'\-]+$/u'];
                    $rules["members.{$index}.dob"] = 'required|date|before:today';
                    $rules["members.{$index}.gender"] = 'required';
                    $rules["members.{$index}.relation"] = 'required';

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
                        if (!empty($member['epic_no'])) {
                            $rules["members.{$index}.assembly_constituency"] = 'required';
                            $rules["members.{$index}.part_no"] = 'required|string|max:100';
                        }

                        if ($this->isMemberFemale25to60($index) || (($member['applying_for_ay'] ?? 'No') === 'Yes')) {
                            $rules["members.{$index}.bank_name"] = ['required', 'string', 'max:100', 'regex:/^[\p{L}\s.\'\-]+$/u'];
                            $rules["members.{$index}.acc_no"] = 'required|digits_between:9,18';
                            $rules["members.{$index}.ifsc"] = ['required', 'size:11', 'regex:/^[A-Z]{4}0[A-Z0-9]{6}$/'];
                        }
                    }
                }
            } elseif ($section === 'ration_subsidy') {
                if ($this->activeMemberIndex === 0) {
                    $rules['formData.has_digital_ration_card'] = 'required';
                    $rules['formData.is_lifting_ration'] = 'required';
                }
            } elseif ($section === 'assets') {
                if ($this->activeMemberIndex === 0) {
                    $rules['formData.has_pucca_rooms'] = 'required';
                    $rules['formData.owns_land'] = 'required';
                    $rules['formData.owns_4_wheeler'] = 'required';
                    $rules['formData.has_health_insurance'] = 'required';

                    if (($this->formData['owns_4_wheeler'] ?? '') === 'Yes') {
                        $rules['formData.num_vehicles'] = 'required|integer|min:1';
                        foreach ($this->formData['vehicles'] ?? [] as $vi => $vehicle) {
                            $rules["formData.vehicles.{$vi}.reg_no"] = ['required', 'string', 'regex:/^[A-Z]{2}[ -]?[0-9]{2}[ -]?[A-Z]{1,3}[ -]?[0-9]{4}$/i'];
                            $rules["formData.vehicles.{$vi}.model"] = 'required|string|max:100';
                        }
                    }

                    if (($this->formData['has_health_insurance'] ?? '') === 'Yes') {
                        $rules['formData.health_insurance_type'] = 'required|in:Government,Private';
                    }
                } else {
                    $index = $this->activeMemberIndex - 1;
                    $member = $this->members[$index] ?? [];
                    if (($member['member_type'] ?? 'adult') === 'adult') {
                        $rules["members.{$index}.has_health_insurance"] = 'required';
                        if (($member['has_health_insurance'] ?? '') === 'Yes') {
                            $rules["members.{$index}.health_insurance_type"] = 'required|in:Government,Private';
                        }
                    }
                }
            } elseif ($section === 'income_profession') {
                if ($this->activeMemberIndex === 0) {
                    $rules['formData.pays_tax'] = 'required';
                    $rules['formData.total_annual_income'] = 'required|numeric|min:0';
                    $rules['formData.has_pan_card'] = 'required|in:Yes,No';
                    if (($this->formData['has_pan_card'] ?? '') === 'Yes') {
                        $rules['formData.hof_pan_name'] = ['required', 'string', 'max:255', 'regex:/^[\p{L}\s.\'\-]+$/u'];
                        $rules['formData.hof_pan_no'] = ['required', 'regex:/^[A-Z]{3}[CPHFATBLJG][A-Z][0-9]{4}[A-Z]$/'];
                    }
                    $rules['formData.has_constitutional_post'] = 'required|in:Yes,No';
                    if (($this->formData['has_constitutional_post'] ?? '') === 'Yes') {
                        $rules['formData.constitutional_post_details'] = 'required|string|max:255';
                    }
                    $rules['formData.has_gst_reg'] = 'required|in:Yes,No';
                    if (($this->formData['has_gst_reg'] ?? '') === 'Yes') {
                        $rules['formData.gstin'] = 'required|string|max:100';
                    }
                    $rules['formData.has_pensioner'] = 'required|in:Yes,No';
                    if (($this->formData['has_pensioner'] ?? '') === 'Yes') {
                        $rules['formData.pensioner_details'] = 'required|string|max:255';
                    }
                } else {
                    $index = $this->activeMemberIndex - 1;
                    $member = $this->members[$index] ?? [];
                    if (($member['member_type'] ?? 'adult') === 'adult') {
                        $rules["members.{$index}.has_pan_card"] = 'required|in:Yes,No';
                        if (($member['has_pan_card'] ?? '') === 'Yes') {
                            $rules["members.{$index}.pan_name"] = ['required', 'string', 'max:255', 'regex:/^[\p{L}\s.\'\-]+$/u'];
                            $rules["members.{$index}.pan_no"] = ['required', 'regex:/^[A-Z]{3}[CPHFATBLJG][A-Z][0-9]{4}[A-Z]$/'];
                        }
                    }
                }
            } elseif ($section === 'other_docs') {
                if ($this->activeMemberIndex === 0) {
                    foreach ($this->formData['hof_kcc_cards'] ?? [] as $ki => $card) {
                        if (!empty($card['type'])) {
                            $rules["formData.hof_kcc_cards.{$ki}.id_no"] = 'required|string|max:100';
                            $rules["formData.hof_kcc_cards.{$ki}.date"] = 'nullable|date|before:today';
                            $rules["formData.hof_kcc_cards.{$ki}.issuing_authority"] = 'nullable|string|max:255';
                        }
                    }
                } else {
                    $index = $this->activeMemberIndex - 1;
                    $member = $this->members[$index] ?? [];
                    if (($member['member_type'] ?? 'adult') === 'adult') {
                        foreach ($member['kcc_cards'] ?? [] as $ki => $card) {
                            if (!empty($card['type'])) {
                                $rules["members.{$index}.kcc_cards.{$ki}.id_no"] = 'required|string|max:100';
                                $rules["members.{$index}.kcc_cards.{$ki}.date"] = 'nullable|date|before:today';
                                $rules["members.{$index}.kcc_cards.{$ki}.issuing_authority"] = 'nullable|string|max:255';
                            }
                        }
                    }
                }
            }
        } else {
            // Full Form validation on Submit (showConfirmation)
            // 1. HOF basic identity & validation rules
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
                'formData.has_pan_card' => 'required|in:Yes,No',
                'formData.hof_epic_no' => ['nullable', 'regex:/^[A-Z]{3}[0-9]{7}$/'],

                'formData.has_pucca_rooms' => 'required',
                'formData.owns_land' => 'required',
                'formData.owns_4_wheeler' => 'required',
                'formData.has_health_insurance' => 'required',

                'formData.pays_tax' => 'required',
                'formData.total_annual_income' => 'required|numeric|min:0',
                'formData.has_constitutional_post' => 'required|in:Yes,No',
                'formData.has_gst_reg' => 'required|in:Yes,No',
                'formData.has_pensioner' => 'required|in:Yes,No',
            ];

            if (($this->formData['has_health_insurance'] ?? '') === 'Yes') {
                $rules['formData.health_insurance_type'] = 'required|in:Government,Private';
            }

            if (!empty($this->formData['hof_epic_no'])) {
                $rules['formData.hof_assembly_constituency'] = 'required';
                $rules['formData.hof_part_no'] = 'required|string|max:100';
            }

            if (($this->formData['has_pan_card'] ?? '') === 'Yes') {
                $rules['formData.hof_pan_name'] = ['required', 'string', 'max:255', 'regex:/^[\p{L}\s.\'\-]+$/u'];
                $rules['formData.hof_pan_no'] = ['required', 'regex:/^[A-Z]{3}[CPHFATBLJG][A-Z][0-9]{4}[A-Z]$/'];
            }

            if (($this->formData['has_constitutional_post'] ?? '') === 'Yes') {
                $rules['formData.constitutional_post_details'] = 'required|string|max:255';
            }

            if (($this->formData['has_gst_reg'] ?? '') === 'Yes') {
                $rules['formData.gstin'] = 'required|string|max:100';
            }
            if (($this->formData['has_pensioner'] ?? '') === 'Yes') {
                $rules['formData.pensioner_details'] = 'required|string|max:255';
            }

            $category = $this->formData['category'] ?? '';
            if (in_array($category, ['SC', 'ST', 'OBC'])) {
                $rules['formData.caste_certificate_no'] = 'required|string|max:100';
            } elseif ($category == 'UR-EWS') {
                $rules['formData.ews_certificate_no'] = 'required|string|max:100';
            } elseif ($category == 'PVTG') {
                $rules['formData.pvtg_certificate_no'] = 'required|string|max:100';
            }

            if (($this->formData['owns_4_wheeler'] ?? '') === 'Yes') {
                $rules['formData.num_vehicles'] = 'required|integer|min:1';
                foreach ($this->formData['vehicles'] ?? [] as $vi => $vehicle) {
                    $rules["formData.vehicles.{$vi}.reg_no"] = ['required', 'string', 'regex:/^[A-Z]{2}[ -]?[0-9]{2}[ -]?[A-Z]{1,3}[ -]?[0-9]{4}$/i'];
                    $rules["formData.vehicles.{$vi}.model"] = 'required|string|max:100';
                }
            }

            // HOF cards validation
            foreach ($this->formData['hof_kcc_cards'] ?? [] as $ki => $card) {
                if (!empty($card['type'])) {
                    $rules["formData.hof_kcc_cards.{$ki}.id_no"] = 'required|string|max:100';
                    $rules["formData.hof_kcc_cards.{$ki}.date"] = 'nullable|date|before:today';
                    $rules["formData.hof_kcc_cards.{$ki}.issuing_authority"] = 'nullable|string|max:255';
                }
            }

            // 2. Validate all members rules
            foreach ($this->members as $index => $member) {
                $rules["members.{$index}.member_type"] = 'required|in:adult,child';
                $rules["members.{$index}.name"] = ['required', 'string', 'max:255', 'regex:/^[\p{L}\s.\'\-]+$/u'];
                $rules["members.{$index}.dob"] = 'required|date|before:today';
                $rules["members.{$index}.gender"] = 'required';
                $rules["members.{$index}.relation"] = 'required';

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
                    if (!empty($member['epic_no'])) {
                        $rules["members.{$index}.assembly_constituency"] = 'required';
                        $rules["members.{$index}.part_no"] = 'required|string|max:100';
                    }
                    $rules["members.{$index}.pan_no"] = ['nullable', 'regex:/^[A-Z]{3}[CPHFATBLJG][A-Z][0-9]{4}[A-Z]$/'];

                    $rules["members.{$index}.has_health_insurance"] = 'required';
                    if (($member['has_health_insurance'] ?? '') === 'Yes') {
                        $rules["members.{$index}.health_insurance_type"] = 'required|in:Government,Private';
                    }

                    if ($this->isMemberFemale25to60($index) || (($member['applying_for_ay'] ?? 'No') === 'Yes')) {
                        $rules["members.{$index}.bank_name"] = ['required', 'string', 'max:100', 'regex:/^[\p{L}\s.\'\-]+$/u'];
                        $rules["members.{$index}.acc_no"] = 'required|digits_between:9,18';
                        $rules["members.{$index}.ifsc"] = ['required', 'size:11', 'regex:/^[A-Z]{4}0[A-Z0-9]{6}$/'];
                    }

                    foreach ($member['kcc_cards'] ?? [] as $ki => $card) {
                        if (!empty($card['type'])) {
                            $rules["members.{$index}.kcc_cards.{$ki}.id_no"] = 'required|string|max:100';
                            $rules["members.{$index}.kcc_cards.{$ki}.date"] = 'nullable|date|before:today';
                            $rules["members.{$index}.kcc_cards.{$ki}.issuing_authority"] = 'nullable|string|max:255';
                        }
                    }
                }
            }
        }

        return $rules;
    }

    /**
     * Get validation messages for a section, or the full form if null.
     */
    protected function getValidationMessages(?string $section = null): array
    {
        $messages = [];

        if ($section !== null) {
            // Section-specific validation messages
            if ($section === 'family_identity') {
                if ($this->activeMemberIndex === 0) {
                    $messages['formData.hof_name.required'] = 'Head of Family name is required.';
                    $messages['formData.hof_name.regex'] = 'Name should contain letters only (no numbers/special characters).';
                    $messages['formData.hof_dob.required'] = 'HOF Date of Birth is required.';
                    $messages['formData.hof_dob.before'] = 'Date of Birth must be in the past.';
                    $messages['formData.hof_gender.required'] = 'HOF Gender is required.';
                    $messages['formData.contact_no.required'] = 'Contact number is required.';
                    $messages['formData.contact_no.digits'] = 'Contact must be exactly 10 digits (numbers only).';
                    $messages['formData.category.required'] = 'Category is required.';
                    $messages['formData.caste_certificate_no.required'] = 'Caste Certificate number is required for SC/ST/OBC.';
                    $messages['formData.ews_certificate_no.required'] = 'EWS Certificate number is required for General (EWS).';
                    $messages['formData.pvtg_certificate_no.required'] = 'PVTG Certificate number is required for PVTG.';
                    $messages['formData.district_id.required'] = 'District is required.';
                    $messages['formData.rural_urban.required'] = 'Rural/Urban is required.';
                    $messages['formData.blockurban.required'] = 'Block/Municipality is required.';
                    $messages['formData.gpward.required'] = 'GP/Ward is required.';
                    $messages['formData.village_town.required'] = 'Village/Town is required.';
                    $messages['formData.police_station.required'] = 'Police Station is required.';
                    $messages['formData.post_office.required'] = 'Post Office is required.';
                    $messages['formData.pincode.required'] = 'Pincode is required.';
                    $messages['formData.pincode.digits'] = 'Pincode must be exactly 6 digits (numbers only).';
                    $messages['formData.hof_aadhaar.required'] = 'HOF Aadhaar number is required.';
                    $messages['formData.hof_aadhaar.digits'] = 'Aadhaar must be exactly 12 digits (numbers only).';
                    $messages['formData.hof_bank_name.required'] = 'HOF Bank Name is required.';
                    $messages['formData.hof_bank_name.regex'] = 'Bank Name should contain letters only (no numbers).';
                    $messages['formData.hof_acc_no.required'] = 'HOF Account Number is required.';
                    $messages['formData.hof_acc_no.digits_between'] = 'Account Number must be 9 to 18 digits (numbers only).';
                    $messages['formData.hof_ifsc.required'] = 'HOF IFSC Code is required.';
                    $messages['formData.hof_ifsc.size'] = 'IFSC Code must be exactly 11 characters.';
                    $messages['formData.hof_ifsc.regex'] = 'IFSC format is invalid (e.g. SBIN0001234).';
                    $messages['formData.hof_epic_no.regex'] = 'Voter ID (EPIC) format is invalid (e.g. ABC1234567).';
                    $messages['formData.hof_assembly_constituency.required'] = 'HOF Assembly Constituency is required when Voter ID is entered.';
                    $messages['formData.hof_part_no.required'] = 'HOF Part Number of Electoral Roll is required when Voter ID is entered.';
                } else {
                    $index = $this->activeMemberIndex - 1;
                    $messages["members.{$index}.member_type.required"] = 'Member category (Adult/Child) is required.';
                    $messages["members.{$index}.name.required"] = 'Member name is required.';
                    $messages["members.{$index}.name.regex"] = 'Member name should contain letters only.';
                    $messages["members.{$index}.dob.required"] = 'Member DOB is required.';
                    $messages["members.{$index}.dob.before"] = 'Member Date of Birth must be in the past.';
                    $messages["members.{$index}.gender.required"] = 'Member Gender is required.';
                    $messages["members.{$index}.relation.required"] = 'Member Relation is required.';
                    $messages["members.{$index}.aadhaar.digits"] = 'Member Aadhaar must be 12 digits (numbers only).';
                    $messages["members.{$index}.epic_no.regex"] = 'Member Voter ID (EPIC) format is invalid (e.g. ABC1234567).';
                    $messages["members.{$index}.bank_name.required"] = 'Member bank name is required since they are applying for AY.';
                    $messages["members.{$index}.bank_name.regex"] = 'Bank Name should contain letters only (no numbers).';
                    $messages["members.{$index}.acc_no.required"] = 'Member bank account number is required since they are applying for AY.';
                    $messages["members.{$index}.acc_no.digits_between"] = 'Account Number must be 9 to 18 digits (numbers only).';
                    $messages["members.{$index}.ifsc.required"] = 'Member IFSC is required since they are applying for AY.';
                    $messages["members.{$index}.ifsc.size"] = 'Member IFSC must be exactly 11 characters.';
                    $messages["members.{$index}.ifsc.regex"] = 'Member IFSC format is invalid (e.g. SBIN0001234).';
                    $messages["members.{$index}.assembly_constituency.required"] = 'Member Assembly Constituency is required when Voter ID is entered.';
                    $messages["members.{$index}.part_no.required"] = 'Member Part Number of Electoral Roll is required when Voter ID is entered.';
                }
            } elseif ($section === 'ration_subsidy') {
                if ($this->activeMemberIndex === 0) {
                    $messages['formData.has_digital_ration_card.required'] = 'Ration card selection is required.';
                    $messages['formData.is_lifting_ration.required'] = 'Ration lifting status selection is required.';
                }
            } elseif ($section === 'assets') {
                if ($this->activeMemberIndex === 0) {
                    $messages['formData.has_pucca_rooms.required'] = 'House size selection is required.';
                    $messages['formData.owns_land.required'] = 'Land ownership selection is required.';
                    $messages['formData.owns_4_wheeler.required'] = '4-wheeler ownership selection is required.';
                    $messages['formData.has_health_insurance.required'] = 'Health insurance selection is required.';
                    $messages['formData.health_insurance_type.required'] = 'Health insurance type selection is required.';
                    $messages['formData.num_vehicles.required'] = 'Please enter number of vehicles.';
                    $messages['formData.num_vehicles.min'] = 'Number of vehicles must be at least 1.';

                    foreach ($this->formData['vehicles'] ?? [] as $vi => $vehicle) {
                        $messages["formData.vehicles.{$vi}.reg_no.required"] = 'Registration number for Vehicle ' . ($vi + 1) . ' is required.';
                        $messages["formData.vehicles.{$vi}.reg_no.regex"] = 'Registration format for Vehicle ' . ($vi + 1) . ' is invalid (e.g. WB-01-AB-1234).';
                        $messages["formData.vehicles.{$vi}.model.required"] = 'Model name for Vehicle ' . ($vi + 1) . ' is required.';
                    }
                } else {
                    $index = $this->activeMemberIndex - 1;
                    $messages["members.{$index}.has_health_insurance.required"] = 'Health insurance selection is required.';
                    $messages["members.{$index}.health_insurance_type.required"] = 'Health insurance type selection is required.';
                }
            } elseif ($section === 'income_profession') {
                if ($this->activeMemberIndex === 0) {
                    $messages['formData.pays_tax.required'] = 'Income Tax payment selection is required.';
                    $messages['formData.total_annual_income.required'] = 'Annual Income is required.';
                    $messages['formData.total_annual_income.numeric'] = 'Annual Income must be a number.';
                    $messages['formData.has_pan_card.required'] = 'Please select if HOF has a PAN Card.';
                    $messages['formData.hof_pan_name.required'] = 'Name on PAN Card is required.';
                    $messages['formData.hof_pan_name.regex'] = 'Name on PAN Card should contain letters only.';
                    $messages['formData.hof_pan_no.required'] = 'PAN Card Number is required.';
                    $messages['formData.hof_pan_no.regex'] = 'HOF PAN format is invalid (e.g. ABCDE1234F).';
                    $messages['formData.has_constitutional_post.required'] = 'Please select if HOF holds any constitutional post.';
                    $messages['formData.constitutional_post_details.required'] = 'Member No. who was holding the position is required.';
                    $messages['formData.has_gst_reg.required'] = 'Please select if HOF is registered under GST.';
                    $messages['formData.gstin.required'] = 'GSTIN is required.';
                    $messages['formData.has_pensioner.required'] = 'Please select if HOF is a government pensioner.';
                    $messages['formData.pensioner_details.required'] = 'Government pensioner details are required.';
                } else {
                    $index = $this->activeMemberIndex - 1;
                    $messages["members.{$index}.has_pan_card.required"] = 'Please select if member has a PAN Card.';
                    $messages["members.{$index}.pan_name.required"] = 'Member Name on PAN Card is required.';
                    $messages["members.{$index}.pan_name.regex"] = 'Member Name on PAN Card should contain letters only.';
                    $messages["members.{$index}.pan_no.required"] = 'Member PAN Card Number is required.';
                    $messages["members.{$index}.pan_no.regex"] = 'Member PAN format is invalid (e.g. ABCDE1234F).';
                }
            } elseif ($section === 'other_docs') {
                if ($this->activeMemberIndex === 0) {
                    foreach ($this->formData['hof_kcc_cards'] ?? [] as $ki => $card) {
                        $messages["formData.hof_kcc_cards.{$ki}.id_no.required"] = 'Card ID Number is required for HOF Card ' . ($ki + 1) . '.';
                        $messages["formData.hof_kcc_cards.{$ki}.date.before"] = 'Issue Date must be in the past for HOF Card ' . ($ki + 1) . '.';
                    }
                } else {
                    $index = $this->activeMemberIndex - 1;
                    foreach ($this->members[$index]['kcc_cards'] ?? [] as $ki => $card) {
                        $messages["members.{$index}.kcc_cards.{$ki}.id_no.required"] = 'Card ID Number is required for Member Card ' . ($ki + 1) . '.';
                        $messages["members.{$index}.kcc_cards.{$ki}.date.before"] = 'Issue Date must be in the past for Member Card ' . ($ki + 1) . '.';
                    }
                }
            }
        } else {
            // Full Form validation messages on Submit
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
                'formData.has_pan_card.required' => 'Please select if HOF has a PAN Card.',
                'formData.hof_pan_name.required' => 'Name on PAN Card is required.',
                'formData.hof_pan_name.regex' => 'Name on PAN Card should contain letters only.',
                'formData.hof_pan_no.required' => 'PAN Card Number is required.',
                'formData.hof_pan_no.regex' => 'PAN format is invalid (e.g. ABCDE1234F).',
                'formData.hof_epic_no.regex' => 'Voter ID (EPIC) format is invalid (e.g. ABC1234567).',
                'formData.hof_assembly_constituency.required' => 'HOF Assembly Constituency is required when Voter ID is entered.',
                'formData.hof_part_no.required' => 'HOF Part Number of Electoral Roll is required when Voter ID is entered.',

                'formData.has_pucca_rooms.required' => 'House size selection is required.',
                'formData.owns_land.required' => 'Land ownership selection is required.',
                'formData.owns_4_wheeler.required' => '4-wheeler ownership selection is required.',
                'formData.has_health_insurance.required' => 'Health insurance selection is required.',
                'formData.health_insurance_type.required' => 'Health insurance type selection is required.',
                'formData.num_vehicles.required' => 'Please enter number of vehicles.',
                'formData.num_vehicles.min' => 'Number of vehicles must be at least 1.',

                'formData.pays_tax.required' => 'Income Tax payment selection is required.',
                'formData.total_annual_income.required' => 'Annual Income is required.',
                'formData.total_annual_income.numeric' => 'Annual Income must be a number.',
                'formData.has_constitutional_post.required' => 'Please select if HOF holds any constitutional post.',
                'formData.constitutional_post_details.required' => 'Member No. who was holding the position is required.',
                'formData.has_gst_reg.required' => 'Please select if HOF is registered under GST.',
                'formData.gstin.required' => 'HOF GSTIN is required.',
                'formData.has_pensioner.required' => 'Please select if HOF is a government pensioner.',
                'formData.pensioner_details.required' => 'HOF Government pensioner details are required.',
            ];

            // HOF cards validation messages
            foreach ($this->formData['hof_kcc_cards'] ?? [] as $ki => $card) {
                $messages["formData.hof_kcc_cards.{$ki}.id_no.required"] = 'Card ID Number is required for HOF Card ' . ($ki + 1) . '.';
                $messages["formData.hof_kcc_cards.{$ki}.date.before"] = 'Issue Date must be in the past for HOF Card ' . ($ki + 1) . '.';
            }

            foreach ($this->members as $index => $member) {
                $messages["members.{$index}.member_type.required"] = 'Member #' . ($index + 1) . ' category is required.';
                $messages["members.{$index}.name.required"] = 'Member #' . ($index + 1) . ' name is required.';
                $messages["members.{$index}.name.regex"] = 'Member #' . ($index + 1) . ' name should contain letters only.';
                $messages["members.{$index}.dob.required"] = 'Member #' . ($index + 1) . ' DOB is required.';
                $messages["members.{$index}.dob.before"] = 'Member #' . ($index + 1) . ' Date of Birth must be in the past.';
                $messages["members.{$index}.gender.required"] = 'Member #' . ($index + 1) . ' Gender is required.';
                $messages["members.{$index}.relation.required"] = 'Member #' . ($index + 1) . ' Relation is required.';

                $messages["members.{$index}.aadhaar.digits"] = 'Member #' . ($index + 1) . ' Aadhaar must be 12 digits (numbers only).';
                $messages["members.{$index}.has_health_insurance.required"] = 'Member #' . ($index + 1) . ' health insurance selection is required.';
                $messages["members.{$index}.health_insurance_type.required"] = 'Member #' . ($index + 1) . ' health insurance type selection is required.';
                $messages["members.{$index}.epic_no.regex"] = 'Member #' . ($index + 1) . ' Voter ID (EPIC) format is invalid (e.g. ABC1234567).';
                $messages["members.{$index}.has_pan_card.required"] = 'Member #' . ($index + 1) . ' PAN Card selection is required.';
                $messages["members.{$index}.pan_name.required"] = 'Member #' . ($index + 1) . ' Name on PAN Card is required.';
                $messages["members.{$index}.pan_name.regex"] = 'Member #' . ($index + 1) . ' Name on PAN Card should contain letters only.';
                $messages["members.{$index}.pan_no.required"] = 'Member #' . ($index + 1) . ' PAN Card Number is required.';
                $messages["members.{$index}.pan_no.regex"] = 'Member #' . ($index + 1) . ' PAN format is invalid (e.g. ABCDE1234F).';

                $messages["members.{$index}.bank_name.required"] = 'Member #' . ($index + 1) . ' bank name is required since they are applying for AY.';
                $messages["members.{$index}.bank_name.regex"] = 'Bank Name should contain letters only (no numbers).';
                $messages["members.{$index}.acc_no.required"] = 'Member #' . ($index + 1) . ' account number is required since they are applying for AY.';
                $messages["members.{$index}.acc_no.digits_between"] = 'Account Number must be 9 to 18 digits (numbers only).';
                $messages["members.{$index}.ifsc.required"] = 'Member #' . ($index + 1) . ' bank account number is required since they are applying for AY.';
                $messages["members.{$index}.ifsc.size"] = 'Member #' . ($index + 1) . ' IFSC must be exactly 11 characters.';
                $messages["members.{$index}.ifsc.regex"] = 'Member #' . ($index + 1) . ' IFSC format is invalid (e.g. SBIN0001234).';
                $messages["members.{$index}.assembly_constituency.required"] = 'Member #' . ($index + 1) . ' Assembly Constituency is required when Voter ID is entered.';
                $messages["members.{$index}.part_no.required"] = 'Member #' . ($index + 1) . ' Part Number of Electoral Roll is required when Voter ID is entered.';

                foreach ($member['kcc_cards'] ?? [] as $ki => $card) {
                    $messages["members.{$index}.kcc_cards.{$ki}.id_no.required"] = 'Card ID Number is required for Member #' . ($index + 1) . ' Card ' . ($ki + 1) . '.';
                    $messages["members.{$index}.kcc_cards.{$ki}.date.before"] = 'Issue Date must be in the past for Member #' . ($index + 1) . ' Card ' . ($ki + 1) . '.';
                }
            }
        }

        return $messages;
    }

    public function validateSection($section)
    {
        $this->successMessage = null;
        $this->errorMessage = null;

        $rules = $this->getValidationRules($section);
        $messages = $this->getValidationMessages($section);

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

        // 2. Validate full form rules for HOF and members
        $rules = $this->getValidationRules(null);
        $messages = $this->getValidationMessages(null);

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
                        if (in_array($field, ['member_type', 'name', 'dob', 'gender', 'relation', 'aadhaar', 'bank_name', 'acc_no', 'ifsc', 'epic_no', 'assembly_constituency', 'part_no', 'applying_for_ay'])) {
                            $this->activeSection = 'family_identity';
                        } elseif (in_array($field, ['has_digital_ration_card', 'ration_card_no', 'ration_card_type'])) {
                            $this->activeSection = 'ration_subsidy';
                        } elseif (in_array($field, ['health_insurance_type', 'health_insurance_premium', 'health_insurance_sum_assured'])) {
                            $this->activeSection = 'assets';
                        } elseif (in_array($field, ['has_pan_card', 'pan_name', 'pan_no', 'employment_nature', 'literate_status', 'highest_qualification'])) {
                            $this->activeSection = 'income_profession';
                        } elseif (in_array($field, ['caa_status', 'caa_app_no', 'caa_cert_no', 'kcc_type', 'kcc_id_no', 'kcc_date', 'kcc_issuing_authority', 'sir_status', 'sir_case_details', 'kcc_cards'])) {
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
                        'hof_assembly_constituency',
                        'hof_part_no',
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
                        'has_pan_card',
                        'hof_pan_name',
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
                        $this->activeSection = 'ration_subsidy';
                    } elseif (in_array($field, $assetsFields)) {
                        $this->activeSection = 'assets';
                    } elseif (in_array($field, $incomeProfessionFields)) {
                        $this->activeSection = 'income_profession';
                    } elseif (in_array($field, ['caa_status', 'caa_app_no', 'caa_cert_no', 'kcc_type', 'kcc_id_no', 'kcc_date', 'kcc_issuing_authority', 'sir_status', 'sir_case_details']) || str_starts_with($field, 'hof_kcc_cards')) {
                        $this->activeSection = 'other_docs';
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
                'created_by_dist_code' => $this->createdByDistCode,
                'created_by_local_body_code' => $this->createdByLocalBodyCode,
            ];

            if ($this->familyId) {
                DB::connection('pgsql_annapurna')->table('dbt_apy.families')->where('id', $this->familyId)->update($familyData);
                $familyId = $this->familyId;
            } else {
                $familyData['created_at'] = now();
                $familyId = DB::connection('pgsql_annapurna')->table('dbt_apy.families')->insertGetId($familyData, 'id');
                $this->familyId = $familyId;
            }

            // 6. Get or Create HOF in dbt_apy.family_members
            $hofMemberData = [
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
                'assembly_constituency_no' => $this->formData['hof_assembly_constituency'] ?? null,
                'part_no' => $this->formData['hof_part_no'] ?? null,
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
                'pan_no' => (($this->formData['has_pan_card'] ?? '') === 'Yes') ? ($this->formData['hof_pan_no'] ?? null) : null,
                'pan_name' => (($this->formData['has_pan_card'] ?? '') === 'Yes') ? ($this->formData['hof_pan_name'] ?? null) : null,
                'holds_constitutional_post' => $hasConstitutionalPost,
                'constitutional_post_member_no' => $constitutionalPostDetails,
                'is_registered_gst' => $hasGstReg,
                'gstin' => $gstin,
                'is_child' => false,
                'is_govt_pensioner' => $hasPensioner,
                'govt_pensioner_member_no' => $pensionerDetails,
                'relation_with_head_of_family' => 'Self',
                'applying_for_annapurna_bhandar' => $this->isHofFemale25to60() || (($this->formData['hof_applying_for_ay'] ?? '') === 'Yes'),
                'has_pan_card' => (($this->formData['has_pan_card'] ?? '') === 'Yes'),
                'has_three_pucca_rooms' => (($this->formData['has_pucca_rooms'] ?? '') === 'Yes'),
                'owns_land' => $ownsLand,
                'landholding_size_decimals' => $landSizeDecimals,
                'lgd_district_code' => $lgdDistrictCode,
                'lgd_block_mc_code' => $lgdBlockMcCode,
                'lgd_gp_ward_code' => $lgdGpWardCode,
                'is_deleted' => 0,
                'deleted_at' => null,
                'created_by_dist_code' => $this->createdByDistCode,
                'created_by_local_body_code' => $this->createdByLocalBodyCode,
            ];

            $hofMember = DB::connection('pgsql_annapurna')->table('dbt_apy.family_members')
                ->where('family_id', $familyId)
                ->where('is_hof', true)
                ->where('is_deleted', 0)
                ->first();

            if ($hofMember) {
                $hofMemberId = $hofMember->id;
                DB::connection('pgsql_annapurna')->table('dbt_apy.family_members')
                    ->where('id', $hofMemberId)
                    ->update($hofMemberData);
            } else {
                $hofMemberId = DB::connection('pgsql_annapurna')->table('dbt_apy.family_members')
                    ->insertGetId($hofMemberData, 'id');
            }

            // HOF employment nature
            $this->syncMemberEmploymentNatures($hofMemberId, (array) ($this->formData['hof_employment_nature'] ?? []), $lgdDistrictCode);

            // HOF govt schemes
            $hofBenefits = (($this->formData['hof_has_dbt_benefits'] ?? 'No') === 'Yes') ? ($this->formData['hof_dbt_benefits'] ?? []) : [];
            $this->syncMemberGovtSchemes($hofMemberId, $hofBenefits, $lgdDistrictCode);

            // HOF credit card / other id
            $this->syncMemberOtherIds($hofMemberId, $this->formData['hof_kcc_cards'] ?? [], $lgdDistrictCode);

            // Identify non-HOF members in DB that need to be soft-deleted
            $currentThreadMemberIds = array_filter(array_column($this->members, 'id'));
            $existingDBCallMemberIds = DB::connection('pgsql_annapurna')->table('dbt_apy.family_members')
                ->where('family_id', $familyId)
                ->where('is_hof', false)
                ->where('is_deleted', 0)
                ->pluck('id')
                ->toArray();

            $membersToDelete = array_diff($existingDBCallMemberIds, $currentThreadMemberIds);
            if (! empty($membersToDelete)) {
                DB::connection('pgsql_annapurna')->table('dbt_apy.member_employment_natures')->whereIn('family_member_id', $membersToDelete)->update(['is_deleted' => 1, 'deleted_at' => now()]);
                DB::connection('pgsql_annapurna')->table('dbt_apy.member_govt_schemes')->whereIn('family_member_id', $membersToDelete)->update(['is_deleted' => 1, 'deleted_at' => now()]);
                DB::connection('pgsql_annapurna')->table('dbt_apy.member_other_ids')->whereIn('family_member_id', $membersToDelete)->update(['is_deleted' => 1, 'deleted_at' => now()]);
                DB::connection('pgsql_annapurna')->table('dbt_apy.family_members')->whereIn('id', $membersToDelete)->update(['is_deleted' => 1, 'deleted_at' => now()]);
            }

            // 7. Insert or update other family members
            foreach ($this->members as $index => $member) {
                $isChild = (($member['member_type'] ?? 'adult') === 'child');

                $mHasDigitalRationCard = ! $isChild && (($member['has_digital_ration_card'] ?? '') === 'Yes');
                $mRationCardNo = $mHasDigitalRationCard ? ($member['ration_card_no'] ?? null) : null;
                $mRationCardType = $mHasDigitalRationCard ? ($member['ration_card_type'] ?? null) : null;

                $mApplyingForAY = ! $isChild && ($this->isMemberFemale25to60($index) || (($member['applying_for_ay'] ?? 'No') === 'Yes'));
                $mBankName = $mApplyingForAY ? ($member['bank_name'] ?? null) : null;
                $mAccNo = $mApplyingForAY ? ($member['acc_no'] ?? null) : null;
                $mIfsc = $mApplyingForAY ? ($member['ifsc'] ?? null) : null;

                $mCaaStatus = $isChild ? 'Not Applicable' : ($member['caa_status'] ?? 'Not Applicable');
                $mCaaAppNo = ! $isChild && $mCaaStatus === 'Applied' ? ($member['caa_app_no'] ?? null) : null;
                $mCaaCertNo = ! $isChild && $mCaaStatus === 'Issued' ? ($member['caa_cert_no'] ?? null) : null;

                $mSirStatus = $isChild ? 'Not Applicable' : ($member['sir_status'] ?? 'Not Applicable');
                $mSirCaseDetails = ! $isChild && $mSirStatus === 'Yes' ? ($member['sir_case_details'] ?? null) : null;

                $mHealthInsuranceType = $isChild ? 'No' : ($member['health_insurance_type'] ?? 'No');
                $mHasHealthInsurance = ! $isChild && ($mHealthInsuranceType !== 'No');
                $mHealthInsurancePremium = ($mHasHealthInsurance && !empty($member['health_insurance_premium'])) ? (float) $member['health_insurance_premium'] : null;
                $mHealthInsuranceSumAssured = ($mHasHealthInsurance && !empty($member['health_insurance_sum_assured'])) ? (float) $member['health_insurance_sum_assured'] : null;

                $memberData = [
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
                    'assembly_constituency_no' => $isChild ? null : (!empty($member['assembly_constituency']) ? $member['assembly_constituency'] : null),
                    'part_no' => $isChild ? null : (!empty($member['part_no']) ? $member['part_no'] : null),
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
                    'pan_no' => ($isChild || (($member['has_pan_card'] ?? '') !== 'Yes')) ? null : ($member['pan_no'] ?? null),
                    'pan_name' => ($isChild || (($member['has_pan_card'] ?? '') !== 'Yes')) ? null : ($member['pan_name'] ?? null),
                    'holds_constitutional_post' => false,
                    'is_registered_gst' => false,
                    'is_child' => $isChild,
                    'is_govt_pensioner' => false,
                    'relation_with_head_of_family' => !empty($member['relation']) ? $member['relation'] : null,
                    'applying_for_annapurna_bhandar' => $mApplyingForAY,
                    'has_pan_card' => ! $isChild && (($member['has_pan_card'] ?? '') === 'Yes'),
                    'lgd_district_code' => $lgdDistrictCode,
                    'lgd_block_mc_code' => $lgdBlockMcCode,
                    'lgd_gp_ward_code' => $lgdGpWardCode,
                    'school_grade' => $isChild ? (!empty($member['school_grade']) ? $member['school_grade'] : null) : null,
                    'school_name' => $isChild ? (!empty($member['school_name']) ? $member['school_name'] : null) : null,
                    'school_type' => $isChild ? (!empty($member['school_type']) ? $member['school_type'] : null) : null,
                    'school_type_other' => $isChild ? (!empty($member['school_type_other']) ? $member['school_type_other'] : null) : null,
                    'vaccination_card_id' => ($isChild && (($member['vaccination_status'] ?? '') === 'Yes' || ($member['vaccination_status'] ?? '') === 'Partial')) ? ($member['vaccination_card_id'] ?? null) : null,
                    'vaccination_status' => $isChild ? (!empty($member['vaccination_status']) ? $member['vaccination_status'] : null) : null,
                    'vaccination_skip_reason_or_date' => ($isChild && (($member['vaccination_status'] ?? '') === 'No' || ($member['vaccination_status'] ?? '') === 'Partial')) ? ($member['vaccination_skip_reason_or_date'] ?? null) : null,
                    'is_deleted' => 0,
                    'deleted_at' => null,
                    'created_by_dist_code' => $this->createdByDistCode,
                    'created_by_local_body_code' => $this->createdByLocalBodyCode,
                ];

                if (!empty($member['id'])) {
                    $memberId = $member['id'];
                    DB::connection('pgsql_annapurna')->table('dbt_apy.family_members')
                        ->where('id', $memberId)
                        ->update($memberData);
                } else {
                    $memberId = DB::connection('pgsql_annapurna')->table('dbt_apy.family_members')
                        ->insertGetId($memberData, 'id');
                    $this->members[$index]['id'] = $memberId;
                }

                // Member employment nature
                $memberEmployment = (! $isChild && ! empty($member['employment_nature'])) ? [$member['employment_nature']] : [];
                $this->syncMemberEmploymentNatures($memberId, $memberEmployment, $lgdDistrictCode);

                // Member govt schemes
                $memberBenefits = (! $isChild && ($member['has_dbt_benefits'] ?? 'No') === 'Yes') ? ($member['dbt_benefits'] ?? []) : [];
                $this->syncMemberGovtSchemes($memberId, $memberBenefits, $lgdDistrictCode);

                // Member credit card / other id
                $memberKcc = (! $isChild && ! empty($member['kcc_cards'])) ? $member['kcc_cards'] : [];
                $this->syncMemberOtherIds($memberId, $memberKcc, $lgdDistrictCode);
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
                'created_by_dist_code' => $this->createdByDistCode,
                'created_by_local_body_code' => $this->createdByLocalBodyCode,
            ];

            if ($this->familyId) {
                DB::connection('pgsql_annapurna')->table('dbt_apy.families')->where('id', $this->familyId)->update($familyData);
                $familyId = $this->familyId;
            } else {
                $familyData['created_at'] = now();
                $familyId = DB::connection('pgsql_annapurna')->table('dbt_apy.families')->insertGetId($familyData, 'id');
                $this->familyId = $familyId;
            }

            // 6. Get or Create HOF in dbt_apy.family_members
            $hofMemberData = [
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
                'part_no' => $this->formData['hof_part_no'] ?? null,
                'assembly_constituency_no' => $this->formData['hof_assembly_constituency'] ?? null,
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
                'pan_no' => (($this->formData['has_pan_card'] ?? '') === 'Yes') ? ($this->formData['hof_pan_no'] ?? null) : null,
                'pan_name' => (($this->formData['has_pan_card'] ?? '') === 'Yes') ? ($this->formData['hof_pan_name'] ?? null) : null,
                'holds_constitutional_post' => $hasConstitutionalPost,
                'constitutional_post_member_no' => $constitutionalPostDetails,
                'is_registered_gst' => $hasGstReg,
                'gstin' => $gstin,
                'is_child' => false,
                'is_govt_pensioner' => $hasPensioner,
                'govt_pensioner_member_no' => $pensionerDetails,
                'relation_with_head_of_family' => 'Self',
                'applying_for_annapurna_bhandar' => $this->isHofFemale25to60() || (($this->formData['hof_applying_for_ay'] ?? '') === 'Yes'),
                'has_pan_card' => (($this->formData['has_pan_card'] ?? '') === 'Yes'),
                'has_three_pucca_rooms' => (($this->formData['has_pucca_rooms'] ?? '') === 'Yes'),
                'owns_land' => $ownsLand,
                'landholding_size_decimals' => $landSizeDecimals,
                'lgd_district_code' => $lgdDistrictCode,
                'lgd_block_mc_code' => $lgdBlockMcCode,
                'lgd_gp_ward_code' => $lgdGpWardCode,
                'is_deleted' => 0,
                'deleted_at' => null,
                'created_by_dist_code' => $this->createdByDistCode,
                'created_by_local_body_code' => $this->createdByLocalBodyCode,
            ];

            $hofMember = DB::connection('pgsql_annapurna')->table('dbt_apy.family_members')
                ->where('family_id', $familyId)
                ->where('is_hof', true)
                ->where('is_deleted', 0)
                ->first();

            if ($hofMember) {
                $hofMemberId = $hofMember->id;
                DB::connection('pgsql_annapurna')->table('dbt_apy.family_members')
                    ->where('id', $hofMemberId)
                    ->update($hofMemberData);
            } else {
                $hofMemberId = DB::connection('pgsql_annapurna')->table('dbt_apy.family_members')
                    ->insertGetId($hofMemberData, 'id');
            }

            // HOF employment nature
            $this->syncMemberEmploymentNatures($hofMemberId, (array) ($this->formData['hof_employment_nature'] ?? []), $lgdDistrictCode);

            // HOF govt schemes
            $hofBenefits = (($this->formData['hof_has_dbt_benefits'] ?? 'No') === 'Yes') ? ($this->formData['hof_dbt_benefits'] ?? []) : [];
            $this->syncMemberGovtSchemes($hofMemberId, $hofBenefits, $lgdDistrictCode);

            // HOF credit card / other id
            $this->syncMemberOtherIds($hofMemberId, $this->formData['hof_kcc_cards'] ?? [], $lgdDistrictCode);

            // Identify non-HOF members in DB that need to be soft-deleted
            $currentThreadMemberIds = array_filter(array_column($this->members, 'id'));
            $existingDBCallMemberIds = DB::connection('pgsql_annapurna')->table('dbt_apy.family_members')
                ->where('family_id', $familyId)
                ->where('is_hof', false)
                ->where('is_deleted', 0)
                ->pluck('id')
                ->toArray();

            $membersToDelete = array_diff($existingDBCallMemberIds, $currentThreadMemberIds);
            if (! empty($membersToDelete)) {
                DB::connection('pgsql_annapurna')->table('dbt_apy.member_employment_natures')->whereIn('family_member_id', $membersToDelete)->update(['is_deleted' => 1, 'deleted_at' => now()]);
                DB::connection('pgsql_annapurna')->table('dbt_apy.member_govt_schemes')->whereIn('family_member_id', $membersToDelete)->update(['is_deleted' => 1, 'deleted_at' => now()]);
                DB::connection('pgsql_annapurna')->table('dbt_apy.member_other_ids')->whereIn('family_member_id', $membersToDelete)->update(['is_deleted' => 1, 'deleted_at' => now()]);
                DB::connection('pgsql_annapurna')->table('dbt_apy.family_members')->whereIn('id', $membersToDelete)->update(['is_deleted' => 1, 'deleted_at' => now()]);
            }

            // 7. Insert or update other family members
            foreach ($this->members as $index => $member) {
                $isChild = (($member['member_type'] ?? 'adult') === 'child');

                $mHasDigitalRationCard = ! $isChild && (($member['has_digital_ration_card'] ?? '') === 'Yes');
                $mRationCardNo = $mHasDigitalRationCard ? ($member['ration_card_no'] ?? null) : null;
                $mRationCardType = $mHasDigitalRationCard ? ($member['ration_card_type'] ?? null) : null;

                $mApplyingForAY = ! $isChild && ($this->isMemberFemale25to60($index) || (($member['applying_for_ay'] ?? 'No') === 'Yes'));
                $mBankName = $mApplyingForAY ? ($member['bank_name'] ?? null) : null;
                $mAccNo = $mApplyingForAY ? ($member['acc_no'] ?? null) : null;
                $mIfsc = $mApplyingForAY ? ($member['ifsc'] ?? null) : null;

                $mCaaStatus = $isChild ? 'Not Applicable' : ($member['caa_status'] ?? 'Not Applicable');
                $mCaaAppNo = ! $isChild && $mCaaStatus === 'Applied' ? ($member['caa_app_no'] ?? null) : null;
                $mCaaCertNo = ! $isChild && $mCaaStatus === 'Issued' ? ($member['caa_cert_no'] ?? null) : null;

                $mSirStatus = $isChild ? 'Not Applicable' : ($member['sir_status'] ?? 'Not Applicable');
                $mSirCaseDetails = ! $isChild && $mSirStatus === 'Yes' ? ($member['sir_case_details'] ?? null) : null;

                $mHealthInsuranceType = $isChild ? 'No' : ($member['health_insurance_type'] ?? 'No');
                $mHasHealthInsurance = ! $isChild && ($mHealthInsuranceType !== 'No');
                $mHealthInsurancePremium = ($mHasHealthInsurance && !empty($member['health_insurance_premium'])) ? (float) $member['health_insurance_premium'] : null;
                $mHealthInsuranceSumAssured = ($mHasHealthInsurance && !empty($member['health_insurance_sum_assured'])) ? (float) $member['health_insurance_sum_assured'] : null;

                $memberData = [
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
                    'assembly_constituency_no' => $isChild ? null : (!empty($member['assembly_constituency']) ? $member['assembly_constituency'] : null),
                    'part_no' => $isChild ? null : (!empty($member['part_no']) ? $member['part_no'] : null),
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
                    'pan_no' => ($isChild || (($member['has_pan_card'] ?? '') !== 'Yes')) ? null : ($member['pan_no'] ?? null),
                    'pan_name' => ($isChild || (($member['has_pan_card'] ?? '') !== 'Yes')) ? null : ($member['pan_name'] ?? null),
                    'holds_constitutional_post' => false,
                    'is_registered_gst' => false,
                    'is_child' => $isChild,
                    'is_govt_pensioner' => false,
                    'relation_with_head_of_family' => !empty($member['relation']) ? $member['relation'] : null,
                    'applying_for_annapurna_bhandar' => $mApplyingForAY,
                    'has_pan_card' => ! $isChild && (($member['has_pan_card'] ?? '') === 'Yes'),
                    'lgd_district_code' => $lgdDistrictCode,
                    'lgd_block_mc_code' => $lgdBlockMcCode,
                    'lgd_gp_ward_code' => $lgdGpWardCode,
                    'school_grade' => $isChild ? (!empty($member['school_grade']) ? $member['school_grade'] : null) : null,
                    'school_name' => $isChild ? (!empty($member['school_name']) ? $member['school_name'] : null) : null,
                    'school_type' => $isChild ? (!empty($member['school_type']) ? $member['school_type'] : null) : null,
                    'school_type_other' => $isChild ? (!empty($member['school_type_other']) ? $member['school_type_other'] : null) : null,
                    'vaccination_card_id' => ($isChild && (($member['vaccination_status'] ?? '') === 'Yes' || ($member['vaccination_status'] ?? '') === 'Partial')) ? ($member['vaccination_card_id'] ?? null) : null,
                    'vaccination_status' => $isChild ? (!empty($member['vaccination_status']) ? $member['vaccination_status'] : null) : null,
                    'vaccination_skip_reason_or_date' => ($isChild && (($member['vaccination_status'] ?? '') === 'No' || ($member['vaccination_status'] ?? '') === 'Partial')) ? ($member['vaccination_skip_reason_or_date'] ?? null) : null,
                    'is_deleted' => 0,
                    'deleted_at' => null,
                    'created_by_dist_code' => $this->createdByDistCode,
                    'created_by_local_body_code' => $this->createdByLocalBodyCode,
                ];

                if (!empty($member['id'])) {
                    $memberId = $member['id'];
                    DB::connection('pgsql_annapurna')->table('dbt_apy.family_members')
                        ->where('id', $memberId)
                        ->update($memberData);
                } else {
                    $memberId = DB::connection('pgsql_annapurna')->table('dbt_apy.family_members')
                        ->insertGetId($memberData, 'id');
                    $this->members[$index]['id'] = $memberId;
                }

                // Member employment nature
                $memberEmployment = (! $isChild && ! empty($member['employment_nature'])) ? [$member['employment_nature']] : [];
                $this->syncMemberEmploymentNatures($memberId, $memberEmployment, $lgdDistrictCode);

                // Member govt schemes
                $memberBenefits = (! $isChild && ($member['has_dbt_benefits'] ?? 'No') === 'Yes') ? ($member['dbt_benefits'] ?? []) : [];
                $this->syncMemberGovtSchemes($memberId, $memberBenefits, $lgdDistrictCode);

                // Member credit card / other id
                $memberKcc = (! $isChild && ! empty($member['kcc_cards'])) ? $member['kcc_cards'] : [];
                $this->syncMemberOtherIds($memberId, $memberKcc, $lgdDistrictCode);
            }

            DB::connection('pgsql_annapurna')->commit();

            // Update session data
            session([
                'annapurna_form_data' => $this->formData,
                'annapurna_members' => $this->members,
                'annapurna_family_id' => $this->familyId,
                'annapurna_app_id' => $this->appId,
            ]);

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

    public function calculateAdultLiteracyCounts()
    {
        $literate = 0;
        $illiterate = 0;

        // Check HOF
        $hofStatus = $this->formData['hof_literate_status'] ?? '';
        if ($hofStatus === 'Literate') {
            $literate++;
        } elseif ($hofStatus === 'Illiterate') {
            $illiterate++;
        }

        // Check members
        foreach ($this->members as $member) {
            $type = $member['member_type'] ?? 'adult';
            if ($type === 'adult') {
                $status = $member['literate_status'] ?? '';
                if ($status === 'Literate') {
                    $literate++;
                } elseif ($status === 'Illiterate') {
                    $illiterate++;
                }
            }
        }

        $this->formData['num_literate_adults'] = $literate;
        $this->formData['num_illiterate_adults'] = $illiterate;
    }

    private function syncMemberEmploymentNatures($memberId, array $employmentNatures, $lgdDistrictCode)
    {
        $activeTypes = [];
        foreach ($employmentNatures as $nature) {
            if (! empty($nature)) {
                $activeTypes[] = $nature;
            }
        }
        $activeTypes = array_unique($activeTypes);

        $existing = DB::connection('pgsql_annapurna')->table('dbt_apy.member_employment_natures')
            ->where('family_member_id', $memberId)
            ->get()
            ->keyBy('employment_type');

        // Soft delete those in DB but not active in input
        $toDelete = $existing->keys()->diff($activeTypes);
        if ($toDelete->isNotEmpty()) {
            DB::connection('pgsql_annapurna')->table('dbt_apy.member_employment_natures')
                ->where('family_member_id', $memberId)
                ->whereIn('employment_type', $toDelete)
                ->update(['is_deleted' => 1, 'deleted_at' => now()]);
        }

        // Restore or Insert active ones
        foreach ($activeTypes as $type) {
            if ($existing->has($type)) {
                $record = $existing->get($type);
                if ($record->is_deleted == 1 || (int)$record->lgd_district_code !== (int)$lgdDistrictCode) {
                    DB::connection('pgsql_annapurna')->table('dbt_apy.member_employment_natures')
                        ->where('id', $record->id)
                        ->update([
                            'is_deleted' => 0,
                            'deleted_at' => null,
                            'lgd_district_code' => $lgdDistrictCode,
                        ]);
                }
            } else {
                DB::connection('pgsql_annapurna')->table('dbt_apy.member_employment_natures')->insert([
                    'family_member_id' => $memberId,
                    'employment_type' => $type,
                    'lgd_district_code' => $lgdDistrictCode,
                    'is_deleted' => 0,
                ]);
            }
        }
    }

    private function syncMemberGovtSchemes($memberId, array $dbtBenefits, $lgdDistrictCode)
    {
        $activeBenefits = [];
        foreach ($dbtBenefits as $benefit) {
            if (! empty($benefit['scheme_name'])) {
                $activeBenefits[$benefit['scheme_name']] = (bool) ($benefit['opt_out'] ?? false);
            }
        }

        $existing = DB::connection('pgsql_annapurna')->table('dbt_apy.member_govt_schemes')
            ->where('family_member_id', $memberId)
            ->get()
            ->keyBy('scheme_name');

        // Soft delete those in DB but not active in input
        $toDelete = array_diff($existing->keys()->toArray(), array_keys($activeBenefits));
        if (! empty($toDelete)) {
            DB::connection('pgsql_annapurna')->table('dbt_apy.member_govt_schemes')
                ->where('family_member_id', $memberId)
                ->whereIn('scheme_name', $toDelete)
                ->update(['is_deleted' => 1, 'deleted_at' => now()]);
        }

        // Restore, Update or Insert active ones
        foreach ($activeBenefits as $schemeName => $optOut) {
            if ($existing->has($schemeName)) {
                $record = $existing->get($schemeName);
                if ($record->is_deleted == 1 || (bool)$record->opt_out !== (bool)$optOut || (int)$record->lgd_district_code !== (int)$lgdDistrictCode) {
                    DB::connection('pgsql_annapurna')->table('dbt_apy.member_govt_schemes')
                        ->where('id', $record->id)
                        ->update([
                            'opt_out' => $optOut,
                            'is_deleted' => 0,
                            'deleted_at' => null,
                            'lgd_district_code' => $lgdDistrictCode,
                        ]);
                }
            } else {
                DB::connection('pgsql_annapurna')->table('dbt_apy.member_govt_schemes')->insert([
                    'family_member_id' => $memberId,
                    'scheme_name' => $schemeName,
                    'opt_out' => $optOut,
                    'lgd_district_code' => $lgdDistrictCode,
                    'is_deleted' => 0,
                ]);
            }
        }
    }

    private function syncMemberOtherIds($memberId, array $kccCards, $lgdDistrictCode)
    {
        $activeCards = [];
        foreach ($kccCards as $card) {
            if (! empty($card['type']) && $card['type'] !== 'None') {
                $activeCards[$card['type']] = $card['date'] ?? '';
            }
        }

        $existing = DB::connection('pgsql_annapurna')->table('dbt_apy.member_other_ids')
            ->where('family_member_id', $memberId)
            ->get()
            ->keyBy('id_type');

        // Soft delete those in DB but not active in input
        $toDelete = array_diff($existing->keys()->toArray(), array_keys($activeCards));
        if (! empty($toDelete)) {
            DB::connection('pgsql_annapurna')->table('dbt_apy.member_other_ids')
                ->where('family_member_id', $memberId)
                ->whereIn('id_type', $toDelete)
                ->update(['is_deleted' => 1, 'deleted_at' => now()]);
        }

        // Restore, Update or Insert active ones
        foreach ($activeCards as $idType => $issueDate) {
            if ($existing->has($idType)) {
                $record = $existing->get($idType);
                if ($record->is_deleted == 1 || (string)$record->issue_date !== (string)$issueDate || (int)$record->lgd_district_code !== (int)$lgdDistrictCode) {
                    DB::connection('pgsql_annapurna')->table('dbt_apy.member_other_ids')
                        ->where('id', $record->id)
                        ->update([
                            'issue_date' => $issueDate,
                            'is_deleted' => 0,
                            'deleted_at' => null,
                            'lgd_district_code' => $lgdDistrictCode,
                        ]);
                }
            } else {
                DB::connection('pgsql_annapurna')->table('dbt_apy.member_other_ids')->insert([
                    'family_member_id' => $memberId,
                    'id_type' => $idType,
                    'issue_date' => $issueDate,
                    'lgd_district_code' => $lgdDistrictCode,
                    'is_deleted' => 0,
                ]);
            }
        }
    }

    public function render()
    {
        \Illuminate\Support\Facades\Log::info('AnnapurnaYojanaForm rendering', [
            'district_id' => $this->formData['district_id'] ?? null,
            'activeSection' => $this->activeSection,
        ]);
        return view('livewire.annapurna-yojana-form');
    }
}
