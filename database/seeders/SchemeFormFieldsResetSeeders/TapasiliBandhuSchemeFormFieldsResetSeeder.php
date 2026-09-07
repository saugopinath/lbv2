<?php

namespace Database\Seeders\SchemeFormFieldsResetSeeders;

use App\Helpers\SchemewiseStoreDataJsonHelper;
use App\Models\Scheme;
use App\Models\SchemeAttachedDocMappings;
use App\Models\SchemeFinalSubmitCheck;
use App\Models\SchemeTabBasefield;
use App\Models\SchemeTabFormField;
use App\Models\SchemeTabLayout;
use App\Models\SchemeTabMapping;
use App\Models\SectionLevelMaster;
use App\Models\SelfDeclerationBasefield;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class TapasiliBandhuSchemeFormFieldsResetSeeder extends Seeder
{
    public function run(): void
    {
        $scheme = Scheme::where('short_name', 'bandhu')
            ->orWhere('name', 'LIKE', '%BANDHU%')
            ->first() ?? Scheme::find(3);

        if (!$scheme) {
            $this->command?->error('Tapasili Bandhu Scheme not found!');
            return;
        }

        $schemeId = (int) $scheme->id;

        DB::transaction(function () use ($schemeId) {
            // 1. Purge existing scheme data
            SchemeTabMapping::where('scheme_id', $schemeId)->delete();
            SchemeTabFormField::where('scheme_id', $schemeId)->delete();
            SchemeAttachedDocMappings::where('scheme_id', $schemeId)->delete();
            SelfDeclerationBasefield::where('scheme_id', $schemeId)->delete();
            SchemeTabLayout::where('scheme_id', $schemeId)->delete();
            SchemeFinalSubmitCheck::where('scheme_id', $schemeId)->delete();

            $jsonPath = "final_schemes_formdata/scheme_{$schemeId}.json";
            if (Storage::disk('local')->exists($jsonPath)) {
                Storage::disk('local')->delete($jsonPath);
            }

            $bladeDir = resource_path("views/schemes/scheme_{$schemeId}");
            if (File::exists($bladeDir)) {
                File::deleteDirectory($bladeDir);
            }

            // 2. Ensure Sections exist in SectionLevelMaster
            $sectionsData = [
                'aadhaar_sec' => [
                    'name' => '',
                    'short' => 'aadhaar_sec',
                    'tab_code' => 105,
                ],
                'pension_from_sec' => [
                    'name' => 'Presently, I am receiving following pension(s) from:',
                    'short' => 'pension_from_sec',
                    'tab_code' => 105,
                ],
                'nominee_sec' => [
                    'name' => 'In the event of my death, I hereby nominate (Please mention Name, Address & Relationship)',
                    'short' => 'nominee_sec',
                    'tab_code' => 105,
                ],
                'social_sec' => [
                    'name' => 'Presently, I am receiving the following social Security Pension/s (Please tick):',
                    'short' => 'social_sec',
                    'tab_code' => 105,
                ],
            ];

            $sectionIds = [];
            foreach ($sectionsData as $key => $sdata) {
                $sec = SectionLevelMaster::updateOrCreate(
                    [
                        'scheme_id' => $schemeId,
                        'tab_code' => $sdata['tab_code'],
                        'section_level_short_name' => $sdata['short'],
                    ],
                    [
                        'scheme_id' => $schemeId,
                        'section_level_name' => $sdata['name'],
                        'section_level_short_name' => $sdata['short'],
                        'section_level_code' => 0,
                        'tab_code' => $sdata['tab_code'],
                        'is_active' => true,
                    ]
                );
                $sectionIds[$key] = $sec->id;
            }

            // 3. Ensure custom base fields exist
            $customBaseFields = [
                [
                    'tab_code' => 101,
                    'field_id' => 'gender',
                    'field_name' => 'gender',
                    'level_name' => 'Gender',
                    'field_type' => 'select',
                    'options' => ['1' => 'Male', '2' => 'Female', '3' => 'Other'],
                    'validation_rule' => 'required',
                    'db_colunm' => 'gender',
                    'is_common' => false,
                    'is_active' => true,
                    'field_position' => 5,
                    'is_mendetory' => 1,
                ],
                [
                    'tab_code' => 101,
                    'field_id' => 'monthly_family_income',
                    'field_name' => 'monthly_family_income',
                    'level_name' => 'Monthly Family Income (In Rs)',
                    'field_type' => 'text',
                    'validation_rule' => 'required|numeric',
                    'regex' => '^[0-9]+$',
                    'db_colunm' => 'other_details',
                    'is_common' => false,
                    'is_active' => true,
                    'field_position' => 14,
                    'is_mendetory' => 1,
                ],
                [
                    'tab_code' => 106,
                    'field_id' => 'ration_card_category',
                    'field_name' => 'ration_card_category',
                    'level_name' => 'Digital Ration Card category',
                    'field_type' => 'select',
                    'options' => ['AAY' => 'AAY', 'PHH' => 'PHH', 'SPHH' => 'SPHH', 'RKSY-I' => 'RKSY-I', 'RKSY-II' => 'RKSY-II'],
                    'validation_rule' => 'required',
                    'db_colunm' => 'other_details',
                    'is_common' => false,
                    'is_active' => true,
                    'field_position' => 1,
                ],
                [
                    'tab_code' => 106,
                    'field_id' => 'ration_card_number',
                    'field_name' => 'ration_card_number',
                    'level_name' => 'Ration Card Number',
                    'field_type' => 'text',
                    'validation_rule' => 'required|string',
                    'db_colunm' => 'other_details',
                    'is_common' => false,
                    'is_active' => true,
                    'field_position' => 2,
                ],
                [
                    'tab_code' => 106,
                    'field_id' => 'pan',
                    'field_name' => 'pan',
                    'level_name' => 'Pan',
                    'field_type' => 'text',
                    'validation_rule' => 'nullable|string',
                    'db_colunm' => 'other_details',
                    'is_common' => false,
                    'is_active' => true,
                    'field_position' => 3,
                ],
                [
                    'tab_code' => 106,
                    'field_id' => 'voter_id_number',
                    'field_name' => 'voter_id_number',
                    'level_name' => 'EPIC/Voter Id Number',
                    'field_type' => 'text',
                    'validation_rule' => 'required|string',
                    'db_colunm' => 'other_details',
                    'is_common' => false,
                    'is_active' => true,
                    'field_position' => 4,
                ],
                [
                    'tab_code' => 106,
                    'field_id' => 'ahl_tin',
                    'field_name' => 'ahl_tin',
                    'level_name' => 'AHL TIN',
                    'field_type' => 'text',
                    'validation_rule' => 'nullable|string',
                    'db_colunm' => 'other_details',
                    'is_common' => false,
                    'is_active' => true,
                    'field_position' => 5,
                ],
                [
                    'tab_code' => 106,
                    'field_id' => 'bpl_seq_number',
                    'field_name' => 'bpl_seq_number',
                    'level_name' => 'BPL Seq Number (if available)',
                    'field_type' => 'text',
                    'validation_rule' => 'nullable|string',
                    'db_colunm' => 'other_details',
                    'is_common' => false,
                    'is_active' => true,
                    'field_position' => 6,
                ],
                [
                    'tab_code' => 106,
                    'field_id' => 'bpl_id_number',
                    'field_name' => 'bpl_id_number',
                    'level_name' => 'BPL Id Number (if available)',
                    'field_type' => 'text',
                    'validation_rule' => 'nullable|string',
                    'db_colunm' => 'other_details',
                    'is_common' => false,
                    'is_active' => true,
                    'field_position' => 7,
                ],
                [
                    'tab_code' => 106,
                    'field_id' => 'bpl_total_score',
                    'field_name' => 'bpl_total_score',
                    'level_name' => 'BPL Total Score (if available)',
                    'field_type' => 'text',
                    'validation_rule' => 'nullable|string',
                    'db_colunm' => 'other_details',
                    'is_common' => false,
                    'is_active' => true,
                    'field_position' => 8,
                ],
                [
                    'tab_code' => 102,
                    'field_id' => 'wb_dwelling_years',
                    'field_name' => 'wb_dwelling_years',
                    'level_name' => 'Number of years of Dwelling in WB',
                    'field_type' => 'text',
                    'validation_rule' => 'required|numeric',
                    'db_colunm' => 'other_details',
                    'is_common' => false,
                    'is_active' => true,
                    'field_position' => 12,
                ],
                [
                    'tab_code' => 102,
                    'field_id' => 'mobile_number',
                    'field_name' => 'mobile_number',
                    'level_name' => 'Mobile No.',
                    'field_type' => 'text',
                    'validation_rule' => 'required|digits:10',
                    'db_colunm' => 'mobile_no',
                    'is_common' => false,
                    'is_active' => true,
                    'field_position' => 13,
                ],
                [
                    'tab_code' => 102,
                    'field_id' => 'email_id',
                    'field_name' => 'email_id',
                    'level_name' => 'Email ID',
                    'field_type' => 'text',
                    'validation_rule' => 'nullable|email',
                    'db_colunm' => 'email',
                    'is_common' => false,
                    'is_active' => true,
                    'field_position' => 14,
                ],
            ];

            foreach ($customBaseFields as $cbf) {
                SchemeTabBasefield::updateOrCreate(
                    [
                        'field_id' => $cbf['field_id'],
                        'tab_code' => $cbf['tab_code'],
                        'scheme_id' => $schemeId,
                    ],
                    array_merge(['scheme_id' => $schemeId], $cbf)
                );
            }

            // 4. Seed Tab Mappings
            $tabs = [
                ['tab_code' => 101, 'position' => 1, 'is_current_address' => false],
                ['tab_code' => 106, 'position' => 2, 'is_current_address' => false],
                ['tab_code' => 102, 'position' => 3, 'is_current_address' => false],
                ['tab_code' => 103, 'position' => 4, 'is_current_address' => false],
                ['tab_code' => 104, 'position' => 5, 'is_current_address' => false],
                ['tab_code' => 105, 'position' => 6, 'is_current_address' => false],
            ];

            foreach ($tabs as $tab) {
                SchemeTabMapping::create([
                    'scheme_id' => $schemeId,
                    'tab_code' => $tab['tab_code'],
                    'position' => $tab['position'],
                    'is_current_address' => $tab['is_current_address'],
                    'is_active' => true,
                ]);
            }

            // 5. Seed Form Fields for Tabs
            $fieldsByTab = [
                101 => [
                    'application_date',
                    'application_type',
                    'ds_registration_no',
                    'ds_date',
                    'beneficiary_name',
                    'gender',
                    'dob',
                    'age',
                    'ben_father_name',
                    'ben_mother_name',
                    'caste',
                    'caste_cer_no',
                    'marital_status',
                    'ben_spouse_name',
                    'monthly_family_income',
                ],
                106 => [
                    'ration_card_category',
                    'ration_card_number',
                    'pan',
                    'voter_id_number',
                    'ahl_tin',
                    'bpl_seq_number',
                    'bpl_id_number',
                    'bpl_total_score',
                ],
                102 => [
                    'state',
                    'district_id',
                    'assemblie',
                    'rural_urban',
                    'blockurban',
                    'gpward',
                    'villtowncity',
                    'housepremiseno',
                    'postoffice',
                    'pincode',
                    'policestation',
                    'wb_dwelling_years',
                    'mobile_number',
                    'email_id',
                ],
                103 => [
                    'ifscode',
                    'bankname',
                    'bank_branch_name',
                    'bankaccountnumber',
                    'confirmbankaccountnumber',
                ],
            ];

            foreach ($fieldsByTab as $tabCode => $fieldIds) {
                foreach ($fieldIds as $index => $fieldId) {
                    $base = SchemeTabBasefield::where('field_id', $fieldId)
                        ->where(function ($q) use ($schemeId) {
                            $q->where('scheme_id', $schemeId)
                                ->orWhere('scheme_id', 0)
                                ->orWhereNull('scheme_id');
                        })
                        ->where(function ($q) use ($tabCode) {
                            $q->where('tab_code', $tabCode)->orWhere('tab_code', 0);
                        })
                        ->orderByRaw('CASE WHEN scheme_id = ? THEN 1 ELSE 2 END', [$schemeId])
                        ->first();

                    if (!$base) {
                        continue;
                    }

                    SchemeTabFormField::create([
                        'tab_field_id' => $base->id,
                        'scheme_id' => $schemeId,
                        'tab_code' => $tabCode,
                        'level_name' => $base->level_name,
                        'field_name' => $base->field_name,
                        'field_type' => $base->field_type,
                        'field_id' => $base->field_id,
                        'options' => $base->options,
                        'validation_rule' => $base->validation_rule,
                        'regex' => $base->regex,
                        'section_level_id' => $base->section_level_id,
                        'section_level_type' => $base->section_level_type,
                        'confirm_of' => $base->confirm_of,
                        'dependent_on' => $base->dependent_on,
                        'dependent_on_values' => $base->dependent_on_values,
                        'field_class' => $base->field_class,
                        'is_multiple' => $base->is_multiple,
                        'field_position' => $index + 1,
                        'is_common' => $base->is_common,
                        'db_column' => $base->db_colunm,
                        'is_mandatory' => $base->is_mendetory,
                        'is_active' => true,
                        'is_readonly' => $base->is_readonly,
                        'is_syncable' => 0,
                    ]);
                }
            }

            // 6. Seed Attached Documents
            $docs = [
                ['code' => '161', 'is_required' => true, 'max_file_size' => '100KB', 'extension_type' => 'jpg,png,jpeg,pdf'], // Passport size profile photo
                ['code' => '165', 'is_required' => true, 'max_file_size' => '500KB', 'extension_type' => 'jpg,jpeg,png,pdf'], // Copy of Aadhar Card
                ['code' => '162', 'is_required' => true, 'max_file_size' => '500KB', 'extension_type' => 'jpg,jpeg,png,pdf'], // Copy of Caste Certificate
                ['code' => '1627', 'is_required' => true, 'max_file_size' => '500KB', 'extension_type' => 'jpg,jpeg,png,pdf'], // Age Proof documets
                ['code' => '169', 'is_required' => true, 'max_file_size' => '500KB', 'extension_type' => 'jpg,jpeg,png,pdf'], // Copy of Bank Pass book
                ['code' => '164', 'is_required' => true, 'max_file_size' => '500KB', 'extension_type' => 'jpg,jpeg,png,pdf'], // Copy of Digital Ration Card
                ['code' => '168', 'is_required' => false, 'max_file_size' => '500KB', 'extension_type' => 'jpg,jpeg,png,pdf'], // Copy of Income Certificate(Self Declaration)
                ['code' => '167', 'is_required' => false, 'max_file_size' => '500KB', 'extension_type' => 'jpg,jpeg,png,pdf'], // Copy of Residential Certificate(Self Declaration)
                ['code' => '1610', 'is_required' => false, 'max_file_size' => '500KB', 'extension_type' => 'jpg,jpeg,png,pdf'], // Others, please specify
                ['code' => '166', 'is_required' => false, 'max_file_size' => '500KB', 'extension_type' => 'jpg,jpeg,png,pdf'], // Copy of EPIC/ Voter Id
            ];

            foreach ($docs as $index => $doc) {
                $cm = \App\Models\Codemaster::where('code', $doc['code'])->first();
                if ($cm) {
                    SchemeAttachedDocMappings::create([
                        'scheme_id' => $schemeId,
                        'tab_code' => 104,
                        'doc_type_id' => $cm->id,
                        'field_position' => $index + 1,
                        'is_required' => $doc['is_required'],
                        'max_file_size' => $doc['max_file_size'],
                        'extension_type' => $doc['extension_type'],
                    ]);
                }
            }

            // 7. Seed Self Declaration Fields (Tab 105)
            $selfDecls = [
                [
                    'field_name' => 'aadhaar_consent',
                    'field_id' => 'aadhaar_consent',
                    'level_name' => 'consent to the use of the Aadhaar No. for authenticating my identity for social security pension (In case Aadhaar no. provided by the applicant)',
                    'field_type' => 'select',
                    'options' => ['give' => 'give', 'do not give' => 'do not give'],
                    'validation_rule' => 'required',
                    'section_level_id' => $sectionIds['aadhaar_sec'] ?? null,
                    'section_level_type' => 0,
                    'field_position' => 1,
                ],
                [
                    'field_name' => 'pension_from',
                    'field_id' => 'pension_from',
                    'level_name' => '',
                    'field_type' => 'checkbox',
                    'is_multiple' => true,
                    'options' => ['1' => 'Central Govt', '2' => 'State Govt', '3' => 'Local Administration', '4' => 'Govt. Aided Organization'],
                    'validation_rule' => 'nullable',
                    'section_level_id' => $sectionIds['pension_from_sec'] ?? null,
                    'section_level_type' => 0,
                    'field_position' => 2,
                ],
                [
                    'field_name' => 'nominee_name',
                    'field_id' => 'nominee_name',
                    'level_name' => 'Name',
                    'field_type' => 'text',
                    'validation_rule' => 'nullable|string',
                    'section_level_id' => $sectionIds['nominee_sec'] ?? null,
                    'section_level_type' => 0,
                    'field_position' => 3,
                ],
                [
                    'field_name' => 'nominee_address',
                    'field_id' => 'nominee_address',
                    'level_name' => 'Address',
                    'field_type' => 'text',
                    'validation_rule' => 'nullable|string',
                    'section_level_id' => $sectionIds['nominee_sec'] ?? null,
                    'section_level_type' => 0,
                    'field_position' => 4,
                ],
                [
                    'field_name' => 'nominee_relationship',
                    'field_id' => 'nominee_relationship',
                    'level_name' => 'Relationship',
                    'field_type' => 'text',
                    'validation_rule' => 'nullable|string',
                    'section_level_id' => $sectionIds['nominee_sec'] ?? null,
                    'section_level_type' => 0,
                    'field_position' => 5,
                ],
                [
                    'field_name' => 'social_security_pension',
                    'field_id' => 'social_security_pension',
                    'level_name' => '',
                    'field_type' => 'checkbox',
                    'is_multiple' => true,
                    'options' => [
                        '1' => 'NSAP Old Age',
                        '2' => 'NSAP Widow Pension',
                        '3' => 'NSAP Disability Pension',
                        '4' => 'Old Age Pension',
                        '5' => 'Widow Pension',
                        '6' => 'Disability Pension',
                        '7' => 'Lok Prasar Prakalpa',
                        '8' => "Fisherman's Old Age Pension",
                        '9' => 'Farmers Old Age Pension',
                        '10' => 'Artisan/Weaver Old Age Pension',
                    ],
                    'validation_rule' => 'nullable',
                    'section_level_id' => $sectionIds['social_sec'] ?? null,
                    'section_level_type' => 0,
                    'field_position' => 6,
                ],
            ];

            foreach ($selfDecls as $sd) {
                SelfDeclerationBasefield::create([
                    'scheme_id' => $schemeId,
                    'tab_code' => 105,
                    'field_name' => $sd['field_name'],
                    'field_id' => $sd['field_id'],
                    'level_name' => $sd['level_name'],
                    'field_type' => $sd['field_type'],
                    'is_multiple' => $sd['is_multiple'] ?? false,
                    'section_level_id' => $sd['section_level_id'],
                    'section_level_type' => $sd['section_level_type'],
                    'options' => $sd['options'] ?? null,
                    'validation_rule' => $sd['validation_rule'],
                    'field_position' => $sd['field_position'],
                    'is_active' => true,
                    'db_column' => 'other_details',
                ]);
            }

            // 8. Seed Grid Layouts (SchemeTabLayout)
            $layouts = [
                101 => [
                    ['row' => 1, 'columns' => 2],
                    ['row' => 2, 'columns' => 1],
                    ['row' => 3, 'columns' => 3],
                    ['row' => 4, 'columns' => 1],
                    ['row' => 5, 'columns' => 1],
                    ['row' => 6, 'columns' => 3],
                    ['row' => 7, 'columns' => 1],
                ],
                106 => [
                    ['row' => 1, 'columns' => 3],
                    ['row' => 2, 'columns' => 3],
                    ['row' => 3, 'columns' => 2],
                ],
                102 => [
                    ['row' => 1, 'columns' => 3],
                    ['row' => 2, 'columns' => 3],
                    ['row' => 3, 'columns' => 3],
                    ['row' => 4, 'columns' => 3],
                    ['row' => 5, 'columns' => 2],
                ],
                103 => [
                    ['row' => 1, 'columns' => 2],
                    ['row' => 2, 'columns' => 2],
                    ['row' => 3, 'columns' => 1],
                ],
            ];

            foreach ($layouts as $tabCode => $layoutConfig) {
                SchemeTabLayout::updateOrCreate(
                    [
                        'scheme_id' => $schemeId,
                        'tab_code' => $tabCode,
                    ],
                    [
                        'layout_json' => json_encode($layoutConfig),
                        'updated_at' => now(),
                    ]
                );
            }
        });

        // 9. Regenerate JSON & Blade templates via helper
        $data = SchemewiseStoreDataJsonHelper::generateSchemeJson($schemeId);
        SchemewiseStoreDataJsonHelper::storeSchemeJson($schemeId, $data);
        SchemewiseStoreDataJsonHelper::store($schemeId, $data['tabs']);

        $this->command?->info("Tapasili Bandhu Scheme (Scheme ID {$schemeId}) form fields successfully reset and regenerated!");
    }
}
