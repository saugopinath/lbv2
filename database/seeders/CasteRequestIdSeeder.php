<?php

namespace Database\Seeders;

use App\Models\Codemaster;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CasteRequestIdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $codemasterParents = array(
            array(
                "name" => "Caste Request Status",
                "short_name" => "caste_request_status",
                "code" => "220",
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
                "name" => "INITIAL UPDATE CASTE REQUEST",
                "short_name" => "initial_update_request",
                "parent_short_code" => "caste_request_status",
                "code" => "2201",
            ),
            array(
                "name" => "VERIFIED CASTE REQUEST",
                "short_name" => "verified_cast_request",
                "parent_short_code" => "caste_request_status",
                "code" => "2202",
            ),
            array(
                "name" => "APPROVED CASTE REQUEST",
                "short_name" => "approved_cast_request",
                "parent_short_code" => "caste_request_status",
                "code" => "2203",
            ),
            array(
                "name" => "REVERT CASTE REQUEST",
                "short_name" => "revert_cast_request",
                "parent_short_code" => "caste_request_status",
                "code" => "2204",
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
