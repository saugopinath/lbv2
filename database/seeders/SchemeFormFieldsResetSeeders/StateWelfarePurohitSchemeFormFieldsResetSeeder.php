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

class StateWelfarePurohitSchemeFormFieldsResetSeeder extends Seeder
{
    public function run(): void
    {
        $scheme = Scheme::where('short_name', 'purohit_monthly')
            ->orWhere('name', 'LIKE', '%PUROHIT%')
            ->first() ?? Scheme::find(17);

        if (!$scheme) {
            $this->command?->error('State Welfare Purohit Scheme not found!');
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
                'land_dwelling_sec' => [
                    'name' => 'Land Details (In case of Dwelling House)',
                    'short' => 'land_dwelling_sec',
                    'tab_code' => 107,
                ],
                'pension_from_sec' => [
                    'name' => 'Presently, I am receiving following pension(s) from:',
                    'short' => 'pension_from_sec',
                    'tab_code' => 105,
                ],
                'pension_other_sec' => [
                    'name' => 'In case the applicant is receiving pension from other sources',
                    'short' => 'pension_other_sec',
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
                $sec = SectionLevelMaster::firstOrCreate(
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
                    'tab_code' => 106,
                    'field_id' => 'pan',
                    'field_name' => 'pan',
                    'level_name' => 'PAN',
                    'field_type' => 'text',
                    'validation_rule' => 'required',
                    'db_colunm' => 'other_details',
                    'is_common' => false,
                    'is_active' => true,
                    'field_position' => 4,
                    'is_mendetory' => 1,
                ],
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
                    'tab_code' => 101,
                    'field_id' => 'application_phase',
                    'field_name' => 'application_phase',
                    'level_name' => 'Select Application Phase',
                    'field_type' => 'select',
                    'options' => ['1' => 'Phase 1', '2' => 'Phase 2'],
                    'validation_rule' => 'required',
                    'db_colunm' => 'other_details',
                    'is_common' => false,
                    'is_active' => true,
                    'field_position' => 15,
                ],
                [
                    'tab_code' => 101,
                    'field_id' => 'temple_type',
                    'field_name' => 'temple_type',
                    'level_name' => 'Temple Type',
                    'field_type' => 'select',
                    'options' => ['1' => 'Private', '2' => 'Public', '3' => 'Trust'],
                    'validation_rule' => 'required',
                    'db_colunm' => 'other_details',
                    'is_common' => false,
                    'is_active' => true,
                    'field_position' => 16,
                ],
                [
                    'tab_code' => 107,
                    'field_id' => 'mouza_name',
                    'field_position' => 3,
                    'field_name' => 'mouza_name',
                    'level_name' => 'Name of the Mouza',
                    'field_type' => 'text',
                    'validation_rule' => 'nullable|string',
                    'section_level_id' => $sectionIds['land_dwelling_sec'] ?? null,
                    'section_level_type' => 0,
                    'db_colunm' => 'other_details',
                    'is_common' => false,
                    'is_active' => true,
                    'field_position' => 1,
                ],
                [
                    'tab_code' => 107,
                    'field_id' => 'jl_no',
                    'field_name' => 'jl_no',
                    'level_name' => 'J.L.No.',
                    'field_type' => 'text',
                    'validation_rule' => 'nullable|string',
                    'section_level_id' => $sectionIds['land_dwelling_sec'] ?? null,
                    'section_level_type' => 0,
                    'db_colunm' => 'other_details',
                    'is_common' => false,
                    'is_active' => true,
                    'field_position' => 3,
                ],
                [
                    'tab_code' => 107,
                    'field_id' => 'khatian_no',
                    'field_name' => 'khatian_no',
                    'level_name' => 'Khatian No.',
                    'field_type' => 'text',
                    'validation_rule' => 'nullable|string',
                    'section_level_id' => $sectionIds['land_dwelling_sec'] ?? null,
                    'section_level_type' => 0,
                    'db_colunm' => 'other_details',
                    'is_common' => false,
                    'is_active' => true,
                    'field_position' => 4,
                ],
                [
                    'tab_code' => 107,
                    'field_id' => 'plot_no',
                    'field_name' => 'plot_no',
                    'level_name' => 'Plot No.',
                    'field_type' => 'text',
                    'validation_rule' => 'nullable|string',
                    'section_level_id' => $sectionIds['land_dwelling_sec'] ?? null,
                    'section_level_type' => 0,
                    'db_colunm' => 'other_details',
                    'is_common' => false,
                    'is_active' => true,
                    'field_position' => 5,
                ],
                [
                    'tab_code' => 107,
                    'field_id' => 'area',
                    'field_name' => 'area',
                    'level_name' => 'Area',
                    'field_type' => 'text',
                    'validation_rule' => 'nullable|string',
                    'section_level_id' => $sectionIds['land_dwelling_sec'] ?? null,
                    'section_level_type' => 0,
                    'db_colunm' => 'other_details',
                    'is_common' => false,
                    'is_active' => true,
                    'field_position' => 6,
                ],
                [
                    'tab_code' => 107,
                    'field_id' => 'land_available',
                    'field_name' => 'land_available',
                    'level_name' => 'Land Available for House',
                    'field_type' => 'select',
                    'options' => ['1' => 'Yes', '2' => 'No'],
                    'validation_rule' => 'nullable',
                    'section_level_id' => $sectionIds['land_dwelling_sec'] ?? null,
                    'section_level_type' => 0,
                    'db_colunm' => 'other_details',
                    'is_common' => false,
                    'is_active' => true,
                    'field_position' => 1,
                ],
            ];

            foreach ($customBaseFields as $cbf) {
                SchemeTabBasefield::firstOrCreate(
                    [
                        'field_id' => $cbf['field_id'],
                        'tab_code' => $cbf['tab_code'],
                        'scheme_id' => $schemeId,
                    ],
                    array_merge(['scheme_id' => $schemeId], $cbf)
                );
            }

            // 4. Seed Tab Mappings (Notice: is_current_address = true for Contact Details Tab 102!)
            $tabs = [
                ['tab_code' => 101, 'position' => 1, 'is_current_address' => false],
                ['tab_code' => 106, 'position' => 2, 'is_current_address' => false],
                ['tab_code' => 102, 'position' => 3, 'is_current_address' => true],
                ['tab_code' => 103, 'position' => 4, 'is_current_address' => false],
                ['tab_code' => 107, 'position' => 5, 'is_current_address' => false],
                ['tab_code' => 104, 'position' => 6, 'is_current_address' => false],
                ['tab_code' => 105, 'position' => 7, 'is_current_address' => false],
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
                    'application_phase',
                    'temple_type',
                ],
                106 => [
                    'ration_card_category',
                    'ration_card_number',
                    'pan',
                    'voter_id_number',
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
                107 => [
                    'mouza_name',
                    'jl_no',
                    'khatian_no',
                    'plot_no',
                    'area',
                    'land_available',
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

                    // For Contact details (tab 102) when current address is enabled, fields are marked syncable
                    $isSyncable = ($tabCode === 102 && in_array($fieldId, ['district_id', 'assemblie', 'rural_urban', 'blockurban', 'gpward', 'villtowncity', 'housepremiseno', 'postoffice', 'pincode', 'policestation'])) ? 1 : 0;

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
                        'is_syncable' => $isSyncable,
                    ]);
                }
            }

            // 6. Seed Attached Documents (Tab 104)
            $docs = [
                ['code' => '161', 'is_required' => true, 'max_file_size' => '100KB', 'extension_type' => 'jpg,png,jpeg,pdf'],
                ['code' => '165', 'is_required' => true, 'max_file_size' => '500KB', 'extension_type' => 'jpg,jpeg,png,pdf'],
                ['code' => '169', 'is_required' => true, 'max_file_size' => '500KB', 'extension_type' => 'jpg,jpeg,png,pdf'],
                ['code' => '164', 'is_required' => true, 'max_file_size' => '500KB', 'extension_type' => 'jpg,jpeg,png,pdf'],
                ['code' => '166', 'is_required' => true, 'max_file_size' => '500KB', 'extension_type' => 'jpg,jpeg,png,pdf'],
                ['code' => '1617', 'is_required' => false, 'max_file_size' => '500KB', 'extension_type' => 'jpg,jpeg,png,pdf'],
                ['code' => '168', 'is_required' => false, 'max_file_size' => '500KB', 'extension_type' => 'jpg,jpeg,png,pdf'],
                ['code' => '167', 'is_required' => false, 'max_file_size' => '500KB', 'extension_type' => 'jpg,jpeg,png,pdf'],
                ['code' => '1610', 'is_required' => false, 'max_file_size' => '500KB', 'extension_type' => 'jpg,jpeg,png,pdf'],
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

            // 7. Seed Self Declaration Fields (Tab 105) with Section Associations
            $selfDecls = [
                [
                    'field_name' => 'aadhaar_consent',
                    'field_id' => 'aadhaar_consent',
                    'level_name' => 'I give consent to the use of the Aadhaar No. for authenticating my identity for social security pension (In case Aadhaar no. provided by the applicant)',
                    'field_type' => 'checkbox',
                    'validation_rule' => 'nullable',
                    'section_level_id' => null,
                    'section_level_type' => null,
                    'field_position' => 2,
                ],
                [
                    'field_name' => 'pension_from',
                    'field_id' => 'pension_from',
                    'level_name' => 'Presently, I am receiving following pension(s) from:',
                    'field_type' => 'select',
                    'options' => ['1' => 'Central Govt', '2' => 'State Govt', '3' => 'Local Administration', '4' => 'Govt. Aided Organization'],
                    'validation_rule' => 'nullable',
                    'section_level_id' => $sectionIds['pension_from_sec'] ?? null,
                    'section_level_type' => 0,
                    'field_position' => 2,
                ],
                [
                    'field_name' => 'pension_other_sources_1',
                    'field_id' => 'pension_other_sources_1',
                    'level_name' => 'In case the applicant is receiving pension from other sources (1)',
                    'field_type' => 'text',
                    'validation_rule' => 'nullable|string',
                    'section_level_id' => $sectionIds['pension_other_sec'] ?? null,
                    'section_level_type' => 0,
                    'field_position' => 3,
                ],
                [
                    'field_name' => 'pension_other_sources_2',
                    'field_id' => 'pension_other_sources_2',
                    'level_name' => 'In case the applicant is receiving pension from other sources (2)',
                    'field_type' => 'text',
                    'validation_rule' => 'nullable|string',
                    'section_level_id' => $sectionIds['pension_other_sec'] ?? null,
                    'section_level_type' => 0,
                    'field_position' => 4,
                ],
                [
                    'field_name' => 'nominee_name',
                    'field_id' => 'nominee_name',
                    'level_name' => 'In the event of my death, I hereby nominate (Name)',
                    'field_type' => 'text',
                    'validation_rule' => 'nullable|string',
                    'section_level_id' => $sectionIds['nominee_sec'] ?? null,
                    'section_level_type' => 0,
                    'field_position' => 5,
                ],
                [
                    'field_name' => 'nominee_address',
                    'field_id' => 'nominee_address',
                    'level_name' => 'Nominee Address',
                    'field_type' => 'text',
                    'validation_rule' => 'nullable|string',
                    'section_level_id' => $sectionIds['nominee_sec'] ?? null,
                    'section_level_type' => 0,
                    'field_position' => 6,
                ],
                [
                    'field_name' => 'nominee_relationship',
                    'field_id' => 'nominee_relationship',
                    'level_name' => 'Nominee Relationship',
                    'field_type' => 'text',
                    'validation_rule' => 'nullable|string',
                    'section_level_id' => $sectionIds['nominee_sec'] ?? null,
                    'section_level_type' => 0,
                    'field_position' => 7,
                ],
                [
                    'field_name' => 'other_scheme_beneficiary',
                    'field_id' => 'other_scheme_beneficiary',
                    'level_name' => 'I am a beneficiary of any other Government financial assistance scheme',
                    'field_type' => 'checkbox',
                    'validation_rule' => 'nullable',
                    'section_level_id' => $sectionIds['social_sec'] ?? null,
                    'section_level_type' => 0,
                    'field_position' => 8,
                ],
                [
                    'field_name' => 'has_pucca_house',
                    'field_id' => 'has_pucca_house',
                    'level_name' => 'I do have a Pucca house / dwelling',
                    'field_type' => 'checkbox',
                    'validation_rule' => 'nullable',
                    'section_level_id' => $sectionIds['social_sec'] ?? null,
                    'section_level_type' => 0,
                    'field_position' => 9,
                ],
                [
                    'field_name' => 'social_security_pension',
                    'field_id' => 'social_security_pension',
                    'level_name' => 'Presently, I am receiving the following social Security Pension/s:',
                    'field_type' => 'select',
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
                    'field_position' => 10,
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
                    ['row' => 8, 'columns' => 2],
                ],
                106 => [
                    ['row' => 1, 'columns' => 2],
                    ['row' => 2, 'columns' => 2],
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
                107 => [
                    ['row' => 1, 'columns' => 3],
                    ['row' => 2, 'columns' => 3],
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

        $this->command?->info("State Welfare Scheme for Purohits (Scheme ID {$schemeId}) form fields successfully reset and regenerated!");
    }
}
