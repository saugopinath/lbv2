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
use App\Models\UniqueAppBenId;
use Illuminate\Database\Seeder;
use App\Models\BeneficiaryAadhaar;
use App\Models\DraftBeneficiaryBank;
use Illuminate\Support\Facades\Crypt;
use App\Models\DraftBeneficiaryContact;
use App\Models\DraftBeneficiaryPersonal;
use App\Models\DraftBeneficiaryDeclaration;
use App\Models\DraftBeneficiaryRelationship;

class DraftApplicantSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        $userId = User::where('name', 'Admin')->value('id') ?? 1;

        $districtIds    = District::pluck('id')->toArray();
        $blockIds       = Block::pluck('id')->toArray();
        $subdivisionIds = Subdivision::pluck('id')->toArray();
        $municipalityIds= Municipality::pluck('id')->toArray();
        $wardIds        = Ward::pluck('id')->toArray();
        $panchayatIds   = Panchayat::pluck('id')->toArray();

        // Common Master IDs
        $entryTypeId        = Codemaster::where('code', 41)->value('id');
        $nextLevelRoleId    = Codemaster::where('code', 22)->value('id');
        $ifsc               = Ifsccodemaster::where('bankmaster_id', 36)->value('code');
        $fatherRelationTypeId = Codemaster::where('code', 131)->value('id');
        $motherRelationTypeId = Codemaster::where('code', 132)->value('id');

        for ($i = 1; $i <= 20; $i++) {

            $casteId         = Codemaster::where('parent_id', 1)->inRandomOrder()->value('id');
            $maritalStatusId = Codemaster::where('parent_id', 3)->inRandomOrder()->value('id');

            $aadharNumber = str_pad(rand(100000000000, 999999999999), 12, '0', STR_PAD_LEFT);

            // Random pick properly
            $districtId    = $faker->randomElement($districtIds);
            $blockId       = $faker->randomElement($blockIds);
            $subdivisionId = !empty($subdivisionIds) ? $faker->randomElement($subdivisionIds) : null;
            $municipalityId= !empty($municipalityIds) ? $faker->randomElement($municipalityIds) : null;
            $wardId        = !empty($wardIds) ? $faker->randomElement($wardIds) : null;
            $panchayatId   = !empty($panchayatIds) ? $faker->randomElement($panchayatIds) : null;

            // Unique Application ID generate
           $uniqueAppBenId = UniqueAppBenId::create([]);

            // Personal
            DraftBeneficiaryPersonal::create([
                'application_id'       => $uniqueAppBenId->application_id,
                'district_id'          => $districtId,
                'block_id'             => $blockId,
                'sub_division_id'      => $subdivisionId,
                'municipality_id'      => $municipalityId,
                'ward_id'              => $wardId,
                'panchayat_id'         => $panchayatId,
                'full_name'            => 'Test Beneficiary ' . $i,
                'dob'                  => '2000-01-01',
                'mobile_no'            => '99999999' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'caste'                => $casteId,
                'next_level_role_id'   => $nextLevelRoleId,
                'caste_certificate_no' => null,
                'marital_status'       => $maritalStatusId,
                'entry_type'           => $entryTypeId,
                'is_final_submit'      => false,
                'is_faulty'            => false,
                'ds_date'              => null,
                'ds_registration_no'   => null,
                'created_by'           => $userId,
            ]);

            // Contact
            DraftBeneficiaryContact::create([
                'application_id'    => $uniqueAppBenId->application_id,
                'district_id'       => $districtId,
                'rural_urban_id'    => 2,
                'block_id'          => $blockId,
                'municipality_id'   => $municipalityId,
                'ward_id'           => $wardId,
                'panchayat_id'      => $panchayatId,
                'police_station'    => 'Test PS ' . $i,
                'village_town_city' => 'Village ' . $i,
                'house_premise_no'  => 'House No ' . $i,
                'post_office'       => 'Post Office ' . $i,
                'pincode'           => '700157',
                'residency_period'  => rand(1, 10),
                'created_by'        => $userId,
            ]);

            // Bank
            DraftBeneficiaryBank::create([
                'application_id'     => $uniqueAppBenId->application_id,
                'created_by'         => $userId,
                'ifsc'               => $ifsc,
                'bank_account_number'=> '1234000' . str_pad($i, 4, '0', STR_PAD_LEFT),
            ]);

            // Declaration
            DraftBeneficiaryDeclaration::create([
                'application_id'          => $uniqueAppBenId->application_id,
                'created_by'              => $userId,
                'is_resident'             => true,
                'earn_monthly_remuneration'=> true,
                'info_genuine_decl'       => true,
                'av_status'               => true,
            ]);

            // Aadhaar
            BeneficiaryAadhaar::create([
                'application_id'   => $uniqueAppBenId->application_id,
                'beneficiary_id'   => null,
                'created_by'       => $userId,
                'encode_key'       => null,
                'encoded_aadhar'   => Crypt::encryptString($aadharNumber),
                'aadhar_hash'      => md5($aadharNumber),
            ]);

            // Relationships
            DraftBeneficiaryRelationship::insert([
                [
                    'application_id'   => $uniqueAppBenId->application_id,
                    'created_by'       => $userId,
                    'full_name'        => 'Father Name ' . $i,
                    'relation_type_id' => $fatherRelationTypeId,
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ],
                [
                    'application_id'   => $uniqueAppBenId->application_id,
                    'created_by'       => $userId,
                    'full_name'        => 'Mother Name ' . $i,
                    'relation_type_id' => $motherRelationTypeId,
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]
            ]);
        }
    }
}
