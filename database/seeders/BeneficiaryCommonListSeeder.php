<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BeneficiaryCommonList;
use App\Models\Ward;
use App\Models\Block;
use App\Models\District;
use App\Models\Panchayat;
use App\Models\Codemaster;
use App\Models\Subdivision;
use Faker\Factory as Faker;
use App\Models\Municipality;
use Illuminate\Support\Str;

class BeneficiaryCommonListSeeder extends Seeder
{
    public function run(): void
    {
          $faker = Faker::create();
        $districtIds    = District::pluck('id')->toArray();
        $blockIds       = Block::pluck('id')->toArray();
        $subdivisionIds = Subdivision::pluck('id')->toArray();
        $municipalityIds= Municipality::pluck('id')->toArray();
        $wardIds        = Ward::pluck('id')->toArray();
        $panchayatIds   = Panchayat::pluck('id')->toArray();

        for ($i = 1200; $i < 1220; $i++) {

            $districtId    = $faker->randomElement($districtIds);
            $blockId       = $faker->randomElement($blockIds);
            $subdivisionId = !empty($subdivisionIds) ? $faker->randomElement($subdivisionIds) : null;
            $municipalityId= !empty($municipalityIds) ? $faker->randomElement($municipalityIds) : null;
            $wardId        = !empty($wardIds) ? $faker->randomElement($wardIds) : null;
            $panchayatId   = !empty($panchayatIds) ? $faker->randomElement($panchayatIds) : null;

            BeneficiaryCommonList::create([
                'sourceable_id' => $i,
                'sourceable_type' => 'App\\Models\\Beneficiary',
                'district_id' => $districtId,
                'block_id' => $blockId,
                'sub_division_id' => $subdivisionId,
                'municipality_id' => $municipalityId,
                'ward_id' => $wardId,
                'panchayat_id' => $panchayatId,
            ]);
        }

        $this->command->info('Successfully seeded 10 records into lb_scheme.beneficiary_common_lists.');
    }
}

