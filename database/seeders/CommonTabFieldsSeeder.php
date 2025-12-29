<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SchemeTabBasefield;
use Illuminate\Support\Facades\DB;

class CommonTabFieldsSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $fields = [
                [
                    'field_id'        => 'app_type',
                    'level_name'      => 'Application Type',
                    'field_name'      => 'app_type',
                    'field_type'      => 'select',
                    'options'         => [
                        "Normal Entry",
                        "Duare Sarkar",
                    ],
                    'is_common'       => true,
                    'tab_code'        => 101,
                    'validation_rule' => 'required',
                    'regex'           => null,
                    'is_multiple'     => false,
                    'is_active'       => true,
                ],
                [
                    'field_id'        => 'app_date',
                    'level_name'      => 'Application Date',
                    'field_name'      => 'app_date',
                    'field_type'      => 'date',
                    'is_common'       => true,
                    'tab_code'        => 101,
                    'validation_rule' => 'required|date',
                    'regex'           => null,
                    'section_id'      => null,
                    'is_multiple'     => false,
                    'is_active'       => true,
                ],
                [
                    'field_id'        => 'reg_no',
                    'level_name'      => 'Duare Sarkar Registration Number',
                    'field_name'      => 'reg_no',
                    'field_type'      => 'text',
                    'is_common'       => true,
                    'tab_code'        => 101,
                    'validation_rule' => 'required',
                    'regex'           => '^[A-Za-z0-9\-\/]+$',
                    'section_id'      => null,
                    'is_multiple'     => false,
                    'is_active'       => true,
                ],
                [
                    'field_id'        => 'ds_date',
                    'level_name'      => 'Duare Sarkar Date',
                    'field_name'      => 'ds_date',
                    'field_type'      => 'date',
                    'is_common'       => true,
                    'tab_code'        => 101,
                    'validation_rule' => 'required|date',
                    'regex'           => null,
                    'section_id'      => null,
                    'is_multiple'     => false,
                    'is_active'       => true,
                ],
                [
                    'level_name'      => 'Applicant Name',
                    'field_id'        => 'full_name',
                    'field_name'      => 'full_name',
                    'field_type'      => 'text',
                    'is_common'       => true,
                    'tab_code'        => 101,
                    'validation_rule' => 'required|string|max:150',
                    'regex'           => '^[A-Za-z .]+$',
                    'section_id'      => null,
                    'is_multiple'     => false,
                    'is_active'       => true,
                ],
                [
                    'field_id'        => 'mobile_no',
                    'level_name'      => 'Mobile Number',
                    'field_name'      => 'mobile_no',
                    'field_type'      => 'text',
                    'is_common'       => true,
                    'validation_rule' => 'required|digits:10',
                    'regex'           => '^[6-9][0-9]{9}$',
                    'section_id'      => null,
                    'is_multiple'     => false,
                    'is_active'       => true,
                ],
                [
                    'field_id'        => 'email_id',
                    'level_name'      => 'Email Address',
                    'field_name'      => 'email_id',
                    'field_type'      => 'email',
                    'options'         => null,
                    'is_common'       => true,
                    'tab_code'        => 101,
                    'validation_rule' => 'nullable|email',
                    'regex'           => null,
                    'section_id'      => null,
                    'is_multiple'     => false,
                    'is_active'       => true,
                ],
                [
                    'field_id'        => 'dob',
                    'level_name'      => 'Date of Birth',
                    'field_name'      => 'dob',
                    'field_type'      => 'date',
                    'is_common'       => true,
                    'tab_code'        => 101,
                    'validation_rule' => 'required|date',
                    'regex'           => null,
                    'section_id'      => null,
                    'is_multiple'     => false,
                    'is_active'       => true,
                ],
                [
                    'field_id'        => 'ffname',
                    'level_name'      => "Father's Name",
                    'field_name'      => 'ffname',
                    'field_type'      => 'text',
                    'is_common'       => true,
                    'tab_code'        => 101,
                    'validation_rule' => 'required|string',
                    'regex'           => '^[A-Za-z\s]+$',
                    'section_id'      => null,
                    'is_multiple'     => false,
                    'is_active'       => true,
                ],
                [
                    'field_id'        => 'mfname',
                    'level_name'      => "Mother's Name",
                    'field_name'      => 'mfname',
                    'field_type'      => 'text',
                    'is_common'       => true,
                    'tab_code'        => 101,
                    'validation_rule' => 'required|string',
                    'regex'           => '^[A-Za-z\s]+$',
                    'section_id'      => null,
                    'is_multiple'     => false,
                    'is_active'       => true,
                ],

                [
                    'field_id'        => 'mar_statu',
                    'level_name'      => 'Marital Status',
                    'field_name'      => 'mar_statu',
                    'field_type'      => 'select',
                    'options'         => [
                        "Un Married",
                        "Married",
                        "Widow",
                        "Divorcee",
                        "Widower"
                    ],
                    'is_common'       => true,
                    'tab_code'        => 101,
                    'validation_rule' => 'required',
                    'regex'           => null,
                    'section_id'      => null,
                    'is_multiple'     => false,
                    'is_active'       => true,
                ],
                [
                    'field_id'        => 'sfname',
                    'level_name'      => "Spouse's Name",
                    'field_name'      => 'sfname',
                    'field_type'      => 'text',
                    'is_common'       => true,
                    'tab_code'        => 101,
                    'validation_rule' => 'required|string',
                    'regex'           => '^[A-Za-z\s]+$',
                    'section_id'      => null,
                    'is_multiple'     => false,
                    'is_active'       => true,
                ],

                [
                    'field_id'        => 'caste',
                    'level_name'      => 'Caste',
                    'field_name'      => 'caste',
                    'field_type'      => 'select',
                    'options'         => [
                        "SC",
                        "ST",
                        "General",
                    ],
                    'is_common'       => true,
                    'tab_code'        => 101,
                    'validation_rule' => 'required',
                    'regex'           => null,
                    'section_id'      => null,
                    'is_multiple'     => false,
                    'is_active'       => true,
                ],


                [
                    'field_id'        => 'cas_cer_no',
                    'level_name'      => 'Caste Certificate Number',
                    'field_name'      => 'cas_cer_no',
                    'field_type'      => 'text',
                    'is_common'       => true,
                    'tab_code'        => 101,
                    'validation_rule' => 'required',
                    'regex'           => '^[A-Za-z0-9\/-]+$',
                    'section_id'      => null,
                    'is_multiple'     => false,
                    'is_active'       => true,
                ],
            ];

            foreach ($fields as $field) {
                SchemeTabBasefield::updateOrCreate(
                    ['field_id' => $field['field_id']],
                    $field
                );
            }
        });
    }
}
