<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ifsccodemaster;
use App\Models\BeneficiaryPersonal;
use App\Models\BeneficiaryApprovedList;
use App\Models\BeneficiaryBank;
use App\Models\BeneficiaryContact;
use App\Models\BeneficiaryRelationship;
use App\Models\Block;
use App\Models\Codemaster;
use App\Models\District;
use App\Models\OfficeMaster;
use App\Models\Panchayat;
use App\Models\UniqueAppBenId;
use App\Models\UserRoleSchemeOfficeMapping;

class BeneficiaryApprovedListSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1200; $i <= 1220; $i++) {
            // Step 1: Unique Application/Beneficiary ID
            $uniqueAppBenId = UniqueAppBenId::create([
                'application_id' => $i,
                'beneficiary_id' => $i+1,
            ]);

            // Step 2: Office & User setup
            $office = OfficeMaster::where('district_id', 318)->first();
            $mapping = UserRoleSchemeOfficeMapping::where('office_id', $office->id)->first();
            $user_id = $mapping->user_id;
            $dist = $office->district_id;

            $nextLevelRoleId = Codemaster::where('code', 23)->value('id');
            $casteId = Codemaster::where('code', 17)->value('id');
            // $relationTypeId = Codemaster::where('code', 201)->value('id'); // উদাহরণ: relation type Father/Mother etc.
            $fatherRelationTypeId = Codemaster::where('code', 131)->value('id'); // Father
            $motherRelationTypeId = Codemaster::where('code', 132)->value('id'); // Mother

            $block_id = Block::where('district_id', $dist)->first()->id;
            $panchayat_id = Panchayat::where('block_id', $block_id)->first()->id;

            // Step 3: Beneficiary Personal
            $beneficiary = BeneficiaryPersonal::create([
                'application_id' => $uniqueAppBenId->application_id,
                'beneficiary_id' => $uniqueAppBenId->beneficiary_id,
                'district_id' => $dist,
                'block_id' => $office->block_id,
                'sub_division_id' => $office->sub_division_id,
                'municipality_id' => $office->municipality_id,
                'ward_id' => $office->ward_id,
                'panchayat_id' => $office->panchayat_id,
                'full_name' => 'Test User ' . $i,
                'dob' => '2000-01-01',
                'mobile_no' => '9999999999',
                'caste' => $casteId,
                'next_level_role_id' => $nextLevelRoleId,
                'marital_status' => 1,
                'entry_type' => 1,
                'is_final_submit' => true,
                'is_faulty' => false,
                'created_by' => $user_id,
            ]);

            // Step 4: Approved List (relation with beneficiary)
            $beneficiary->lists()->create([
                'district_id' => $beneficiary->district_id,
                'block_id' => $beneficiary->block_id,
                'sub_division_id' => $beneficiary->sub_division_id,
                'municipality_id' => $beneficiary->municipality_id,
                'ward_id' => $beneficiary->ward_id,
                'panchayat_id' => $beneficiary->panchayat_id,
            ]);

            // Step 5: Contact Info
            BeneficiaryContact::create([
                'beneficiary_id' => $beneficiary->beneficiary_id,
                'application_id' => $beneficiary->application_id,
                'district_id' => $dist,
                'rural_urban_id' => 2,
                'block_id' => $block_id,
                'panchayat_id' => $panchayat_id,
                'police_station' => 'Test PS ' . $i,
                'village_town_city' => 'Village ' . $i,
                'house_premise_no' => 'House No ' . $i,
                'post_office' => 'Post Office ' . $i,
                'pincode' => '7000' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'residency_period' => rand(1, 10),
                'created_by' => $user_id,
            ]);

            // Step 6: Bank Info
            BeneficiaryBank::create([
                'beneficiary_id' => $beneficiary->beneficiary_id,
                'application_id' => $beneficiary->application_id,
                'created_by' => $user_id,
                'ifsc' => Ifsccodemaster::where('id', 6712)->value('code'),
                'bank_account_number' => '12345678901123' . str_pad($i, 4, '0', STR_PAD_LEFT),
            ]);

            // ✅ Step 7: Relationship Insert
            BeneficiaryRelationship::create([
                'application_id'   => $beneficiary->application_id,
                'beneficiary_id'   => $beneficiary->beneficiary_id,
                'created_by'       => $user_id,
                'full_name'        => 'Father of ' . $beneficiary->full_name,
                'relation_type_id' => $fatherRelationTypeId,
            ]);

            BeneficiaryRelationship::create([
                'application_id'   => $beneficiary->application_id,
                'beneficiary_id'   => $beneficiary->beneficiary_id,
                'created_by'       => $user_id,
                'full_name'        => 'Mother of ' . $beneficiary->full_name,
                'relation_type_id' => $motherRelationTypeId,
            ]);
        }
    }
}
