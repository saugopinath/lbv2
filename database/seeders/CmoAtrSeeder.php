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
                "atr_code" => "1",
                'can_find_applicant' => 1,
            ],
            [
                "atr_desc" => "Repeated Complaint/already redressed",
                "atr_code" => "2",
                'can_find_applicant' => 1,
            ],
            [
                "atr_desc" => "Benefit/Service provided",
                "atr_code" => "3",
                'can_find_applicant' => 1,
            ],
            [
                "atr_desc" => "Matter taken up for expected resolution within 90 days",
                "atr_code" => "4",
                'can_find_applicant' => 1,
            ],
            [
                "atr_desc" => "Pending for policy decision at department level",
                "atr_code" => "5",
                'can_find_applicant' => 1,
            ],
            [
                "atr_desc" => "Matter taken up for expected resolution beyond 90 days",
                "atr_code" => "6",
                'can_find_applicant' => 1,
            ],
            [
                "atr_desc" => "Complaint not sustained/not genuine/not admissible",
                "atr_code" => "7",
            ],
            [
                "atr_desc" => "Subjudice Case",
                "atr_code" => "8",
            ],
            [
                "atr_desc" => "Not eligible to get benefit/ service",
                "atr_code" => "9",
            ],
            [
                "atr_desc" => "Complainant not found / unable or not willing to share information",
                "atr_code" => "10",
            ],
            [
                "atr_desc" => "Beyond State Govt. purview",
                "atr_code" => "11",
            ],
            [
                "atr_desc" => "Employment Prayer - processed/information provided",
                "atr_code" => "12",
            ],
        ];

        foreach ($cmoatrmasters as $cmoatrmaster) {
            CmoAtrMaster::create([
                'atr_desc' => strtoupper($cmoatrmaster['atr_desc']),
                'atr_code' => $cmoatrmaster['atr_code'],
                'can_find_applicant' => $cmoatrmaster['can_find_applicant'] ?? null,
            ]);
        }
    }
}
