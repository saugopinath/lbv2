<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ApplicantIncompletDeatil;
use App\Models\BeneficiaryPersonalDetail;

class ApplicantIncompletDetailsSeeder extends Seeder
{
    public function run(): void
    {
        $beneficiaries = BeneficiaryPersonalDetail::select('application_id','scheme_id')->get();

        if ($beneficiaries->isEmpty()) {
            $this->command->error('BeneficiaryPersonalDetail is empty');
            return;
        }

        $combinations = [
            ['141'],
            ['142'],
            ['141','142'],
            ['149','142'],
            ['142','1410'],
            ['1411','1413'],
            ['1411','1412'],
            ['1411','146'],
            ['1411','145'],
            ['1411'],
            ['146'],
            ['1412'],
            ['145'],
            ['1411','141','142','146'],
            ['1411','141','1410','146'],
            ['1411','1410','146'],
        ];

        $inserted = 0;
        $comboIndex = 0;

        while ($inserted < 200) {

            $beneficiary = $beneficiaries->random();

            $applicationId = $beneficiary->application_id;
            $schemeId = $beneficiary->scheme_id;

            $combo = $combinations[$comboIndex % count($combinations)];

            foreach ($combo as $type) {

                if ($inserted >= 200) {
                    break;
                }

                $exists = ApplicantIncompletDeatil::where('application_id', $applicationId)
                    ->where('incomplet_type', $type)
                    ->exists();

                if ($exists) {
                    continue;
                }

                ApplicantIncompletDeatil::updateOrCreate([
                    'scheme_id'             => $schemeId,
                    'application_id'        => $applicationId,
                    'beneficiary_id'        => null,
                    'incomplet_type'        => $type,
                    'next_level_request_id' => null,
                    'new_value'             => null,
                    'old_value'             => [
                        "ifsc" => "BKID0004264",
                        "bank_account_number" => "1234567890123456",
                    ],
                    'request_id'            => null,
                ]);

                $inserted++;
            }

            $comboIndex++;
        }
       
    }
}