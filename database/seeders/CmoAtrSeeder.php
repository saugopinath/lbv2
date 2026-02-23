<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CmoAtrMaster;

class CmoAtrSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cmoatrmasters = [
            [
                "atr_desc" => "Beyond the administrative jurisdiction of this Office/ Deptt.",
                "atr_code" => "002",
                'can_find_applicant' => 1,
                'atn_id' => 2
            ],
            [
                "atr_desc" => "Repeated Complaint/already redressed",
                "atr_code" => "004",
                'can_find_applicant' => 1,
                'atn_id' => 4
            ],
            [
                "atr_desc" => "Benefit/Service provided",
                "atr_code" => "007",
                'can_find_applicant' => 1,
                'atn_id' => 6
            ],
            [
                "atr_desc" => "Matter taken up for expected resolution within 90 days",
                "atr_code" => "011",
                'can_find_applicant' => 1,
                'atn_id' => 9
            ],
            [
                "atr_desc" => "Pending for policy decision at department level",
                "atr_code" => "013",
                'can_find_applicant' => 1,
                'atn_id' => 10
            ],
            [
                "atr_desc" => "Matter taken up for expected resolution beyond 90 days",
                "atr_code" => "016",
                'can_find_applicant' => 1,
                'atn_id' => 12
            ],
            [
                "atr_desc" => "Complaint not sustained/not genuine/not admissible",
                "atr_code" => "000",
                'atn_id' => 1
            ],
            [
                "atr_desc" => "Subjudice Case",
                "atr_code" => "003",
                'atn_id' => 3
            ],
            [
                "atr_desc" => "Not eligible to get benefit/ service",
                "atr_code" => "006",
                'atn_id' => 5
            ],
            [
                "atr_desc" => "Complainant not found / unable or not willing to share information",
                "atr_code" => "009",
                'atn_id' => 7
            ],
            [
                "atr_desc" => "Beyond State Govt. purview",
                "atr_code" => "010",
                'atn_id' => 8
            ],
            [
                "atr_desc" => "Employment Prayer - processed/information provided",
                "atr_code" => "015",
                'atn_id' => 11
            ],
        ];

        foreach ($cmoatrmasters as $cmoatrmaster) {
            CmoAtrMaster::create([
                'atr_desc' => strtoupper($cmoatrmaster['atr_desc']),
                'atn_id' => $cmoatrmaster['atn_id'],
                'atr_code' => $cmoatrmaster['atr_code'],
                'can_find_applicant' => $cmoatrmaster['can_find_applicant'] ?? null,
            ]);
        }
    }
}
