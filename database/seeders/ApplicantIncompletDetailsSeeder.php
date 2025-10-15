<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ApplicantIncompletDeatil;
use App\Models\BeneficiaryCommonList;
use App\Models\IncompletTypeModelMapping;

class ApplicantIncompletDetailsSeeder extends Seeder
{
    public function run(): void
    {


        $applicationIds = BeneficiaryCommonList::pluck('sourceable_id')->toArray();
        $incompleteTypes = IncompletTypeModelMapping::pluck('incomplet_type_code')->toArray();

        if (empty($applicationIds)) {
            $this->command->error('BeneficiaryCommonList is empty');
            return;
        }

        if (empty($incompleteTypes)) {
            $this->command->error('IncompletTypeModelMapping is empty');
            return;
        }

        foreach ($applicationIds as $applicationId) {

            $randomTypes = collect($incompleteTypes)->random(rand(2, 10));

            foreach ($randomTypes as $type) {

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
            }
        }

        $this->command->info('✅ Successfully seeded ApplicantIncompletDetail records.');
    }
}
