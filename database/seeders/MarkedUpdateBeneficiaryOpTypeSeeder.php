<?php

namespace Database\Seeders;

use App\Models\Codemaster;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MarkedUpdateBeneficiaryOpTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $codemasterChilds = [
            [
                "name" => "Marked Update Beneficiary",
                "short_name" => "marked_update_beneficiary",
                "parent_short_code" => "op_type",
                "code" => "2110",
            ],
        ];

        foreach ($codemasterChilds as $item) {

            $parent = Codemaster::where('short_name', $item['parent_short_code'])
                ->firstOrFail();
            Codemaster::updateOrCreate(
                [
                    'code' => $item['code'],
                ],
                [
                    'name'              => strtoupper($item['name']),
                    'short_name'        => $item['short_name'],
                    'parent_short_code' => $item['parent_short_code'],
                    'parent_id'         => $parent->id,
                ]

            );
        }
    }
}
