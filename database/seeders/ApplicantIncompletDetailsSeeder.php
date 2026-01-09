<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ApplicantIncompletDeatil;
use App\Models\BeneficiaryCommonList;

class ApplicantIncompletDetailsSeeder extends Seeder
{
    public function run(): void
    {
        $applicationIds = BeneficiaryCommonList::pluck('sourceable_id')->toArray();

        if (empty($applicationIds)) {
            $this->command->error('❌ BeneficiaryCommonList is empty');
            return;
        }

        $combinations = [

            ['141'], // NO AADHAR
            ['142'], // NO MOBILE

            // Pair combinations
            ['141', '142'],              // NO AADHAR + NO MOBILE
            ['149', '142'],              // DUPLICATE AADHAR + NO MOBILE
            ['142', '1410'],             // NO MOBILE + DUPLICATE MOBILE
            ['1411', '1413'],            // DUPLICATE BANK + Mismatch(90–100)
            ['1411', '1412'],            // DUPLICATE BANK + Mismatch(40–89)
            ['1411', '146'],             // DUPLICATE BANK + ACCOUNT FAILED
            ['1411', '145'],
            ['1411'],
            ['146'],
            ['1412'],
            ['146'],
            ['145'],

            // 3–4 type combinations
            ['1411', '141', '142', '146'],          // BANK DUP + NO AADHAR + NO MOBILE + ACC FAILED
            ['1411', '141', '1410', '146'],         // BANK DUP + NO AADHAR + DUP MOBILE + ACC FAILED
            ['1411', '1410', '146'],         // BANK DUP + DUP MOBILE + ACC FAILED
        ];

        $count = 0;
        $index = 0;

        foreach ($applicationIds as $applicationId) {
            $combo = $combinations[$index % count($combinations)];

            foreach ($combo as $type) {
                $exists = ApplicantIncompletDeatil::where('application_id', $applicationId)
                    ->where('incomplet_type', $type)
                    ->exists();

                if ($exists) {
                    continue;
                }

                ApplicantIncompletDeatil::create([
                    'application_id'        => $applicationId,
                    'beneficiary_id'        => null,
                    'incomplet_type'        => $type,
                    'next_level_request_id' => null,
                    'new_value'             => null,
                    'old_value'             => [
                        "ifsc"                => "BKID0004264",
                        "bank_account_number" => "1234567890123456",
                    ],
                    'request_id'            => null,
                ]);

                $count++;
            }

            $index++;
        }

        $this->command->info("✅ Successfully seeded {$count} ApplicantIncompletDeatil records with all combinations.");
    }
}
