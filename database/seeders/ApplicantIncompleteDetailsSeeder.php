<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ApplicantIncompleteDetailsSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(IncompleteDetailsTestSeeder::class);
    }
}