<?php

namespace Database\Seeders\SchemeFormFieldsResetSeeders;

use Illuminate\Database\Seeder;

class AllSchemeResetSeeders extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            FarmerSchemeFormFieldsResetSeeder::class,
            FishermanSchemeFormFieldsResetSeeder::class,
            JaiJoharSchemeFormFieldsResetSeeder::class,
            LPPPensionerSchemeFormFieldsResetSeeder::class,
            LPPRetainerSchemeFormFieldsResetSeeder::class,
            ManabikSchemeFormFieldsResetSeeder::class,
            MSMESchemeFormFieldsResetSeeder::class,
            OldAgePensionSchemeFormFieldsResetSeeder::class,
            StateWelfarePurohitSchemeFormFieldsResetSeeder::class,
            TapasiliBandhuSchemeFormFieldsResetSeeder::class,
            TextileSchemeFormFieldsResetSeeder::class,
            WidowPensionSchemeFormFieldsResetSeeder::class
        ]);
    }
}
