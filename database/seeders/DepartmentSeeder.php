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
                "department_name" => "West Bengal Tribal Development Department",
                "short_name" => "WBTDD",
            ),
            array(
                "state_code" => "19",
                "department_name" => "Backward Classes Welfare Department",
                "short_name" => "BCWD",
            ),
            array(
                "state_code" => "19",
                "department_name" => "Finance Department",
                "short_name" => "FND",
            ),
            array(
                "state_code" => "19",
                "department_name" => "Fisheries Department",
                "short_name" => "FSD",
            ),
            array(
                "state_code" => "19",
                "department_name" => "Department of Micro, Small & Medium Enterprises and Textiles",
                "short_name" => "MSME",
            ),
            array(
                "state_code" => "19",
                "department_name" => "Information and Cultural Affairs Department",
                "short_name" => "INCA",
            ),
            array(
                "state_code" => "19",
                "department_name" => "Department of Women & Child Development and Social Welfare",
                "short_name" => "WCD",
            ),
            array(
                "state_code" => "19",
                "department_name" => "Agriculture Department",
                "short_name" => "AGR",
            )
            
        );
        foreach ($departments as $department_item) {
            Department::create([
                'name'     => strtoupper($department_item['department_name']),
                'short_name'     => $department_item['short_name'],
                'state_id'   => State::where('id', '19')->firstOrFail()->id,
            ]);
        }
    }
}
