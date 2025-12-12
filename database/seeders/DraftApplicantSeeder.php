<?php

namespace Database\Seeders;

use App\Models\BeneficiaryAadhaar;
use App\Models\Ifsccodemaster;
use Illuminate\Database\Seeder;
use App\Models\DraftBeneficiaryPersonal;
use App\Models\DraftBeneficiaryBank;
use App\Models\DraftBeneficiaryContact;
use App\Models\DraftBeneficiaryRelationship;
use App\Models\BeneficiaryEnclosure;
use App\Models\OfficeMaster;
use App\Models\Panchayat;
use App\Models\UniqueAppBenId;
use App\Models\UserRoleSchemeOfficeMapping;
use App\Models\Block;
use App\Models\Codemaster;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class DraftApplicantSeeder extends Seeder
{
    public function run(): void
    {
        try {

            for ($i = 0; $i < 200; $i++) {

                /** --------------------------
                 * Load Office + User
                 * -------------------------- */
                $office = OfficeMaster::where('district_id', 318)
                    ->where('block_id', 2979)->first();

                $mapping = UserRoleSchemeOfficeMapping::where('office_id', $office->id)
                    ->where('role_id', 8)->first();

                $user_id = $mapping->user_id;

                $office = OfficeMaster::find($mapping->office_id);
                $dist   = $office->district_id;

                /** --------------------------
                 * STATIC MASTER VALUES
                 * -------------------------- */
                $nextLevelRoleId      = Codemaster::where('code', 22)->value('id');
                $casteId              = Codemaster::where('code', 171)->value('id');
                $fatherRelationTypeId = Codemaster::where('code', 131)->value('id');
                $motherRelationTypeId = Codemaster::where('code', 132)->value('id');

                $block_id     = Block::where('district_id', $dist)
                    ->where('lgd_code', 2979)->value('id');

                $panchayat_id = Panchayat::where('block_id', $block_id)->value('id');

                $aadhar_number  = rand(100000000000, 999999999999);
                $encoded_aadhar = Crypt::encryptString((string) $aadhar_number);


                /** --------------------------
                 * BEGIN TRANSACTION
                 * -------------------------- */
                DB::beginTransaction();

                /** --------------------------
                 * Unique Application ID
                 * -------------------------- */
                $uniqueAppBenId = UniqueAppBenId::create([]);
                $beneficiary_id_obj = UniqueAppBenId::where('application_id', $uniqueAppBenId->application_id)->first();
                // $beneficiary_id = $unique->beneficiary_id;



                /** --------------------------
                 * Aadhaar Save
                 * -------------------------- */
                $beneficiary_aadhar = BeneficiaryAadhaar::create([
                    'application_id'   => $uniqueAppBenId->application_id,
                    'beneficiary_id'   => $uniqueAppBenId->beneficiary_id,
                    'created_by'     => $user_id,
                    'encode_key'     => null,
                    'encoded_aadhar' => $encoded_aadhar,
                    'aadhar_hash'    => md5($aadhar_number),
                ]);

                /** --------------------------
                 * PERSONAL
                 * -------------------------- */
                $beneficiary = DraftBeneficiaryPersonal::create([
                    'application_id' => $uniqueAppBenId->application_id,
                    'beneficiary_id' => $beneficiary_id_obj->beneficiary_id,
                    'district_id'         => $office->district_id,
                    'block_id'            => $office->block_id,
                    'sub_division_id'     => $office->sub_division_id,
                    'municipality_id'     => $office->municipality_id,
                    'ward_id'             => $office->ward_id,
                    'panchayat_id'        => $office->panchayat_id,
                    'full_name'           => 'Test User ' . ($i + 1),
                    'dob'                 => '2000-01-01',
                    'mobile_no'           => '9999999999',
                    'caste'               => $casteId,
                    'next_level_role_id'  => $nextLevelRoleId,
                    'marital_status'      => 1,
                    'entry_type'          => 1,
                    'is_final_submit'     => true,
                    'is_faulty'           => false,
                    'created_by'          => $user_id,
                ]);

                /** ------------------------------------
                 * FIXED Relationship (PostgreSQL SAFE)
                 * ------------------------------------ */
                DraftBeneficiaryRelationship::insert([
                    [
                        'application_id' => $uniqueAppBenId->application_id,
                        // 'beneficiary_id' => $beneficiary_id_obj->beneficiary_id,
                        'created_by'       => $user_id,
                        'full_name'        => 'Father Name ' . $i,
                        'relation_type_id' => $fatherRelationTypeId,
                        'created_at'       => now(),
                        'updated_at'       => now(),
                    ],
                    [
                        'application_id' => $uniqueAppBenId->application_id,
                        // 'beneficiary_id' => $beneficiary_id_obj->beneficiary_id,
                        'created_by'       => $user_id,
                        'full_name'        => 'Mother Name ' . $i,
                        'relation_type_id' => $motherRelationTypeId,
                        'created_at'       => now(),
                        'updated_at'       => now(),
                    ]
                ]);


                /** --------------------------
                 * CONTACT
                 * -------------------------- */
                $beneficiary_contact = DraftBeneficiaryContact::create([
                    'application_id' => $beneficiary->application_id,
                    'district_id'        => $dist,
                    'rural_urban_id'     => 2,
                    'block_id'           => $block_id,
                    'panchayat_id'       => $panchayat_id,
                    'police_station'     => 'Test PS ' . $i,
                    'village_town_city'  => 'Village ' . $i,
                    'house_premise_no'   => 'House No ' . $i,
                    'post_office'        => 'Post Office ' . $i,
                    'pincode'            => '700000',
                    'residency_period'   => rand(1, 10),
                    'created_by'         => $user_id,
                ]);


                /** --------------------------
                 * BANK
                 * -------------------------- */
                $beneficiary_bank = DraftBeneficiaryBank::create([
                    // 'beneficiary_id' => $beneficiary->beneficiary_id,
                    'application_id' => $beneficiary->application_id,
                    'created_by'           => $user_id,
                    'ifsc'                 => Ifsccodemaster::where('id', 6712)->value('code'),
                    'bank_account_number'  => 'ACC' . str_pad($i, 4, '0', STR_PAD_LEFT),
                ]);

                /** --------------------------
                 * DOCUMENTS
                 * -------------------------- */
                $docs = [
                    ['type' => 104],
                    ['type' => 101],
                    ['type' => 108],
                ];

                foreach ($docs as $doc) {

                    BeneficiaryEnclosure::create([
                         'beneficiary_id'     => $beneficiary->beneficiary_id,
                        'application_id'     => $beneficiary->application_id,
                        'attched_document'   => 'sample-base64',
                        'ip_address'         => '127.0.0.1',
                        'document_extension' => 'jpg',
                        'document_mime_type' => 'image/jpeg',
                        'document_type'      => $doc['type'],
                        'created_by'         => $user_id,
                    ]);
                }


                /** --------------------------
                 * FINAL COMMIT
                 * -------------------------- */
                DB::commit();
                $this->command->info("Draft Beneficiary inserted successfully" . $i);
            }
        } catch (\Exception $e) {

            DB::rollBack();
            throw $e;
        }
    }
}
