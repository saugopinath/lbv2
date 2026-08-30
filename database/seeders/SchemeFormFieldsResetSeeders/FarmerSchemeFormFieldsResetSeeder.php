<?php

namespace Database\Seeders\SchemeFormFieldsResetSeeders;

use App\Helpers\SchemewiseStoreDataJsonHelper;
use App\Models\MasterTab;
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

class FarmerSchemeFormFieldsResetSeeder extends Seeder
{
    public function run(): void
    {
        $scheme = Scheme::where('short_name', 'farmer')
            ->orWhere('name', 'LIKE', '%FARMER%')
            ->first() ?? Scheme::find(13);

        if (!$scheme) {
            $this->command?->error('Farmer Scheme not found!');
            return;
        }

        $schemeId = (int) $scheme->id;

        // Ensure MasterTab 108 (Family Members) exists
        MasterTab::firstOrCreate(
            ['tab_code' => 108],
            [
                'tab_name' => 'Family Members',
                'tab_code' => 108,
                'tab_short_name' => 'family_members',
                'tab_model_name' => 'BeneficiaryFamilyDetail',
                'tab_icon' => 'M16 1h-3.278A1.992 1.992 0 0 0 11 0H7a1.993 1.993 0 0 0-1.722 1H2a2 2 0 0 0-2 2v15a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2Zm-3 14H5a1 1 0 0 1 0-2h8a1 1 0 0 1 0 2Zm0-4H5a1 1 0 0 1 0-2h8a1 1 0 1 1 0 2Zm0-5H5a1 1 0 0 1 0-2h2V2h4v2h2a1 1 0 1 1 0 2Z',
                'is_active' => true,
            ]
        );

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
            $landSec = SectionLevelMaster::firstOrCreate(
                [
                    'scheme_id' => $schemeId,
                    'tab_code' => 107,
                    'section_level_short_name' => 'land_sec',
                ],
                [
                    'scheme_id' => $schemeId,
                    'section_level_name' => 'Land Details',
                    'section_level_short_name' => 'land_sec',
                    'section_level_code' => 0,
                    'tab_code' => 107,
                    'is_active' => true,
                ]
            );

            $familySec = SectionLevelMaster::firstOrCreate(
                [
                    'scheme_id' => $schemeId,
                    'tab_code' => 108,
                    'section_level_short_name' => 'family_sec',
                ],
                [
                    'scheme_id' => $schemeId,
                    'section_level_name' => 'Family Details',
                    'section_level_short_name' => 'family_sec',
                    'section_level_code' => 0,
                    'tab_code' => 108,
                    'is_active' => true,
                ]
            );

            // 3. Ensure custom base fields exist for Scheme
            $customBaseFields = [
                // Personal Details (101)
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
                // Identification Numbers (106)
                [
                    'tab_code' => 106,
                    'field_id' => 'ration_card_category',
                    'field_name' => 'ration_card_category',
                    'level_name' => 'Digital Ration Card category',
                    'field_type' => 'select',
                    'options' => ['AAY' => 'AAY', 'PHH' => 'PHH', 'SPHH' => 'SPHH', 'RKSY-I' => 'RKSY-I', 'RKSY-II' => 'RKSY-II'],
                    'validation_rule' => 'nullable',
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
                    'validation_rule' => 'nullable|string',
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
                    'validation_rule' => 'nullable|string',
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
                    'tab_code' => 106,
                    'field_id' => 'krishak_bondhu_id',
                    'field_name' => 'krishak_bondhu_id',
                    'level_name' => 'Krishak Bondhu ID',
                    'field_type' => 'text',
                    'validation_rule' => 'nullable|string',
                    'db_colunm' => 'other_details',
                    'is_common' => false,
                    'is_active' => true,
                    'field_position' => 9,
                ],
                // Contact Details (102)
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
                // Land Details (107)
                [
                    'tab_code' => 107,
                    'field_id' => 'mouza_name',
                    'field_name' => 'mouza_name',
                    'level_name' => 'Name of the Mouza',
                    'field_type' => 'text',
                    'validation_rule' => 'nullable|string',
                    'section_level_id' => $landSec->id,
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
                    'level_name' => 'J.L. No.',
                    'field_type' => 'text',
                    'validation_rule' => 'nullable|string',
                    'section_level_id' => $landSec->id,
                    'section_level_type' => 0,
                    'db_colunm' => 'other_details',
                    'is_common' => false,
                    'is_active' => true,
                    'field_position' => 2,
                ],
                [
                    'tab_code' => 107,
                    'field_id' => 'khatian_no',
                    'field_name' => 'khatian_no',
                    'level_name' => 'Khatian No.',
                    'field_type' => 'text',
                    'validation_rule' => 'nullable|string',
                    'section_level_id' => $landSec->id,
                    'section_level_type' => 0,
                    'db_colunm' => 'other_details',
                    'is_common' => false,
                    'is_active' => true,
                    'field_position' => 3,
                ],
                [
                    'tab_code' => 107,
                    'field_id' => 'plot_no',
                    'field_name' => 'plot_no',
                    'level_name' => 'Plot No.',
                    'field_type' => 'text',
                    'validation_rule' => 'nullable|string',
                    'section_level_id' => $landSec->id,
                    'section_level_type' => 0,
                    'db_colunm' => 'other_details',
                    'is_common' => false,
                    'is_active' => true,
                    'field_position' => 4,
                ],
                [
                    'tab_code' => 107,
                    'field_id' => 'area',
                    'field_name' => 'area',
                    'level_name' => 'Area in Acre',
                    'field_type' => 'text',
                    'validation_rule' => 'nullable|string',
                    'section_level_id' => $landSec->id,
                    'section_level_type' => 0,
                    'db_colunm' => 'other_details',
                    'is_common' => false,
                    'is_active' => true,
                    'field_position' => 5,
                ],
                [
                    'tab_code' => 107,
                    'field_id' => 'cultivation_by_applicant',
                    'field_name' => 'cultivation_by_applicant',
                    'level_name' => 'Select Cultivation by Applicant (Yes/No)',
                    'field_type' => 'select',
                    'options' => ['1' => 'Yes', '2' => 'No'],
                    'validation_rule' => 'required',
                    'section_level_id' => $landSec->id,
                    'section_level_type' => 0,
                    'db_colunm' => 'other_details',
                    'is_common' => false,
                    'is_active' => true,
                    'field_position' => 6,
                ],
                [
                    'tab_code' => 107,
                    'field_id' => 'source_present_income',
                    'field_name' => 'source_present_income',
                    'level_name' => 'Source of Present Income',
                    'field_type' => 'text',
                    'validation_rule' => 'nullable|string',
                    'section_level_id' => $landSec->id,
                    'section_level_type' => 0,
                    'db_colunm' => 'other_details',
                    'is_common' => false,
                    'is_active' => true,
                    'field_position' => 7,
                ],
                [
                    'tab_code' => 107,
                    'field_id' => 'other_benefits_received',
                    'field_name' => 'other_benefits_received',
                    'level_name' => 'Any other Benefits received',
                    'field_type' => 'text',
                    'validation_rule' => 'nullable|string',
                    'section_level_id' => $landSec->id,
                    'section_level_type' => 0,
                    'db_colunm' => 'other_details',
                    'is_common' => false,
                    'is_active' => true,
                    'field_position' => 8,
                ],
                // Family Members (108)
                [
                    'tab_code' => 108,
                    'field_id' => 'member_name',
                    'field_name' => 'member_name',
                    'level_name' => 'Name',
                    'field_type' => 'text',
                    'validation_rule' => 'required|string',
                    'section_level_id' => $familySec->id,
                    'section_level_type' => 0,
                    'db_colunm' => 'other_details',
                    'is_common' => false,
                    'is_active' => true,
                    'field_position' => 1,
                ],
                [
                    'tab_code' => 108,
                    'field_id' => 'member_address',
                    'field_name' => 'member_address',
                    'level_name' => 'Address',
                    'field_type' => 'text',
                    'validation_rule' => 'nullable|string',
                    'section_level_id' => $familySec->id,
                    'section_level_type' => 0,
                    'db_colunm' => 'other_details',
                    'is_common' => false,
                    'is_active' => true,
                    'field_position' => 2,
                ],
                [
                    'tab_code' => 108,
                    'field_id' => 'member_age',
                    'field_name' => 'member_age',
                    'level_name' => 'Age in Years',
                    'field_type' => 'text',
                    'validation_rule' => 'required|numeric',
                    'section_level_id' => $familySec->id,
                    'section_level_type' => 0,
                    'db_colunm' => 'other_details',
                    'is_common' => false,
                    'is_active' => true,
                    'field_position' => 3,
                ],
                [
                    'tab_code' => 108,
                    'field_id' => 'member_profession',
                    'field_name' => 'member_profession',
                    'level_name' => 'Profession',
                    'field_type' => 'text',
                    'validation_rule' => 'nullable|string',
                    'section_level_id' => $familySec->id,
                    'section_level_type' => 0,
                    'db_colunm' => 'other_details',
                    'is_common' => false,
                    'is_active' => true,
                    'field_position' => 4,
                ],
                [
                    'tab_code' => 108,
                    'field_id' => 'member_monthly_income',
                    'field_name' => 'member_monthly_income',
                    'level_name' => 'Monthly Income (Rs.)',
                    'field_type' => 'text',
                    'validation_rule' => 'nullable|numeric',
                    'section_level_id' => $familySec->id,
                    'section_level_type' => 0,
                    'db_colunm' => 'other_details',
                    'is_common' => false,
                    'is_active' => true,
                    'field_position' => 5,
                ],
                [
                    'tab_code' => 108,
                    'field_id' => 'member_relationship',
                    'field_name' => 'member_relationship',
                    'level_name' => 'Relation with Applicant',
                    'field_type' => 'text',
                    'validation_rule' => 'required|string',
                    'section_level_id' => $familySec->id,
                    'section_level_type' => 0,
                    'db_colunm' => 'other_details',
                    'is_common' => false,
                    'is_active' => true,
                    'field_position' => 6,
                ],
                [
                    'tab_code' => 108,
                    'field_id' => 'member_dependent',
                    'field_name' => 'member_dependent',
                    'level_name' => 'Dependent on Applicant',
                    'field_type' => 'select',
                    'options' => ['1' => 'Yes', '2' => 'No'],
                    'validation_rule' => 'required',
                    'section_level_id' => $familySec->id,
                    'section_level_type' => 0,
                    'db_colunm' => 'other_details',
                    'is_common' => false,
                    'is_active' => true,
                    'field_position' => 7,
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

            // 4. Seed Tab Mappings (8 tabs for Farmer)
            $tabs = [
                ['tab_code' => 101, 'position' => 1, 'is_current_address' => false],
                ['tab_code' => 106, 'position' => 2, 'is_current_address' => false],
                ['tab_code' => 102, 'position' => 3, 'is_current_address' => false],
                ['tab_code' => 103, 'position' => 4, 'is_current_address' => false],
                ['tab_code' => 107, 'position' => 5, 'is_current_address' => false],
                ['tab_code' => 108, 'position' => 6, 'is_current_address' => false],
                ['tab_code' => 104, 'position' => 7, 'is_current_address' => false],
                ['tab_code' => 105, 'position' => 8, 'is_current_address' => false],
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
                    'krishak_bondhu_id',
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
                    'cultivation_by_applicant',
                    'source_present_income',
                    'other_benefits_received',
                ],
                108 => [
                    'member_name',
                    'member_address',
                    'member_age',
                    'member_profession',
                    'member_monthly_income',
                    'member_relationship',
                    'member_dependent',
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

            // 6. Seed Attached Documents (Tab 104)
            $docs = [
                ['code' => '161', 'is_required' => true, 'max_file_size' => '100KB', 'extension_type' => 'jpg,png,jpeg,pdf'],
                ['code' => '165', 'is_required' => true, 'max_file_size' => '500KB', 'extension_type' => 'jpg,jpeg,png,pdf'],
                ['code' => '169', 'is_required' => true, 'max_file_size' => '500KB', 'extension_type' => 'jpg,jpeg,png,pdf'],
                ['code' => '164', 'is_required' => true, 'max_file_size' => '500KB', 'extension_type' => 'jpg,jpeg,png,pdf'],
                ['code' => '166', 'is_required' => true, 'max_file_size' => '500KB', 'extension_type' => 'jpg,jpeg,png,pdf'],
                ['code' => '1617', 'is_required' => false, 'max_file_size' => '500KB', 'extension_type' => 'jpg,jpeg,png,pdf'],
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
                    'field_name' => 'resident',
                    'field_id' => 'resident',
                    'level_name' => 'I am a resident of West Bengal',
                    'field_type' => 'checkbox',
                    'validation_rule' => 'required',
                    'section_level_id' => null,
                    'section_level_type' => null,
                    'field_position' => 1,
                ],
                [
                    'field_name' => 'no_govt_salary',
                    'field_id' => 'no_govt_salary',
                    'level_name' => 'I do not earn any monthly remuneration from any regular Government job',
                    'field_type' => 'checkbox',
                    'validation_rule' => 'required',
                    'section_level_id' => null,
                    'section_level_type' => null,
                    'field_position' => 2,
                ],
                [
                    'field_name' => 'info_true',
                    'field_id' => 'info_true',
                    'level_name' => 'All information and documents submitted are correct',
                    'field_type' => 'checkbox',
                    'validation_rule' => 'required',
                    'section_level_id' => null,
                    'section_level_type' => null,
                    'field_position' => 3,
                ],
                [
                    'field_name' => 'aadhaar_consent',
                    'field_id' => 'aadhaar_consent',
                    'level_name' => 'I give consent to the use of the Aadhaar No. for authenticating my identity for social security pension',
                    'field_type' => 'select',
                    'options' => ['give' => 'give', 'give not' => 'give not'],
                    'validation_rule' => 'required',
                    'section_level_id' => null,
                    'section_level_type' => null,
                    'field_position' => 4,
                ],
                [
                    'field_name' => 'pension_from',
                    'field_id' => 'pension_from',
                    'level_name' => 'Presently, I am receiving following pension(s) from:',
                    'field_type' => 'checkbox',
                    'is_multiple' => true,
                    'options' => ['1' => 'Central Govt', '2' => 'State Govt', '3' => 'Local Administration', '4' => 'Govt. Aided Organization'],
                    'validation_rule' => 'nullable',
                    'section_level_id' => null,
                    'section_level_type' => null,
                    'field_position' => 5,
                ],
                [
                    'field_name' => 'pension_other_sources_1',
                    'field_id' => 'pension_other_sources_1',
                    'level_name' => 'In case the applicant is receiving pension from other sources (1)',
                    'field_type' => 'text',
                    'validation_rule' => 'nullable|string',
                    'section_level_id' => null,
                    'section_level_type' => null,
                    'field_position' => 6,
                ],
                [
                    'field_name' => 'pension_other_sources_2',
                    'field_id' => 'pension_other_sources_2',
                    'level_name' => 'In case the applicant is receiving pension from other sources (2)',
                    'field_type' => 'text',
                    'validation_rule' => 'nullable|string',
                    'section_level_id' => null,
                    'section_level_type' => null,
                    'field_position' => 7,
                ],
                [
                    'field_name' => 'nominee_name',
                    'field_id' => 'nominee_name',
                    'level_name' => 'In the event of my death, I hereby nominate (Name)',
                    'field_type' => 'text',
                    'validation_rule' => 'nullable|string',
                    'section_level_id' => null,
                    'section_level_type' => null,
                    'field_position' => 5,
                ],
                [
                    'field_name' => 'nominee_address',
                    'field_id' => 'nominee_address',
                    'level_name' => 'Nominee Address',
                    'field_type' => 'text',
                    'validation_rule' => 'nullable|string',
                    'section_level_id' => null,
                    'section_level_type' => null,
                    'field_position' => 6,
                ],
                [
                    'field_name' => 'nominee_relationship',
                    'field_id' => 'nominee_relationship',
                    'level_name' => 'Nominee Relationship',
                    'field_type' => 'text',
                    'validation_rule' => 'nullable|string',
                    'section_level_id' => null,
                    'section_level_type' => null,
                    'field_position' => 7,
                ],
                [
                    'field_name' => 'social_security_pension',
                    'field_id' => 'social_security_pension',
                    'level_name' => 'Presently, I am receiving the following social Security Pension/s:',
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
                    'section_level_id' => null,
                    'section_level_type' => null,
                    'field_position' => 8,
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
                    ['row' => 3, 'columns' => 3],
                    ['row' => 4, 'columns' => 1],
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
                    ['row' => 3, 'columns' => 2],
                ],
                108 => [
                    ['row' => 1, 'columns' => 3],
                    ['row' => 2, 'columns' => 3],
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

        $this->command?->info("Farmer Pension (Scheme ID {$schemeId}) form fields successfully reset and regenerated with Family Members tab!");
    }
}
