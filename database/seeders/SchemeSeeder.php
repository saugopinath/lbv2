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
                "id" => "1",
                "name" => "Jai Johar (for ST)",
                "short_name" => "ST",
                "dept_short_name" => "WBTDD",
            ),
            array(
                "id" => "3",
                "name" => "Taposili Bandhu(for SC)",
                "short_name" => "SC",
                "dept_short_name" => "BCWD",
            ),
            array(
                "id" => "2",
                "name" => "WCD Manabik",
                "short_name" => "MANABIK",
                "dept_short_name" => "WCD",
            ),
            array(
                "id" => "10",
                "name" => "WCD Old Age Pension",
                "short_name" => "OAP(WCD)",
                "dept_short_name" => "WCD",
            ),
            array(
                "id" => "11",
                "name" => "WCD Widow Pension",
                "short_name" => "WP",
                "dept_short_name" => "WCD",
            ),
            array(
                "id" => "5",
                "name" => "Old age Pension for FisherMan",
                "short_name" => "fisherman_oap",
                "dept_short_name" => "FSD",
            ),
            array(
                "id" => "6",
                "name" => "MSME Pension",
                "short_name" => "msme",
                "dept_short_name" => "MSME",
            ),
            array(
                "id" => "7",
                "name" => "Textile Pension",
                "short_name" => "textile",
                "dept_short_name" => "MSME",
            ),
            array(
                "id" => "8",
                "name" => "LPP Retainer",
                "short_name" => "lokprasarretainer",
                "dept_short_name" => "INCA",
            ),
            array(
                "id" => "9",
                "name" => "LPP Pensioner",
                "short_name" => "lokprasarpensioner",
                "dept_short_name" => "INCA",
            ),
            array(
                "id" => "13",
                "name" => "Old age Pension for Farmer",
                "short_name" => "oapfarmer",
                "dept_short_name" => "AGR",
            ),
            array(
                "id" => "17",
                "name" => "State Welfare Scheme for Purohits",
                "short_name" => "purohit",
                "dept_short_name" => "INCA",
            ),
            array(
                "id" => "19",
                "name" => "Legacy Old Age Pension for ST",
                "short_name" => "oapst",
                "dept_short_name" => "WBTDD",
            ),
            
        );
        foreach ($schemes as $scheme_item) {
            Scheme::create([
                'id'     => $scheme_item['id'],
                'name'     => strtoupper($scheme_item['name']),
                'short_name'     => $scheme_item['short_name'],
                'department_id'   => Department::where('short_name', $scheme_item['dept_short_name'])->firstOrFail()->id,
            ]);
        }
    }
}
