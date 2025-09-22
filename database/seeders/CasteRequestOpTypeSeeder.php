<?php

namespace Database\Seeders;

use App\Models\Codemaster;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CasteRequestOpTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   
    public function run(): void
    {
         $codemasterParents = array(
            array(
                "name" => "OPTYPE",
                "short_name" => "op_type",
                "code" => "210",
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
                "name" => "REQUEST CASTE MODIFICATION",
                "short_name" => "request_caste_modification",
                "parent_short_code" => "op_type",
                "code" => "2106",
            ),
            array(
                "name" => "VERIFy CASTE MODIFICATION",
                "short_name" => "verify_cast_modification",
                "parent_short_code" => "op_type",
                "code" => "2107",
            ),
            array(
                "name" => "APPROVE CASTE MODIFICATION",
                "short_name" => "approve_cast_modification",
                "parent_short_code" => "op_type",
                "code" => "2108",
            ),
            array(
                "name" => "REVERT CASTE MODIFICATION",
                "short_name" => "revert_cast_modification",
                "parent_short_code" => "op_type",
                "code" => "2109",
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

