<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Scheme;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SchemeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Scheme::truncate();

        $schemes = [
            [
                'id' => '1',
                'name' => 'Jai Johar (for ST)',
                'short_name' => 'johar',
                'dept_short_name' => 'TWD',
            ],
            [
                'id' => '2',
                'name' => 'WCD Manabik',
                'short_name' => 'manabik',
                'dept_short_name' => 'WCD&SW',
            ],
            [
                'id' => '3',
                'name' => 'Taposili Bandhu(for SC)',
                'short_name' => 'bandhu',
                'dept_short_name' => 'BCWD',
            ],
            [
                'id' => '5',
                'name' => 'Old age Pension for FisherMan',
                'short_name' => 'fisherman_oap',
                'dept_short_name' => 'Fisheries',
            ],
            [
                'id' => '6',
                'name' => 'MSME Pension',
                'short_name' => 'msme',
                'dept_short_name' => 'MSME&T',
            ],
            [
                'id' => '7',
                'name' => 'Textile Pension',
                'short_name' => 'weavers',
                'dept_short_name' => 'MSME&T',
            ],
            [
                'id' => '8',
                'name' => 'LPP Retainer',
                'short_name' => 'lokprasar_retainer',
                'dept_short_name' => 'null',
            ],
            [
                'id' => '9',
                'name' => 'LPP Pensioner',
                'short_name' => 'lokprasar_pensioner',
                'dept_short_name' => 'null',
            ],
            [
                'id' => '10',
                'name' => 'WCD Old Age Pension',
                'short_name' => 'oap_wcd',
                'dept_short_name' => 'WCD&SW',
            ],
            [
                'id' => '11',
                'name' => 'WCD Widow Pension',
                'short_name' => 'wp_wcd',
                'dept_short_name' => 'WCD&SW',
            ],
            [
                'id' => '13',
                'name' => 'Old age Pension for Farmer',
                'short_name' => 'farmer',
                'dept_short_name' => 'null',
            ],
            [
                'id' => '17',
                'name' => 'State Welfare Scheme for Purohits',
                'short_name' => 'purohit_monthly',
                'dept_short_name' => 'I&CA',
            ],
            [
                'id' => '19',
                'name' => 'Legacy Old Age Pension for ST',
                'short_name' => 'oap_st',
                'dept_short_name' => 'TWD',
            ],
            [
                'id' => '20',
                'name' => 'Annapurna Bhandar',
                'short_name' => 'LB',
                'dept_short_name' => 'WCD&SW',
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

        //// Reset auto-increment sequence after seeding hardcoded IDs
        DB::statement("SELECT setval(pg_get_serial_sequence('schemes', 'id'), COALESCE(max(id), 1)) FROM schemes;");
    }
}
