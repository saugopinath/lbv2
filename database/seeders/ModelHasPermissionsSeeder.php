<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModelHasPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $rolePermissions = [
            77 => [4, 5],
            78 => [4, 5],
            79 => [4, 5],
            81 => [4, 5],
            82 => [8, 9],
            83 => [8, 9],
            84 => [8, 9],
            85 => [4, 5, 6, 7],
            87 => [4, 5, 6, 7, 8, 9, 10, 11],
            88 => [4, 5, 6, 7],
            89 => [1, 6, 7, 4, 5, 2, 3],
            90 => [1, 2, 3, 4, 5, 6, 7],
            91 => [1, 2, 3, 4, 5, 6, 7],
            92 => [1, 2, 3, 4, 5, 6, 7],
            93 => [4, 5, 6, 7, 8, 9, 10, 11],
            94 => [8, 9],
            95 => [8, 9],
            98 => [8, 9],
            99 => [4, 5, 6, 7],
            101 => [1, 2, 3, 4, 5, 6, 7],
            102 => [4, 5, 6, 7],
            103 => [6, 7],
            104 => [4, 5],
            105 => [1, 2, 3, 4, 5, 6, 7],
            106 => [1, 2, 3, 4, 5, 6, 7],
            107 => [1, 2, 3, 4, 5, 6, 7],
        ];

        $insertData = [];

        foreach ($rolePermissions as $permissionId => $roleIds) {
            foreach ($roleIds as $roleId) {
                $insertData[] = [
                    'permission_id' => $permissionId,
                    'model_type' => 'App\\Models\\User',
                    'model_id' => $roleId,
                ];
            }
        }

        DB::table('public.model_has_permissions')->insert($insertData);
    }
}
