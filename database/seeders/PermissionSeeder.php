<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // --------------------------------------------------
        // 1️⃣ Role list (as per your public.roles table)
        // --------------------------------------------------
        $roles = [
            1 => 'Super Admin',
            2 => 'HOD',
            3 => 'Delegated HOD',
            4 => 'Approver',
            5 => 'Delegated Approver',
            6 => 'Verifier',
            7 => 'Delegated Verifier',
            8 => 'Operator',
            9 => 'Delegated Operator',
            10 => 'DDO',
            11 => 'Delegated DDO',
            12 => 'Mis User State',
            13 => 'Mis User District',
            14 => 'Mis User Block',
            15 => 'Mis User GP',
            16 => 'Mis User Sub Division',
            17 => 'Mis User Municipality',
            18 => 'Mis User Ward',
        ];

        foreach ($roles as $id => $name) {
            Role::firstOrCreate(['id' => $id], ['name' => $name, 'guard_name' => 'web']);
        }

        // --------------------------------------------------
        // 2️⃣ Permission list (as per your public.permissions table)
        // --------------------------------------------------
        $permissions = [            
            77 => 'update bank',
            78 => 'search bank update',
            79 => 'update mobile',
            81 => 'update bank details',
            82 => 'submit lb form',
            83 => 'view draft list',
            84 => 'edit draft',
            85 => 'view lb applications',
            87 => 'view beneficiaries',
            88 => 'revert incomplete',
            89 => 'manage role mappings',
            90 => 'create users',
            91 => 'create role mappings',
            92 => 'view offices',
            93 => 'view reports',
            94 => 'update caste',
            95 => 'edit caste',         
            98 => 'modify caste',
            99 => 'view caste modification list',
            100 => 'view incomplete applications',
            101 => 'view users',
            102 => 'update incomplete',
            103 => 'view verifier incomplete',
            104 => 'view approver incomplete',
            105 => 'view permission',
            106 => 'view user permission',
            107 => 'create offices',
        ];

        foreach ($permissions as $id => $name) {
            Permission::firstOrCreate(['id' => $id], ['name' => $name, 'guard_name' => 'web']);
        }

        // --------------------------------------------------
        // 3️⃣ Mapping: permission_id → role_id(s)
        // --------------------------------------------------
        $rolePermissions = [
            77 => [4, 5],
            78 => [4, 5],
            79 => [4, 5], 
            81 => [4, 5],           
            82 => [8, 9],  
            83 => [8, 9],  
            84 => [8, 9],  
            85 => [4, 5, 6, 7],    
            87 => [4,5,6,7,8,9,10,11],
            88 => [4, 5, 6, 7],
            89 => [1,6,7,4,5,2,3],
            90 => [1,2,3,4,5,6,7],
            91 => [1,2,3,4,5,6,7],
            92 => [1,2,3,4,5,6,7],
            93 => [4,5,6,7,8,9,10,11], 
            94 => [8, 9],
            98 => [8, 9], 
            95 => [8,9],
            99 => [4, 5, 6, 7],   
            102 => [4, 5, 6, 7],  
            103 => [6, 7],
            104 => [4, 5], 
            105 => [1,2,3,4,5,6,7],
            106 => [1,2,3,4,5,6,7], 
            107 => [1,2,3,4,5,6,7],           
        ];

        // --------------------------------------------------
        // 4️⃣ Insert into pivot: role_has_permissions
        // --------------------------------------------------
        foreach ($rolePermissions as $permissionId => $roleIds) {
            foreach ($roleIds as $roleId) {
                DB::table('role_has_permissions')->updateOrInsert([
                    'permission_id' => $permissionId,
                    'role_id' => $roleId,
                ]);
            }
        }

        // Give Super Admin all permissions automatically
        $superAdmin = Role::find(1);
        if ($superAdmin) {
            $superAdmin->syncPermissions(Permission::all());
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
