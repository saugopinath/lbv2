<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Block;
use App\Models\Panchayat;
use App\Models\Codemaster;
use Faker\Factory as Faker;
use App\Models\Ifsccodemaster;
use App\Models\UniqueAppBenId;
use App\Models\BenRejectDetail;
use Illuminate\Database\Seeder;

class BenRejectDetailsSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $userId = User::where('name', 'Admin')->value('id');
        $blockId = Panchayat::where('id', 2485)->value('block_id');
        $districtId = Block::where('id', $blockId)->value('district_id');
        $entryTypeId = Codemaster::where('code', 41)->value('id');
        $nextLevelRoleId = Codemaster::where('code', 22)->value('id');
        $ifsc = Ifsccodemaster::where('bankmaster_id', 36)->value('code');

        for ($i = 1; $i <= 5; $i++) {
            $casteId = Codemaster::where('parent_id', 1)->inRandomOrder()->value('id');
            $maritalStatusId = Codemaster::where('parent_id', 3)->inRandomOrder()->value('id');

            $uniqueAppBenId = UniqueAppBenId::create([
                'beneficiary_id' => null,
            ]);

            BenRejectDetail::create([
                'application_id' => $uniqueAppBenId->application_id,
                'beneficiary_id' => null,
                'full_name' => $faker->name,
                'dob' => $faker->date('Y-m-d', '-20 years'),
                'mobile_no' => $faker->numerify('9#########'),
                'gender' => $faker->numberBetween(1, 3),
                'caste' => $casteId,
                'next_level_role_id' => $nextLevelRoleId,
                'caste_certificate_no' => $faker->optional()->numerify('CC#####'),
                'marital_status' => $maritalStatusId,
                'entry_type' => $entryTypeId,
                'is_final_submit' => false,
                'is_faulty' => false,
                'ds_date' => null,
                'ds_registration_no' => null,
                'created_by' => $userId,
                'father_full_name' => $faker->name('male'),
                'mother_full_name' => $faker->name('female'),
                'spouse_full_name' => $faker->name,
                'district_id' => $districtId,
                'rural_urban_id' => $faker->numberBetween(1, 2),
                'block_id' => $blockId,
                'municipality_id' => null,
                'ward_id' => null,
                'panchayat_id' => null,
                'police_station' => $faker->city,
                'village_town_city' => $faker->city,
                'house_premise_no' => $faker->buildingNumber,
                'post_office' => $faker->city,
                'pincode' => '7000' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'residency_period' => rand(1, 10),
                'ifsc' => $ifsc,
                'bank_account_number' => '1234000' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'encode_key' => $faker->md5,
                'aadhar_hash' => $faker->unique()->sha256,
                'encoded_aadhar' => $faker->md5,
                'document_type' => null,
                'attched_document' => null,
                'ip_address' => null,
                'document_extension' => null,
                'document_mime_type' => null,
                'av_status' => true,
                'rejected_reason' => $faker->randomElement([
                    'Documents not valid',
                    'Aadhaar mismatch',
                    'Incomplete application',
                    'Invalid bank details',
                    'Duplicate entry detected',
                ]),
                'earn_monthly_remuneration' => true,
                'info_genuine_decl' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
