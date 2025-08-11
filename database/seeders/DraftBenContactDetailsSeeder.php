<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DraftBenContactDetails;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Monolog\Handler\NullHandler;

class DraftBenContactDetailsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DraftBenContactDetails::insert([
            [
                'application_id' => 134302617,
                'dist_code' => 305,
                'police_station' => 'Sonamukhi',
                'rural_urban_id' => 2,
                'block_ulb_code' => 2793,
                'block_ulb_name' => 'Sonamukhi',
                'block_ulb_type' => null,
                'gp_ward_code' => 108457,
                'gp_ward_name' => 'Panchal',
                'village_town_city' => 'Panchal',
                'house_premise_no' => '123/AB',
                'post_office' => 'Panchal',
                'pincode' => 722157,
                'residency_period' => null,
                'created_by_level' => 'Block',
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'created_by' => 32549,
                'ip_address' => '192.168.0.1',
                'created_by_dist_code' => 305,
                'created_by_local_body_code' => 2793,
                'ds_phase' => 8,
                'action_by' => null,
                'action_ip_address' => '192.168.0.1',
                'action_type' => null,
            ],
            [
                'application_id' => 134302618,
                'dist_code' => 305,
                'police_station' => 'Sonamukhi',
                'rural_urban_id' => 2,
                'block_ulb_code' => 2793,
                'block_ulb_name' => 'Sonamukhi',
                'block_ulb_type' => null,
                'gp_ward_code' => 108458,
                'gp_ward_name' => 'Gopalpur',
                'village_town_city' => 'Gopalpur',
                'house_premise_no' => '456/CD',
                'post_office' => 'Gopalpur',
                'pincode' => 722158,
                'residency_period' => null,
                'created_by_level' => 'Block',
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
                'created_by' => 32549,
                'ip_address' => '192.168.0.1',
                'created_by_dist_code' => 305,
                'created_by_local_body_code' => 2793,
                'ds_phase' => 8,
                'action_by' => null,
                'action_ip_address' => '192.168.0.1',
                'action_type' => null,
            ],
        ]);

    }
}
