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
                    'field_id'        => 'full_name',
                    'scheme_id'       => 0,
                    'level_name'      => 'application',
                    'field_name'      => 'Full Name',
                    'field_type'      => 'text',
                    'options'         => null,
                    'is_common'       => true,
                    'tab_code'        => 101,
                    'validation_rule' => 'required|string|max:150',
                    'regex'           => '^[A-Za-z .]+$',
                    'section_id'      => null,
                    'is_multiple'     => false,
                    'field_position'  => 1,
                    'is_active'       => true,
                ],
                [
                    'field_id'        => 'mobile_no',
                    'scheme_id'       => 0,
                    'level_name'      => 'application',
                    'field_name'      => 'Mobile Number',
                    'field_type'      => 'text',
                    'options'         => null,
                    'is_common'       => true,
                    'tab_code'        => 101,
                    'validation_rule' => 'required|digits:10',
                    'regex'           => '^[6-9][0-9]{9}$',
                    'section_id'      => null,
                    'is_multiple'     => false,
                    'field_position'  => 2,
                    'is_active'       => true,
                ],
                [
                    'field_id'        => 'monthly_income',
                    'scheme_id'       => 0, // JB
                    'level_name'      => 'application',
                    'field_name'      => 'Monthly Income',
                    'field_type'      => 'number',
                    'options'         => null,
                    'is_common'       => false,
                    'tab_code'        => 101,
                    'validation_rule' => 'required|numeric|min:0',
                    'regex'           => '^[0-9]+$',
                    'section_id'      => null,
                    'is_multiple'     => false,
                    'field_position'  => 3,
                    'is_active'       => true,
                ],
                [
                    'field_id'        => 'disability_type',
                    'scheme_id'       => 2,
                    'level_name'      => 'application',
                    'field_name'      => 'Type of Disability',
                    'field_type'      => 'select',
                    'options'         => ['Locomotor', 'Visual', 'Hearing', 'Mental'],
                    'is_common'       => false,
                    'tab_code'        => 101,
                    'validation_rule' => 'required',
                    'regex'           => null,
                    'section_id'      => null,
                    'is_multiple'     => false,
                    'field_position'  => 4,
                    'is_active'       => true,
                ],
                [
                    'field_id'        => 'disability_percentage',
                    'scheme_id'       => 2,
                    'level_name'      => 'application',
                    'field_name'      => 'Disability Percentage',
                    'field_type'      => 'number',
                    'options'         => null,
                    'is_common'       => false,
                    'tab_code'        => 101,
                    'validation_rule' => 'required|integer|min:40|max:100',
                    'regex'           => '^[0-9]{2,3}$',
                    'section_id'      => null,
                    'is_multiple'     => false,
                    'field_position'  => 5,
                    'is_active'       => true,
                ],
                [
                    'field_id'        => 'aadhaar_no',
                    'scheme_id'       => 0,
                    'level_name'      => 'application',
                    'field_name'      => 'Aadhaar Number',
                    'field_type'      => 'text',
                    'options'         => null,
                    'is_common'       => false,
                    'tab_code'        => 101,
                    'validation_rule' => 'required|digits:12',
                    'regex'           => '^[0-9]{12}$',
                    'section_id'      => null,
                    'is_multiple'     => false,
                    'field_position'  => 10,
                    'is_active'       => true,
                ],

                [
                    'field_id'        => 'district_id',
                    'scheme_id'       => 0,
                    'level_name'      => 'application',
                    'field_name'      => 'District',
                    'field_type'      => 'select',
                    'options'         => [
                        'source' => 'lgd_districts',
                        'label'  => 'district_name',
                        'value'  => 'id'
                    ],
                    'is_common'       => true,
                    'tab_code'        => 102,
                    'validation_rule' => 'required',
                    'regex'           => null,
                    'section_id'      => null,
                    'is_multiple'     => false,
                    'field_position'  => 1,
                    'is_active'       => true,
                ],
                [
                    'field_id'        => 'block_id',
                    'scheme_id'       => 0,
                    'level_name'      => 'application',
                    'field_name'      => 'Block / Subdivision',
                    'field_type'      => 'select',
                    'options'         => [
                        'depends_on' => 'district_id',
                        'source'     => 'lgd_blocks',
                        'label'      => 'block_name',
                        'value'      => 'id'
                    ],
                    'is_common'       => true,
                    'tab_code'        => 102,
                    'validation_rule' => 'required',
                    'regex'           => null,
                    'section_id'      => null,
                    'is_multiple'     => false,
                    'field_position'  => 2,
                    'is_active'       => true,
                ],

                /*
                |--------------------------------------------------------------------------
                | BANK TAB – COMMON
                |--------------------------------------------------------------------------
                */
                [
                    'field_id'        => 'ifsc_code',
                    'scheme_id'       => 0,
                    'level_name'      => 'application',
                    'field_name'      => 'IFSC Code',
                    'field_type'      => 'text',
                    'options'         => null,
                    'is_common'       => true,
                    'tab_code'        => 104,
                    'validation_rule' => 'required|size:11',
                    'regex'           => '^[A-Z]{4}0[A-Z0-9]{6}$',
                    'section_id'      => null,
                    'is_multiple'     => false,
                    'field_position'  => 1,
                    'is_active'       => true,
                ],
                [
                    'field_id'        => 'account_no',
                    'scheme_id'       => 0,
                    'level_name'      => 'application',
                    'field_name'      => 'Account Number',
                    'field_type'      => 'text',
                    'options'         => null,
                    'is_common'       => true,
                    'tab_code'        => 104,
                    'validation_rule' => 'required|numeric',
                    'regex'           => '^[0-9]{9,18}$',
                    'section_id'      => null,
                    'is_multiple'     => false,
                    'field_position'  => 2,
                    'is_active'       => true,
                ],

                /*
                |--------------------------------------------------------------------------
                | SELF DECLARATION – COMMON
                |--------------------------------------------------------------------------
                */
                [
                    'field_id'        => 'self_declaration',
                    'scheme_id'       => 0,
                    'level_name'      => 'application',
                    'field_name'      => 'Self Declaration',
                    'field_type'      => 'checkbox',
                    'options'         => null,
                    'is_common'       => true,
                    'tab_code'        => 105,
                    'validation_rule' => 'accepted',
                    'regex'           => null,
                    'section_id'      => null,
                    'is_multiple'     => false,
                    'field_position'  => 1,
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