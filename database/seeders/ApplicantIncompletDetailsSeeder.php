<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\ApplicantIncompletDeatil;
use App\Models\BeneficiaryCommonList;
use App\Models\IncompletTypeModelMapping;
use App\Models\AcceptRejectInfo;

class ApplicantIncompletDetailsSeeder extends Seeder
{
    public function run(): void
    {
        // Fetch IDs from the required tables
        $applicationIds = BeneficiaryCommonList::pluck('sourceable_id')->toArray();
        $incompleteTypes = IncompletTypeModelMapping::pluck('incomplet_type_code')->toArray();
        $requestIds = AcceptRejectInfo::pluck('id')->toArray();

        // Check if any of the required data is missing
        if (empty($applicationIds)) {
            $this->command->warn('No records found in BeneficiaryCommonList. Skipping ApplicantIncompletDetails seeding.');
            return;
        }

        if (empty($incompleteTypes)) {
            $this->command->warn('No records found in IncompletTypeModelMapping. Skipping ApplicantIncompletDetails seeding.');
            return;
        }

        if (empty($requestIds)) {
            $this->command->warn('No records found in AcceptRejectInfo. Skipping ApplicantIncompletDetails seeding.');
            return;
        }

        // Seed 10 records if all required data is available
        foreach (range(1200, 1220) as $i) {
            ApplicantIncompletDeatil::create([
                'application_id'        => $applicationIds[array_rand($applicationIds)],
                'beneficiary_id'        => null,
                'incomplet_type'        => $incompleteTypes[array_rand($incompleteTypes)],
                'next_level_request_id' => null,
                'new_value'             => null,
                'old_value'             => null,
                'request_id'            => $requestIds[array_rand($requestIds)],
            ]);
        }

        $this->command->info('Successfully seeded 10 records into ApplicantIncompletDeatil.');
    }
}
