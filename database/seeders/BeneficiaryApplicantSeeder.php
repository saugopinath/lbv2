<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Ward;
use App\Models\Block;
use App\Models\District;
use App\Models\Panchayat;
use App\Models\Codemaster;
use App\Models\Subdivision;
use Faker\Factory as Faker;
use App\Models\Municipality;
use App\Models\Ifsccodemaster;
use App\Models\BeneficiaryBank;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BeneficiaryApplicantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        $userId = User::where('name', 'Admin')->value('id') ?? User::first()->id ?? 1;
        $districtIds = District::pluck('id')->toArray();
        $blockIds = Block::pluck('id')->toArray();
        $subdivisionIds = Subdivision::pluck('id')->toArray();
        $municipalityIds = Municipality::pluck('id')->toArray();
        $wardIds = Ward::pluck('id')->toArray();
        $panchayatIds = Panchayat::pluck('id')->toArray();


        $casteIds = Codemaster::where('parent_id', 1)->pluck('id')->toArray();
        $maritalStatusIds = Codemaster::where('parent_id', 3)->pluck('id')->toArray();
        $entryTypeId = Codemaster::where('code', 41)->value('id');
        $nextLevelRoleId = Codemaster::where('code', 23)->value('id');
        $fatherRelationTypeId = Codemaster::where('code', 131)->value('id');
        $ifsc = Ifsccodemaster::where('bankmaster_id', 36)->value('code');
        $motherRelationTypeId = Codemaster::where('code', 132)->value('id');
        $genderIds = Codemaster::where('parent_id', 2)->pluck('id')->toArray();


        if (empty($districtIds) || empty($blockIds) || empty($panchayatIds) || empty($casteIds) || empty($maritalStatusIds) || empty($genderIds)) {
            throw new \Exception('Required data in districts, blocks, panchayats, or codemasters is missing.');
        }

        $beneficiaries = [];
        for ($i = 1; $i <= 20; $i++) {
            $isRural = $faker->boolean;
            $districtId = $faker->randomElement($districtIds);


            $dsDate = $faker->optional()->dateTimeThisYear();
            $dsDateFormatted = $dsDate ? $dsDate->format('Y-m-d') : null;

            $beneficiaries[] = [
                'beneficiary_id' => $i,
                'district_id' => $districtId,
                'block_id' => $isRural ? $faker->randomElement($blockIds) : null,
                'sub_division_id' => $faker->optional()->randomElement($subdivisionIds),
                'municipality_id' => !$isRural ? $faker->randomElement($municipalityIds) : null,
                'ward_id' => !$isRural ? $faker->randomElement($wardIds) : null,
                'panchayat_id' => $isRural ? $faker->randomElement($panchayatIds) : null,
                'full_name' => $faker->name,
                'dob' => $faker->dateTimeBetween('-80 years', '-18 years')->format('Y-m-d'),
                'mobile_no' => $faker->phoneNumber,
                // 'gender' => $faker->randomElement($genderIds),
                'caste' => $faker->randomElement($casteIds),
                'next_level_role_id' => $nextLevelRoleId,
                'caste_certificate_no' => $faker->optional()->numerify('CC######'),
                'marital_status' => $faker->randomElement($maritalStatusIds),
                'entry_type' => $entryTypeId,
                'is_final_submit' => $faker->boolean,
                'is_faulty' => $faker->boolean,
                'ds_date' => $dsDateFormatted,
                'ds_registration_no' => $faker->optional()->numerify('DS######'),
                'created_by' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('lb_scheme.beneficiary_personals')->insert($beneficiaries);


        $beneficiaryData = DB::table('lb_scheme.beneficiary_personals')
            ->whereIn('application_id', array_column($beneficiaries, 'application_id'))
            ->get(['beneficiary_id', 'application_id', 'district_id', 'block_id', 'municipality_id', 'ward_id', 'panchayat_id'])
            ->keyBy('application_id')
            ->toArray();


        $contacts = [];
        foreach ($beneficiaryData as $appId => $beneficiary) {
            $isRural = !empty($beneficiary->block_id) && !empty($beneficiary->panchayat_id);

            $contacts[] = [
                'beneficiary_id' => $beneficiary->beneficiary_id,
                'application_id' => $appId,
                'district_id' => $beneficiary->district_id,
                'rural_urban_id' => $isRural ? 2 : 1,
                'block_id' => $isRural ? $beneficiary->block_id : null,
                'municipality_id' => !$isRural ? $beneficiary->municipality_id : null,
                'ward_id' => !$isRural ? $beneficiary->ward_id : null,
                'panchayat_id' => $isRural ? $beneficiary->panchayat_id : null,
                'police_station' => $faker->city,
                'village_town_city' => $faker->city,
                'house_premise_no' => $faker->buildingNumber,
                'post_office' => $faker->city,
                'pincode' => $faker->numerify('########'),
                'residency_period' => $faker->numberBetween(1, 50),
                'created_by' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }


        DB::table('lb_scheme.beneficiary_contacts')->insert($contacts);


        foreach ($beneficiaryData as $appId => $beneficiary) {

            BeneficiaryBank::create([
                'beneficiary_id'      => $beneficiary->beneficiary_id,
                'application_id'      => $appId,
                'created_by'          => $userId,
                'ifsc'                => $ifsc,
                'bank_account_number' => '123400045687524
                ' . str_pad($beneficiary->beneficiary_id, 4, '0', STR_PAD_LEFT),
            ]);
        }


        $relationships = [];
        foreach ($beneficiaryData as $appId => $beneficiary) {
            $relationships[] = [
                'beneficiary_id' => $beneficiary->beneficiary_id,
                'application_id'   => $i,
                'full_name' => $faker->name . ' (Father)',
                'relation_type_id' => $fatherRelationTypeId,
                'created_by' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $relationships[] = [
                'beneficiary_id' => $beneficiary->beneficiary_id,
                'application_id'   =>$i,
                'full_name' => $faker->name . ' (Mother)',
                'relation_type_id' => $motherRelationTypeId,
                'created_by' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }


        DB::table('lb_scheme.beneficiary_relationships')->insert($relationships);
    }
}
