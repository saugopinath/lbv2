<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Codemaster;

class JNMPSeeder extends Seeder
{
    public function run(): void
    {
        // -----------------------------
        // PARENT ENTRY
        // -----------------------------
        $parent = Codemaster::firstOrCreate(
            [
                'short_name' => 'next_level_role_id'   // UNIQUE CHECK
            ],
            [
                'name'       => 'NEXT LEVEL ROLE ID',
                'code'       => '2',
            ]
        );

        // -----------------------------
        // CHILD ENTRY
        // -----------------------------
        Codemaster::firstOrCreate(
            [
                'short_name' => 'next_level_role_id_jnmp'  // UNIQUE CHECK
            ],
            [
                'name'             => 'NEXT LEVEL ROLE ID JNMP',
                'code'             => '2300',
                'parent_short_code'=> 'next_level_role_id',
                'parent_id'        => $parent->id,
            ]
        );
    }
}
