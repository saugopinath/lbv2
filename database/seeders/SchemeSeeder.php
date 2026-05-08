<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Scheme;
use Illuminate\Database\Seeder;

class SchemeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $schemes = [

            [
                'id' => '20',
                'name' => 'Annapurna Bhandar',
                'short_name' => 'LB',
                'dept_short_name' => 'WCD',
            ],
            [
                'id' => '10',
                'name' => 'Old Age Pension',
                'short_name' => 'OAP',
                'dept_short_name' => 'WCD',
            ],
            [
                'id' => '11',
                'name' => 'Widow Pension',
                'short_name' => 'WP',
                'dept_short_name' => 'WCD',
            ],
            [
                'id' => '2',
                'name' => 'Manabik',
                'short_name' => 'manabik',
                'dept_short_name' => 'WCD',
            ],

        ];
        //   foreach ($schemes as $scheme_item) {
        //     Scheme::create([
        //         'id'     => $scheme_item['id'],
        //         'name'     => strtoupper($scheme_item['name']),
        //         'short_name'     => $scheme_item['short_name'],
        //         'department_id'   => Department::where('short_name', $scheme_item['dept_short_name'])->firstOrFail()->id,
        //     ]);
        // }
        foreach ($schemes as $scheme_item) {
            Scheme::updateOrCreate([
                'id' => $scheme_item['id'],
                'name' => strtoupper($scheme_item['name']),
                'short_name' => $scheme_item['short_name'],
                'department_id' => Department::where('short_name', $scheme_item['dept_short_name'])->firstOrFail()->id,
            ]);
        }
    }
}
