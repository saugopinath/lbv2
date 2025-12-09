<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Codemaster;

class BackFromJBSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $codemasterParents = array(
            array(
                "name" => "Back From JB",
                "short_name" => "back_from_jb",
                "code" => "440",
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
                "name" => "Back From JB Verification Pending",
                "short_name" => "back_from_jb_verification_pending",
                "parent_short_code" => "back_from_jb",
                "code" => "4401",
            ),
            array(
                "name" => "Back From JB Verified",
                "short_name" => "back_from_jb_verified",
                "parent_short_code" => "back_from_jb",
                "code" => "4402",
            ),
            array(
                "name" => "Back From JB Approved",
                "short_name" => "back_from_jb_approved",
                "parent_short_code" => "back_from_jb",
                "code" => "4403",
            )
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
