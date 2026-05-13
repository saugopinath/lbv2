<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\State;
class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = array(
            array(
                "state_code" => "19",
                "department_name" => "Department of Women & Child Development and Social Welfare",
                "short_name" => "WCD",
            ),
            array(
                "state_code" => "19",
                "department_name" => "Department of Women & Child Development and Social Welfare",
                "short_name" => "WCD&SWD",
            ),
            array(
                "state_code" => "19",
                "department_name" => "Tribal Welfare Department",
                "short_name" => "TWD",
            ),
            array(
                "state_code" => "19",
                "department_name" => "Department of Fisheries",
                "short_name" => "Fisheries",
            ),
            array(
                "state_code" => "19",
                "department_name" => "Micro, Small & Medium Enterprises and Textiles Department",
                "short_name" => "MSME&T",
            ),
            array(
                "state_code" => "19",
                "department_name" => "Information and Cultural Affairs Department",
                "short_name" => "I&CA",
            ),
            array(
                "state_code" => "19",
                "department_name" => "Backward Classes Welfare Department",
                "short_name" => "BCWD",
            ),
            array(
                "state_code" => "19",
                "department_name" => "No Department",
                "short_name" => "null",
            ),
        );
        foreach ($departments as $department_item) {
            Department::updateOrCreate(
                ['short_name' => $department_item['short_name']],
                [
                    'name'     => strtoupper($department_item['department_name']),
                    'state_id'   => State::where('lgd_code', '19')->firstOrFail()->id,
                ]
            );
        }
    }
}
