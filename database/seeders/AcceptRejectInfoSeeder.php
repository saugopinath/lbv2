<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AcceptRejectInfo;
use App\Models\BeneficiaryCommonList;
use App\Models\User;
use App\Models\Codemaster;

class AcceptRejectInfoSeeder extends Seeder
{
    public function run(): void
    {
        // Fetch required data
        $applicationIds = BeneficiaryCommonList::pluck('sourceable_id')->toArray();
        $opTypes = Codemaster::whereIn('code', [243, 244, 245, 246])->pluck('id')->toArray();
        $users = User::pluck('id')->toArray();

        // Check if required data is available
        if (empty($applicationIds)) {
            $this->command->warn('No records found in BeneficiaryCommonList. Skipping AcceptRejectInfo seeding.');
            return;
        }

        if (empty($opTypes)) {
            $this->command->warn('No records found in Codemaster with codes [243, 244, 245, 246]. Skipping AcceptRejectInfo seeding.');
            return;
        }

        if (empty($users)) {
            $this->command->warn('No records found in User. Skipping AcceptRejectInfo seeding.');
            return;
        }

        // Seed 4 records
        for ($i = 0; $i < 4; $i++) {
            AcceptRejectInfo::create([
                'application_id' => $applicationIds[array_rand($applicationIds)],
                'beneficiary_id' => null,
                'ip_address' => fake()->ipv4,
                'user_id' => $users[array_rand($users)],
                'browser' => fake()->userAgent,
                'model_name' => 'Beneficiary',
                'op_type' => $opTypes[array_rand($opTypes)],
            ]);
        }

        $this->command->info('Successfully seeded 4 records into AcceptRejectInfo.');
    }
}
