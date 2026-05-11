<?php

namespace Database\Seeders;

use App\Models\BeneficiaryAadhaar;
use App\Models\BeneficiaryBankDetail;
use App\Models\BeneficiaryContactDetail;
use App\Models\BeneficiaryEnclosure;
use App\Models\BeneficiaryPersonalDetail;
use App\Models\BeneficiarySelfDeclaration;
use App\Models\Block;
use App\Models\District;
use App\Models\OfficeMaster;
use App\Models\Panchayat;
use App\Models\Scheme;
use App\Models\UniqueAppBenId;
use App\Models\UserRoleSchemeOfficeMapping;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class BeneficiaryApprovedListSeeder extends Seeder
{
    public function run(): void
    {
        // $schemes = Scheme::pluck('id')->toArray();
        $schemes = [1, 2, 3, 5, 6, 7, 8, 9, 10, 11, 13, 17, 19, 20];

        if (empty($schemes)) {
            $this->command->error('No schemes found!');

            return;
        }

        try {

            for ($i = 0; $i < 1000; $i++) {
                // 🔹 Random Scheme
                $schemeId = $schemes[array_rand($schemes)];

                $districts = District::pluck('lgd_code')->toArray();
                $district = $districts[array_rand($districts)];
                $blocks = Block::where('district_id', $district)
                    ->pluck('lgd_code')->toArray();
                $block = $blocks[array_rand($blocks)];

                // Office & User তথ্য
                $office = OfficeMaster::where('district_id', $district)
                    ->where('block_id', $block)
                    ->first();
                if (!$office) {
                    $this->command->error("Office not found for District {$district}, Block {$block}.");

                    continue;
                }
                $mapping = UserRoleSchemeOfficeMapping::where('office_id', $office->id)
                    ->where('role_id', 8)
                    ->first();

                if (!$mapping) {
                    $this->command->error("User mapping not found for Office {$office->id}, Role 8.");

                    continue;
                }

                $user_id = $mapping->user_id;

                $dist = $office->district_id;
                $block_id = Block::where('district_id', $dist)
                    ->where('lgd_code', $block)
                    ->value('id');

                $panchayat_id = Panchayat::where('block_id', $block_id)
                    ->value('id');

                DB::beginTransaction();

                $unique = UniqueAppBenId::create([
                    'scheme_id' => $schemeId,
                ]);

                // 🔥 VERY IMPORTANT (PostgreSQL sequence refresh)
                $unique->refresh();

                $applicationId = $unique->application_id;
                $beneficiaryId = $unique->beneficiary_id;

                /*
                |--------------------------------------------------------------------------
                | Aadhaar
                |--------------------------------------------------------------------------
                */

                $aadharNumber = rand(100000000000, 999999999999);

                // dd($uniqueAppBenId);
                // dd($beneficiary_id_obj->beneficiary_id);
                $beneficiary_aadhar = BeneficiaryAadhaar::updateOrCreate([
                    'scheme_id' => $schemeId,
                    'application_id' => $applicationId,
                    'beneficiary_id' => $beneficiaryId,
                ], [
                    'encode_key' => null,
                    'encoded_aadhar' => Crypt::encryptString($aadharNumber),
                    'aadhar_hash' => md5($aadharNumber),
                ]);
                // dd($beneficiary_aadhar);
                $beneficiary = BeneficiaryPersonalDetail::updateOrCreate([
                    'scheme_id' => $schemeId,
                    'application_id' => $applicationId,
                    'beneficiary_id' => $beneficiaryId,
                ], [
                    'application_date' => now(),
                    'beneficiary_name' => "Test User $i",
                    'age' => rand(18, 65),
                    'dob' => '2000-01-01',
                    'mar_statu' => 1,
                    'caste' => 2,
                    'other_details' => [
                        'mobile_no' => (string) rand(6000000000, 9999999999),
                    ],
                    'next_level_role_id' => 2,
                    'is_final' => 1,
                    'created_by_dist_code' => $dist,
                    'ben_father_name' => 'Sanjoy',
                    'created_by_local_body_code' => $block,
                ]);

                $beneficiary_contact = BeneficiaryContactDetail::updateOrCreate([
                    'scheme_id' => $schemeId,
                    'application_id' => $applicationId,
                    'beneficiary_id' => $beneficiaryId,
                ], [
                    'state' => 'West Bengal',
                    'district_id' => $dist,
                    'rural_urban' => 2,
                    'blockurban' => $block_id,
                    'gpward' => $panchayat_id,
                    'villtowncity' => 'Village ' . $i,
                    'housepremiseno' => 'House ' . $i,
                    'postoffice' => 'Test PO',
                    'pincode' => '700001',
                ]);

                $beneficiary_self_declaration = BeneficiarySelfDeclaration::updateOrCreate([
                    'scheme_id' => $schemeId,
                    'application_id' => $applicationId,
                    'beneficiary_id' => $beneficiaryId,
                ], [
                    'other_details' => [
                        'declaration' => 'I hereby declare that all information provided is true.',
                        'accepted_terms' => true,
                        'submitted_by' => "Test User $i",
                    ],
                    'is_clean' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $beneficiary_bank = BeneficiaryBankDetail::updateOrCreate([
                    'scheme_id' => $schemeId,
                    'application_id' => $applicationId,
                    'beneficiary_id' => $beneficiaryId,
                ], [
                    'ifscode' => 'BKID0004264',
                    'bankname' => 'BANK OF INDIA',
                    'bank_branch_name' => 'PANCHAL',
                    'bankaccountnumber' => (string) rand(1000000000, 9999999999),
                ]);
                $docs = [
                    ['name' => 'aadhar_card_enc', 'type' => 104],
                    ['name' => 'caste_certificate_enc', 'type' => 101],
                    ['name' => 'bank_pass_book_enc', 'type' => 108],
                    ['name' => 'profile_photo_enc', 'type' => 103]
                ];

                $enclosures_success = true;
                foreach ($docs as $doc) {
                    // $filePath = 'D:\lbv2\public\images\background-cover.jpg';
                    // $fileContent = file_get_contents($filePath);
                    $base64File = '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CmVuZG9iagoKOSAwIG9iago8PC9UeXBlL0ZvbnQvU3VidHlwZS9UcnVlVHlwZS9CYXNlRm9udC9CQUFBQUErQXJpYWwtQm9sZE1UCi9GaXJzdENoYXIgMAovTGFzdENoYXIgMTEKL1dpZHRoc1s3NTAgNzIyIDYxMCA4ODkgNTU2IDI3NyA2NjYgNjEwIDMzMyAyNzcgMjc3IDU1NiBdCi9Gb250RGVzY3JpcHRvciA3IDAgUgovVG9Vbmljb2RlIDggMCBSCj4+CmVuZG9iagoKMTAgMCBvYmoKPDwKL0YxIDkgMCBSCj4+CmVuZG9iagoKMTEgMCBvYmoKPDwvRm9udCAxMCAwIFIKL1Byb2NTZXRbL1BERi9UZXh0XT4+CmVuZG9iagoKMSAwIG9iago8PC9UeXBlL1BhZ2UvUGFyZW50IDQgMCBSL1Jlc291cmNlcyAxMSAwIFIvTWVkaWFCb3hbMCAwIDU5NSA4NDJdL0dyb3VwPDwvUy9UcmFuc3BhcmVuY3kvQ1MvRGV2aWNlUkdCL0kgdHJ1ZT4+L0NvbnRlbnRzIDIgMCBSPj4KZW5kb2JqCgoxMiAwIG9iago8PC9Db3VudCAxL0ZpcnN0IDEzIDAgUi9MYXN0IDEzIDAgUgo+PgplbmRvYmoKCjEzIDAgb2JqCjw8L1RpdGxlPEZFRkYwMDQ0MDA3NTAwNkQwMDZEMDA3OTAwMjAwMDUwMDA0NDAwNDYwMDIwMDA2NjAwNjkwMDZDMDA2NT4KL0Rlc3RbMSAwIFIvWFlaIDU2LjcgNzczLjMgMF0vUGFyZW50IDEyIDAgUj4+CmVuZG9iagoKNCAwIG9iago8PC9UeXBlL1BhZ2VzCi9SZXNvdXJjZXMgMTEgMCBSCi9NZWRpYUJveFsgMCAwIDU5NSA4NDIgXQovS2lkc1sgMSAwIFIgXQovQ291bnQgMT4+CmVuZG9iagoKMTQgMCBvYmoKPDwvVHlwZS9DYXRhbG9nL1BhZ2VzIDQgMCBSCi9PdXRsaW5lcyAxMiAwIFIKPj4KZW5kb2JqCgoxNSAwIG9iago8PC9BdXRob3I8RkVGRjAwNDUwMDc2MDA2MTAwNkUwMDY3MDA2NTAwNkMwMDZGMDA3MzAwMjAwMDU2MDA2QzAwNjEwMDYzMDA2ODAwNkYwMDY3MDA2OTAwNjEwMDZFMDA2RTAwNjkwMDczPgovQ3JlYXRvcjxGRUZGMDA1NzAwNzIwMDY5MDA3NDAwNjUwMDcyPgovUHJvZHVjZXI8RkVGRjAwNEYwMDcwMDA2NTAwNkUwMDRGMDA2NjAwNjYwMDY5MDA2MzAwNjUwMDJFMDA2RjAwNzIwMDY3MDAyMDAwMzIwMDJFMDAzMT4KL0NyZWF0aW9uRGF0ZShEOjIwMDcwMjIzMTc1NjM3KzAyJzAwJyk+PgplbmRvYmoKCnhyZWYKMCAxNgowMDAwMDAwMDAwIDY1NTM1IGYgCjAwMDAwMTE5OTcgMDAwMDAgbiAKMDAwMDAwMDAxOSAwMDAwMCBuIAowMDAwMDAwMjI0IDAwMDAwIG4gCjAwMDAwMTIzMzAgMDAwMDAgbiAKMDAwMDAwMDI0NCAwMDAwMCBuIAowMDAwMDExMTU0IDAwMDAwIG4gCjAwMDAwMTExNzYgMDAwMDAgbiAKMDAwMDAxMTM2OCAwMDAwMCBuIAowMDAwMDExNzA5IDAwMDAwIG4gCjAwMDAwMTE5MTAgMDAwMDAgbiAKMDAwMDAxMTk0MyAwMDAwMCBuIAowMDAwMDEyMTQwIDAwMDAwIG4gCjAwMDAwMTIxOTYgMDAwMDAgbiAKMDAwMDAxMjQyOSAwMDAwMCBuIAowMDAwMDEyNDk0IDAwMDAwIG4gCnRyYWlsZXIKPDwvU2l6ZSAxNi9Sb290IDE0IDAgUgovSW5mbyAxNSAwIFIKL0lEIFsgPEY3RDc3QjNEMjJCOUY5MjgyOUQ0OUZGNUQ3OEI4RjI4Pgo8RjdENzdCM0QyMkI5RjkyODI5RDQ5RkY1RDc4QjhGMjg+IF0KPj4Kc3RhcnR4cmVmCjEyNzg3CiUlRU9GCg==';
                    $base64File_1 = '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';
                    $beneficiary_enclosure = BeneficiaryEnclosure::updateOrCreate([
                        'scheme_id' => $schemeId,
                        'beneficiary_id' => $beneficiaryId,
                        'application_id' => $applicationId,
                        'attched_document' => ($doc['type'] == '103') ? $base64File_1 : $base64File,
                        'ip_address' => '127.0.0.1',
                        'document_extension' => 'jpg',
                        'document_mime_type' => 'image/jpeg',
                        'document_type' => $doc['type'],
                        'created_by' => $user_id,
                    ]);
                    if (!$beneficiary_enclosure) {
                        $enclosures_success = false;
                        break;
                    }
                }
                if ($unique && $beneficiary_aadhar && $beneficiary && $beneficiary_contact && $beneficiary_bank && $enclosures_success && $beneficiary_self_declaration) {
                    DB::commit();
                } else {
                    DB::rollBack();
                    $this->command->error('Failed to insert beneficiary.');
                }
            }
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}