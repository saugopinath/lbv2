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
