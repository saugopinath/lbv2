<?php


namespace Database\Seeders;


use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\Scheme;
class SchemeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $schemes = array(

            array(
                "id" => "20",
                "name" => "Lakshmir Bhandar",
                "short_name" => "LB",
                "dept_short_name" => "WCD",
            ),
            array(
                "id" => "10",
                "name" => "Old Age Pension",
                "short_name" => "OAP",
                "dept_short_name" => "WCD",
            ),
             array(
                "id" => "11",
                "name" => "Widow Pension",
                "short_name" => "WP",
                "dept_short_name" => "WCD",
             ),
             array(
                "id" => "2",
                "name" => "Manabik",
                "short_name" => "manabik",
                "dept_short_name" => "WCD",
             )
        );
          foreach ($schemes as $scheme_item) {
            Scheme::updateOrCreate([
                'id'     => $scheme_item['id'],
                'name'     => strtoupper($scheme_item['name']),
                'short_name'     => $scheme_item['short_name'],
                'department_id'   => Department::where('short_name', $scheme_item['dept_short_name'])->firstOrFail()->id,
            ]);
        }
    }
}
