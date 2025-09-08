<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Codemaster;

class ValidationFailedCodemasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Parent records
        $codemasterParents = [
            [
                "name" => "VALIDATION FAILED",
                "short_name" => "validation_failed",
                "code" => "18",
            ],
            [
                "name" => "PAYMENT FAILED",
                "short_name" => "payment_failed",
                "code" => "19",
            ],
            [
                "name" => "ALLOWED VALIDATION PARAMETERS",
                "short_name" => "allowed_validation_parameters",
                "code" => "30",
            ],
        ];

        foreach ($codemasterParents as $parent) {
            Codemaster::firstOrCreate(
                ['short_name' => $parent['short_name']], // unique check
                [
                    'name'       => strtoupper($parent['name']),
                    'code'       => $parent['code'],
                    'short_name' => $parent['short_name'],
                ]
            );
        }

        // Child records
        $codemasterChilds = [
            [
                "name" => "Name Validation Failed",
                "short_name" => "name_validation_failed",
                "parent_short_code" => "validation_failed",
                "code" => "181",
            ],
            [
                "name" => "Account Validation Failed",
                "short_name" => "account_validation_failed",
                "parent_short_code" => "validation_failed",
                "code" => "182",
            ],
            [
                "name" => "MAJOR MISMATCH",
                "short_name" => "major_mismatch",
                "parent_short_code" => "allowed_validation_parameters",
                "code" => "201",
            ],
            [
                "name" => "MINOR MISMATCH",
                "short_name" => "minor_mismatch",
                "parent_short_code" => "allowed_validation_parameters",
                "code" => "202",
            ],
            [
                "name" => "KEEP SAME",
                "short_name" => "keep_same",
                "parent_short_code" => "allowed_validation_parameters",
                "code" => "203",
            ],
            [
                "name" => "REJECT",
                "short_name" => "reject",
                "parent_short_code" => "allowed_validation_parameters",
                "code" => "204",
            ],
        ];

        foreach ($codemasterChilds as $child) {
            $parent = Codemaster::where('short_name', $child['parent_short_code'])->firstOrFail();

            Codemaster::firstOrCreate(
                ['short_name' => $child['short_name']], // unique check
                [
                    'name'      => strtoupper($child['name']),
                    'code'      => $child['code'],
                    'short_name'=> $child['short_name'],
                    'parent_id' => $parent->id,
                ]
            );
        }
    }
}
