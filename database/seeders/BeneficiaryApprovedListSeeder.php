<?php


namespace Database\Seeders;

use App\Models\Ifsccodemaster;
use Illuminate\Database\Seeder;
use App\Models\BeneficiaryPersonal;
use App\Models\BeneficiaryBank;
use App\Models\BeneficiaryContact;
use App\Models\Block;
use App\Models\Codemaster;
use App\Models\District;
use App\Models\OfficeMaster;
use App\Models\Panchayat;
use App\Models\UniqueAppBenId;
use App\Models\UserRoleSchemeOfficeMapping;

class BeneficiaryApprovedListSeeder extends Seeder
{
    // public function run(): void
    // {
    //     // Create a BeneficiaryPersonal
    //     $uniqueAppBenId = UniqueAppBenId::create([
    //         'application_id' => rand(1000, 9999),
    //         'beneficiary_id' => rand(1000, 9999),
    //     ]);
    //     // $dist = District::where('lgd_code', 318)->pluck('id')->first();
    //     $office = OfficeMaster::where('district_id', 318)->first();
    //     $mapping = UserRoleSchemeOfficeMapping::where('office_id', $office->id)->first();
    //     $user_id = $mapping->user_id;
    //     // dd($mapping);
    //     $office = OfficeMaster::find($mapping->office_id);
    //     $dist=$office->district_id;
    //     // dd($office);
    //     dd($dist);
    //     dd($user_id);
    //     $beneficiary = BeneficiaryPersonal::create([
    //         'application_id' => $uniqueAppBenId->application_id,
    //         'beneficiary_id' => $uniqueAppBenId->beneficiary_id,
    //         'district_id' => $dist,
    //         'full_name' => 'Test User',
    //         'dob' => '2000-01-01',
    //         'mobile_no' => '9999999999',
    //         'caste' => 1,
    //         'next_level_role_id' => 1,
    //         'marital_status' => 1,
    //         'entry_type' => 1,
    //         'is_final_submit' => true,
    //         'is_faulty' => false,
    //         'created_by' => $user_id,
    //         // add other required fields or set nullable fields to null
    //     ]);

    //     $beneficiary->lists()->create([
    //         // add any additional fields if present in your table
    //     ]);
    // }

    public function run(): void
    {
        for ($i = 0; $i < 5; $i++) {
            // Create a BeneficiaryPersonal
            

            $office = OfficeMaster::where('district_id', 318)->where('block_id', 2979)->first();
            $mapping = UserRoleSchemeOfficeMapping::where('office_id', $office->id)->first();
            $user_id = $mapping->user_id;
            $office = OfficeMaster::find($mapping->office_id);
            $dist = $office->district_id;
            $nextLevelRoleId = Codemaster::where('code', 23)->value('id');
            $casteId = Codemaster::where('code', 171)->value('id');
            $block_id = Block::where('district_id', $dist)->first()->value('id');
            $panchayat_id = Panchayat::where('block_id', $block_id)->first()->value('id');
            $user_block_id = $office->block_id;

            $user_sub_division_id = $office->sub_division_id;
            $user_municipality_id = $office->municipality_id;
            $user_ward_id = $office->ward_id;
            $user_panchayat_id = $office->panchayat_id;

            $uniqueAppBenId = UniqueAppBenId::create([ ]);
            $beneficiary_id_obj = UniqueAppBenId::where('application_id', $uniqueAppBenId->application_id)->first();
            // dd($uniqueAppBenId);
            // dd($beneficiary_id_obj->beneficiary_id);
            $beneficiary = BeneficiaryPersonal::create([
                'application_id' => $uniqueAppBenId->application_id,
                'beneficiary_id' => $beneficiary_id_obj->beneficiary_id,
                'district_id' => $dist,
                'block_id' => $user_block_id,
                'sub_division_id' => $user_sub_division_id,
                'municipality_id' => $user_municipality_id,
                'ward_id' => $user_ward_id,
                'panchayat_id' => $user_panchayat_id,
                'full_name' => 'Test User ' . ($i + 1),
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
            // dd($beneficiary);
            // $beneficiary->lists()->create([
            //     'district_id' => $beneficiary->district_id,
            //     'block_id' => $beneficiary->block_id,
            //     'sub_division_id' => $beneficiary->sub_division_id,
            //     'municipality_id' => $beneficiary->municipality_id,
            //     'ward_id' => $beneficiary->ward_id,
            //     'panchayat_id' => $beneficiary->panchayat_id,
            // ]);

            $beneficiary_contact = BeneficiaryContact::create([
                'beneficiary_id' => $beneficiary->beneficiary_id,
                'application_id' => $beneficiary->application_id,
                'district_id' => $dist,
                'rural_urban_id' => 2,
                'block_id' => $block_id,
                // 'sub_division_id' => $sub_division_id,
                // 'municipality_id' => $municipality_id,
                // 'ward_id' => $ward_id,
                'panchayat_id' => $panchayat_id,
                'police_station' => 'Test PS ' . $i,
                'village_town_city' => 'Village ' . $i,
                'house_premise_no' => 'House No ' . $i,
                'post_office' => 'Post Office ' . $i,
                'pincode' => '7000' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'residency_period' => rand(1, 10),
                'created_by' => $user_id,
            ]);
            $beneficiary_bank = BeneficiaryBank::create([
                'beneficiary_id' => $beneficiary->beneficiary_id,
                'application_id' => $beneficiary->application_id,
                'created_by' => $user_id,
                'ifsc' => Ifsccodemaster::where('id', 6712)->value('code'),
                'bank_account_number' => 'ACC' . str_pad($i, 4, '0', STR_PAD_LEFT),
            ]);
        }
    }
}
