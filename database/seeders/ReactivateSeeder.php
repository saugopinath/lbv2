<?php

namespace Database\Seeders;

use App\Models\Codemaster;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ReactivateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $codemasterParents = array(
            array(
                "name" => "Reactive Reason",
                "short_name" => "reactive_reason",
                "code" => "211",
            ),
        );
        foreach ($codemasterParents as $codemasterParent_item) {
            Codemaster::create([
                'name'     => strtoupper($codemasterParent_item['name']),
                'code'     => $codemasterParent_item['code'],
                'short_name'     => $codemasterParent_item['short_name'],
            ]);
        }
        $codemasterChilds = array(
            array(
                "name" => "Aadhaar in Lakshmir Bhandar is correct but may not be in Janma Mrityu Tathya",
                "short_name" => "aadhaar_in_lakshmir_bhandar_is_correct_but_may_not_be_in_janma_mrityu_tathya",
                "parent_short_code" => "reactive_reason",
                "code" => "2111",
            ),
            array(
                "name" => "Aadhaar in Lakshmir Bhandar is wrong",
                "short_name" => "aadhaar_in_lakshmir_bhandar_is_wrong",
                "parent_short_code" => "reactive_reason",
                "code" => "2112",
            ),
        );
        foreach ($codemasterChilds as $codemasterChild_item) {
            Codemaster::create([
                'name' => strtoupper($codemasterChild_item['name']),
                'code' => $codemasterChild_item['code'],
                'parent_short_code' => $codemasterChild_item['parent_short_code'],
                'short_name'     => $codemasterChild_item['short_name'],
                'parent_id'   => Codemaster::where('short_name', $codemasterChild_item['parent_short_code'])->firstOrFail()->id,
            ]);
        }
    }
}
