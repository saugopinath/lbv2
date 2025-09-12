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
            $this->command->error('BeneficiaryCommonList');
            return;
        }

        if (empty($incompleteTypes)) {
            $this->command->error('IncompletTypeModelMapping');
            return;
        }

        foreach (range(1200, 1220) as $i) {
            ApplicantIncompletDeatil::create([
                'application_id'        => $applicationIds[array_rand($applicationIds)],
                'beneficiary_id'        => null,
                'incomplet_type'        => $incompleteTypes[array_rand($incompleteTypes)],
                'next_level_request_id' => null,
                'new_value'             => null,
                'old_value'             => [
                    "ifsc"                => "BKID0004264",
                    "bank_account_number" => "1234567890123456",
                ],
                'request_id'            => null,
            ]);
        }

        $this->command->info('✅ Successfully seeded ApplicantIncompletDetail records.');
    }
}
