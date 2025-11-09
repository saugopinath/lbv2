<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FaultyBeneficiaryPersonal;
use App\Models\UniqueAppBenId;
use App\Models\OfficeMaster;
use App\Models\Codemaster;
use App\Models\Block;
use App\Models\Panchayat;

class FaultyBeneficiaryPersonalSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1200; $i <= 1220; $i++) {
           

            // Step 2: office, district, block, panchayat setup
            $office = OfficeMaster::where('district_id', 318)->first();
            $dist   = $office->district_id;
            $block  = Block::where('district_id', $dist)->first();
            $panchayat = Panchayat::where('block_id', $block->id)->first();

            // Step 3: caste, next_level_role etc.
            $casteId          = Codemaster::where('code', 17)->value('id');   // OBC / SC / ST etc.
            $nextLevelRoleId  = Codemaster::where('code', 23)->value('id');   // Approver role
            
            $userId           = 1; 

            // Step 4: insert faulty beneficiary personal
            FaultyBeneficiaryPersonal::create([
                'application_id'     => $i,
                'beneficiary_id'     => $i+1,
                'district_id'        => $dist,
                'block_id'           => $block->id,
                'sub_division_id'    => $office->sub_division_id,
                'municipality_id'    => $office->municipality_id,
                'ward_id'            => $office->ward_id,
                'panchayat_id'       => $panchayat->id,
                'full_name'          => 'Faulty User ' . $i,
                'dob'                => '2001-01-01',
                'mobile_no'          => '9000000' . $i,
                'caste'              => $casteId,
                'next_level_role_id' => $nextLevelRoleId,
                'marital_status'     => 1,
                'entry_type'         =>1,
                'is_final_submit'    => false,
                'is_faulty'          => true,
                'created_by'         => $userId,
            ]);
        }
    }
}
