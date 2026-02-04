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
                    'field_id' => 'app_type',
                    'level_name' => 'Application Type',
                    'field_name' => 'app_type',
                    'field_type' => 'select',
                    'options' => [
                        "1" => "Normal Entry",
                        "2" => "Duare Sarkar Entry",
                    ],
                    'is_common' => true,
                    'tab_code' => 101,
                    'validation_rule' => 'required',
                    'regex' => null,
                    'is_multiple' => false,
                    'is_active' => true,
                    'field_position' => 1,
                    'db_colunm' => 'app_type',
                ],
                [
                    'field_id' => 'app_date',
                    'level_name' => 'Application Date',
                    'field_name' => 'app_date',
                    'field_type' => 'date',
                    'is_common' => true,
                    'tab_code' => 101,
                    'validation_rule' => 'required|date',
                    'regex' => null,
                    'section_level_id' => null,
                    'is_multiple' => false,
                    'is_active' => true,
                    'field_position' => 2,
                    'db_colunm' => 'app_date'

                ],
                [
                    'field_id' => 'reg_no',
                    'level_name' => 'Duare Sarkar Registration Number',
                    'field_name' => 'reg_no',
                    'field_type' => 'text',
                    'is_common' => true,
                    'tab_code' => 101,
                    'validation_rule' => 'required_if:formData.app_type,2',
                    'dependent_on' => 'app_type',
                    'dependent_on_values' => ["2"],
                    'regex' => '^[A-Za-z0-9\-\/]+$',
                    'section_level_id' => null,
                    'is_multiple' => false,
                    'is_active' => true,
                    'field_position' => 3,
                    'db_colunm' => 'reg_no'

                ],
                [
                    'field_id' => 'ds_date',
                    'level_name' => 'Duare Sarkar Date',
                    'field_name' => 'ds_date',
                    'field_type' => 'date',
                    'is_common' => true,
                    'tab_code' => 101,
                    'validation_rule' => 'required_if:formData.app_type,2|nullable|date',
                    'dependent_on' => 'app_type',
                    'dependent_on_values' => ["2"],
                    'regex' => null,
                    'section_level_id' => null,
                    'is_multiple' => false,
                    'is_active' => true,
                    'field_position' => 4,
                    'db_colunm' => 'ds_date'

                ],
                [
                    'level_name' => 'Applicant Name',
                    'field_id' => 'full_name',
                    'field_name' => 'full_name',
                    'field_type' => 'text',
                    'is_common' => true,
                    'tab_code' => 101,
                    'validation_rule' => 'required|string|max:150',
                    'regex' => '^[A-Za-z .]+$',
                    'section_level_id' => null,
                    'is_multiple' => false,
                    'is_active' => true,
                    'field_position' => 5,
                    'db_colunm' => 'full_name',
                    'is_mendetory' => 1,
                ],

                [
                    'field_id' => 'age',
                    'level_name' => 'Age',
                    'field_name' => 'age',
                    'field_type' => 'text',
                    'is_common' => true,
                    'validation_rule' => 'required|integer|min:25|max:60',
                    'regex' => null,
                    'section_level_id' => null,
                    'is_multiple' => false,
                    'is_active' => true,
                    'tab_code' => 101,
                    'field_position' => 6,
                    'db_colunm' => null,
                    'is_readonly' => 1,

                ],

                [
                    'field_id' => 'email_id',
                    'level_name' => 'Email Address',
                    'field_name' => 'email_id',
                    'field_type' => 'text',
                    'options' => null,
                    'is_common' => true,
                    'tab_code' => 101,
                    'validation_rule' => 'nullable|email',
                    'regex' => null,
                    'section_level_id' => null,
                    'is_multiple' => false,
                    'is_active' => true,
                    'field_position' => 7,
                    'db_colunm' => 'email_id'

                ],
                [
                    'field_id' => 'dob',
                    'level_name' => 'Date of Birth',
                    'field_name' => 'dob',
                    'field_type' => 'date',
                    'is_common' => true,
                    'tab_code' => 101,
                    'validation_rule' => 'required|date',
                    'regex' => null,
                    'section_level_id' => null,
                    'is_multiple' => false,
                    'is_active' => true,
                    'field_position' => 8,
                    'db_colunm' => 'dob'

                ],
                [
                    'field_id' => 'ffname',
                    'level_name' => "Father's Name",
                    'field_name' => 'ffname',
                    'field_type' => 'text',
                    'is_common' => true,
                    'tab_code' => 101,
                    'validation_rule' => 'required|string',
                    'regex' => '^[A-Za-z\s]+$',
                    'section_level_id' => null,
                    'is_multiple' => false,
                    'is_active' => true,
                    'field_position' => 9,
                    'db_colunm' => 'ffname'

                ],
                [
                    'field_id' => 'mfname',
                    'level_name' => "Mother's Name",
                    'field_name' => 'mfname',
                    'field_type' => 'text',
                    'is_common' => true,
                    'tab_code' => 101,
                    'validation_rule' => 'required|string',
                    'regex' => '^[A-Za-z\s]+$',
                    'section_level_id' => null,
                    'is_multiple' => false,
                    'is_active' => true,
                    'field_position' => 10,
                    'db_colunm' => 'mfname'

                ],

                [
                    'field_id' => 'mar_statu',
                    'level_name' => 'Marital Status',
                    'field_name' => 'mar_statu',
                    'field_type' => 'select',
                    'options' => [
                        "1" => "Un Married",
                        "2" => "Married",
                        "3" => "Widow",
                        "4" => "Divorcee",
                        "5" => "Widower",
                    ],

                    'is_common' => true,
                    'tab_code' => 101,
                    'validation_rule' => 'required',
                    'regex' => null,
                    'section_level_id' => null,
                    'is_multiple' => false,
                    'is_active' => true,
                    'field_position' => 11,
                    'db_colunm' => 'mar_statu'

                ],
                [
                    'field_id' => 'sfname',
                    'level_name' => "Spouse's Name",
                    'field_name' => 'sfname',
                    'field_type' => 'text',
                    'is_common' => true,
                    'tab_code' => 101,
                    'validation_rule' => 'required_if:formData.mar_statu,2,3,5',
                    'regex' => '^[A-Za-z\s]+$',
                    "dependent_on" => "mar_statu",
                    "dependent_on_values" => ["2", "3", "5"],
                    'section_level_id' => null,
                    'is_multiple' => false,
                    'is_active' => true,
                    'field_position' => 12,
                    'db_colunm' => 'sfname'
                ],

                [
                    'field_id' => 'caste',
                    'level_name' => 'Caste',
                    'field_name' => 'caste',
                    'field_type' => 'select',
                    'options' => [
                        "1" => "SC",
                        "2" => "ST",
                        "3" => "OBC",
                        "4" => "General",
                    ],

                    'is_common' => true,
                    'tab_code' => 101,
                    'validation_rule' => 'required',
                    'regex' => null,
                    'section_level_id' => null,
                    'is_multiple' => false,
                    'is_active' => true,
                    'field_position' => 13,
                    'db_colunm' => 'caste'

                ],
                [
                    'field_id' => 'cas_cer_no',
                    'level_name' => 'Caste Certificate Number',
                    'field_name' => 'cas_cer_no',
                    'field_type' => 'text',
                    'is_common' => true,
                    'tab_code' => 101,
                    'validation_rule' => 'required_if:formData.caste,1,2,3',
                    'regex' => '^[A-Za-z0-9\/-]+$',
                    "dependent_on" => "caste",
                    "dependent_on_values" => ["1", "2", "3"],
                    'section_level_id' => null,
                    'is_multiple' => false,
                    'is_active' => true,
                    'field_position' => 14,
                    'db_colunm' => 'cas_cer_no'

                ],
                [
                    'field_id' => 'mobile_no',
                    'level_name' => 'Mobile Number',
                    'field_name' => 'mobile_no',
                    'field_type' => 'text',
                    'is_common' => true,
                    'validation_rule' => 'required|digits:10',
                    'regex' => '^[6-9][0-9]{9}$',
                    'section_level_id' => null,
                    'is_multiple' => false,
                    'is_active' => true,
                    'field_position' => 1,
                    'db_colunm' => 'other_details',
                    'is_mendetory' => 1,
                ],
                // contact Details
                [
                    'field_id' => 'district_id',
                    'field_name' => 'district_id',
                    'level_name' => 'District',
                    'field_type' => 'select',
                    'field_class' => 'district_id',
                    // 'options'         => [
                    //     'source' => 'lgd_districts',
                    //     'label'  => 'name',
                    //     'value'  => 'id',
                    //     'visible_key' => 'district_dropdown'
                    // ],
                    'is_common' => true,
                    'tab_code' => 102,
                    'validation_rule' => 'required',
                    'regex' => null,
                    'section_level_id' => null,
                    'is_multiple' => false,
                    'field_position' => 1,
                    'is_active' => true,
                    'db_colunm' => 'district_id',
                    'is_mendetory' => 1,
                ],
                [
                    'field_id' => 'rural_urban',
                    'field_name' => 'rural_urban',
                    'level_name' => 'Rural/Urbar',
                    'field_type' => 'select',
                    'field_class' => 'rural_urban',
                    // 'options'         => [
                    //     'source' => 'config',
                    //     'key'    => 'constants.rural_urban',
                    //     'visible_key' => 'rural_urban_dropdown'
                    // ],
                    'is_common' => true,
                    'tab_code' => 102,
                    'validation_rule' => 'required',
                    'regex' => null,
                    'section_level_id' => null,
                    'is_multiple' => false,
                    'field_position' => 2,
                    'is_active' => true,
                    'db_colunm' => 'rural_urban',
                    'is_mendetory' => 1,
                ],

                [
                    'field_id' => 'blockurban',
                    'field_name' => 'blockurban',
                    'level_name' => 'Block/Municipality',
                    'field_type' => 'select',
                    'field_class' => 'blockurban',
                    // 'options'         => [
                    //     'depends_on' => ['district_id', 'rural_urban'],
                    //     'source_map' => [
                    //         '2' => 'lgd_blocks',
                    //         '1' => 'lgd_municipalities'
                    //     ],
                    //     'label' => 'name',
                    //     'value' => 'id',
                    //     'visible_key' => 'block_dropdown'
                    // ],
                    'is_common' => true,
                    'tab_code' => 102,
                    'validation_rule' => 'required',
                    'regex' => null,
                    'section_level_id' => null,
                    'is_multiple' => false,
                    'field_position' => 3,
                    'is_active' => true,
                    'db_colunm' => 'blockurban',
                    'is_mendetory' => 1,
                ],

                /*
                |--------------------------------------------------------------------------
                | GP / Ward
                |--------------------------------------------------------------------------
                */
                [
                    'field_id' => 'gpWard',
                    'field_name' => 'gpWard',
                    'level_name' => 'GP / Ward',
                    'field_type' => 'select',
                    'field_class' => 'gpWard',
                    // 'options'         => [
                    //     'depends_on' => ['blockurban', 'rural_urban'],
                    //     'source_map' => [
                    //         '2' => 'lgd_gps',
                    //         '1' => 'lgd_wards'
                    //     ],
                    //     'label' => 'name',
                    //     'value' => 'id',
                    //     'visible_key' => 'gp_ward_dropdown'
                    // ],
                    'is_common' => true,
                    'tab_code' => 102,
                    'validation_rule' => 'required',
                    'regex' => null,
                    'section_level_id' => null,
                    'is_multiple' => false,
                    'field_position' => 4,
                    'is_active' => true,
                    'db_colunm' => 'gpWard',
                    'is_mendetory' => 1,
                ],

                /*
                |--------------------------------------------------------------------------
                | State (Disabled)
                |--------------------------------------------------------------------------
                */
                [
                    'field_id' => 'state',
                    'field_name' => 'state',
                    'level_name' => 'State',
                    'field_type' => 'select',
                    'options' => [
                        '19' => 'West Bengal'
                    ],
                    'is_common' => true,
                    'tab_code' => 102,
                    'validation_rule' => 'required',
                    'regex' => null,
                    'section_level_id' => null,
                    'is_multiple' => false,
                    'field_position' => 5,
                    'is_active' => true,
                    'db_colunm' => 'state',
                    'is_mendetory' => 1,
                ],

                /*
                |--------------------------------------------------------------------------
                | Police Station
                |--------------------------------------------------------------------------
                */
                [
                    'field_id' => 'policestation',
                    'field_name' => 'policestation',
                    'level_name' => 'Police Station',
                    'field_type' => 'text',
                    'is_common' => true,
                    'tab_code' => 102,
                    'validation_rule' => 'required|string',
                    'regex' => '^[A-Za-z\s]+$',
                    'section_level_id' => null,
                    'is_multiple' => false,
                    'field_position' => 6,
                    'is_active' => true,
                    'db_colunm' => 'policestation'
                ],

                /*
                |--------------------------------------------------------------------------
                | Village / Town / City
                |--------------------------------------------------------------------------
                */
                [
                    'field_id' => 'villtowncity',
                    'field_name' => 'villtowncity',
                    'level_name' => 'Village / Town / City',
                    'field_type' => 'text',
                    'is_common' => true,
                    'tab_code' => 102,
                    'validation_rule' => 'required|string',
                    'regex' => '^[A-Za-z\s]+$',
                    'section_level_id' => null,
                    'is_multiple' => false,
                    'field_position' => 7,
                    'is_active' => true,
                    'db_colunm' => 'villtowncity'
                ],

                /*
                |--------------------------------------------------------------------------
                | House / Premise No
                |--------------------------------------------------------------------------
                */
                [
                    'field_id' => 'housepremiseno',
                    'field_name' => 'housepremiseno',
                    'level_name' => 'House / Premise No',
                    'field_type' => 'text',
                    'options' => null,
                    'is_common' => true,
                    'tab_code' => 102,
                    'validation_rule' => 'nullable|string',
                    'regex' => null,
                    'section_level_id' => null,
                    'is_multiple' => false,
                    'field_position' => 8,
                    'is_active' => true,
                    'db_colunm' => 'housepremiseno'
                ],

                /*
                |--------------------------------------------------------------------------
                | Post Office
                |--------------------------------------------------------------------------
                */
                [
                    'field_id' => 'postoffice',
                    'field_name' => 'postoffice',
                    'scheme_id' => 0,
                    'level_name' => 'Post Office',
                    'field_type' => 'text',
                    'is_common' => true,
                    'tab_code' => 102,
                    'validation_rule' => 'required|string',
                    'regex' => '^[A-Za-z\s]+$',
                    'section_level_id' => null,
                    'is_multiple' => false,
                    'field_position' => 9,
                    'is_active' => true,
                    'db_colunm' => 'postoffice'
                ],

                /*
                |--------------------------------------------------------------------------
                | Pin Code
                |--------------------------------------------------------------------------
                */
                [
                    'field_id' => 'pincode',
                    'field_name' => 'pincode',
                    'level_name' => 'Pin Code',
                    'field_type' => 'text',
                    'is_common' => true,
                    'tab_code' => 102,
                    'validation_rule' => 'required|digits:6',
                    'regex' => '^[0-9]{6}$',
                    'section_level_id' => null,
                    'is_multiple' => false,
                    'field_position' => 10,
                    'is_active' => true,
                    'db_colunm' => 'pincode'
                ],




                /*
                |--------------------------------------------------------------------------
                | IFSC Code
                |--------------------------------------------------------------------------
                */
                [
                    'field_id' => 'ifscode',
                    'field_name' => 'ifscode',
                    'level_name' => 'IFSC Code',
                    'field_type' => 'text',
                    // 'options'         => [
                    //     'uppercase' => true,
                    //     'maxlength' => 11,
                    //     'fetch_bank_details' => true
                    // ],
                    'is_common' => true,
                    'tab_code' => 103,
                    'validation_rule' => 'required|size:11',
                    'regex' => '^[A-Z]{4}0[A-Z0-9]{6}$',
                    'section_level_id' => null,
                    'is_multiple' => false,
                    'field_position' => 1,
                    'is_active' => true,
                    'db_colunm' => 'ifscode',
                    'is_mendetory' => 1,
                ],

                /*
                |--------------------------------------------------------------------------
                | Bank Name (Auto-filled)
                |--------------------------------------------------------------------------
                */
                [
                    'field_id' => 'bankname',
                    'field_name' => 'bankname',
                    'level_name' => 'Bank Name',
                    'field_type' => 'text',
                    // 'options'         => [
                    //     'disabled' => true,
                    //     'depends_on' => 'ifscode'
                    // ],
                    'is_common' => true,
                    'tab_code' => 103,
                    'validation_rule' => 'required|string',
                    'regex' => null,
                    'section_level_id' => null,
                    'is_multiple' => false,
                    'field_position' => 2,
                    'is_active' => true,
                    'db_colunm' => 'bankname',
                    'is_mendetory' => 1,
                    'is_readonly' => 1,
                ],

                /*
                |--------------------------------------------------------------------------
                | Bank Branch Name (Auto-filled)
                |--------------------------------------------------------------------------
                */
                [
                    'field_id' => 'bank_branch_name',
                    'field_name' => 'bank_branch_name',
                    'level_name' => 'Bank Branch Name',
                    'field_type' => 'text',
                    'is_common' => true,
                    'tab_code' => 103,
                    'validation_rule' => 'required|string',
                    'regex' => null,
                    'section_level_id' => null,
                    'is_multiple' => false,
                    'field_position' => 3,
                    'is_active' => true,
                    'db_colunm' => 'bank_branch_name',
                    'is_mendetory' => 1,
                    'is_readonly' => 1,
                ],
                /*
                |--------------------------------------------------------------------------
                | Bank Account Number
                |--------------------------------------------------------------------------
                */
                [
                    'field_id' => 'bankaccountnumber',
                    'field_name' => 'bankaccountnumber',
                    'level_name' => 'Bank Account Number',
                    'field_type' => 'text',
                    // 'options'         => [
                    //     'numeric' => true,
                    //     'mask' => true
                    // ],
                    'is_common' => true,
                    'tab_code' => 103,
                    'validation_rule' => 'required|numeric|min:9',
                    'regex' => '^[0-9]{9,18}$',
                    'section_level_id' => null,
                    'is_multiple' => false,
                    'field_position' => 4,
                    'is_active' => true,
                    'db_colunm' => 'bankaccountnumber',
                    'is_mendetory' => 1,
                ],

                /*
                |--------------------------------------------------------------------------
                | Confirm Bank Account Number
                |--------------------------------------------------------------------------
                */
                [
                    'field_id' => 'confirmbankaccountnumber',
                    'field_name' => 'confirmbankaccountnumber',
                    'level_name' => 'Confirm Bank Account Number',
                    'field_type' => 'text',
                    // 'options'         => [
                    //     'numeric' => true,
                    //     'match_with' => 'bankaccountnumber'
                    // ],
                    'is_common' => true,
                    'tab_code' => 103,
                    'validation_rule' => 'required|same:formData.bankaccountnumber',
                    'regex' => '^[0-9]{9,18}$',
                    'section_level_id' => null,
                    'is_multiple' => false,
                    'field_position' => 5,
                    'is_active' => true,
                    'db_colunm' => null,
                    'is_mendetory' => 1,
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
