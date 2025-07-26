<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $roles = [
            'Super Admin',
            'HOD',
            'HOP',
            'Dashboard',
            'AuditOfficer',
            'MisState',
            'SpecialStatusCheck',
            'StatusChecker',
            'DDO',
            'Delegated DDO',
            'Approver',
            'Delegated Approver',
            'Verifier',
            'Delegated Verifier',
            'Operator',
        ];

     

     

        $permissions = [
            'Duty Management',
            'Applicantion Form Entry',
            'Applicantion Form Update',
            'Applicantion Form Verification',
            'Applicantion Form Approval',
            //User Resource
            'create user resource',
            'read user resource',
            'update user resource',
            'delete user resource',
            //Role
            'create role',
            'read role',
            'update role',
            'delete role',
            //Permission
            'create permission',
            'read permission',
            'update permission',
            'delete permission',
            //Role Permission
            'create role permission',
            'read role permission',
            'update role permission',
            'delete role permission',
            //User Role
            'create user role',
            'read user role',
            'update user role',
            'delete user role',
            //User Permission
            'create user permission',
            'read user permission',
            'update user permission',
            'delete user permission',
            //State
            'create state',
            'read state',
            'update state',
            'delete state',
            //District
            'create district',
            'read district',
            'update district',
            'delete district',
            //Sub Division
            'create sub division',
            'read sub division',
            'update sub division',
            'delete sub division',
            //Block
            'create block',
            'read block',
            'update block',
            'delete block',
            //Municipality
            'create municipality',
            'read municipality',
            'update municipality',
            'delete municipality',
        ];

        $role_permission_map = [
            'Super Admin'           => [
                //Role
                'create role',
                'read role',
                'update role',
                'delete role',
                //Permission
                'create permission',
                'read permission',
                'update permission',
                'delete permission',
                //Role Permission
                'create role permission',
                'read role permission',
                'update role permission',
                'delete role permission',
            ],
            
        ];

        $rolesArr = collect($roles)->map(function ($role) {
            return [
                'name' => $role,
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now()
            ];
        })->toArray();

        $permissionsArr = collect($permissions)->map(function ($permission) {
            return [
                'name' => $permission,
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now()
            ];
        })->toArray();

        // Insert all roles
        Role::insert($rolesArr);

        // Insert all permissions
        Permission::insert($permissionsArr);

        foreach (Role::all() as $role) {
            
            $role->save();
        }

        foreach ($role_permission_map as $role_name => $permissions) {
            $role = Role::where('name', $role_name)->first();

           // $role = Role::findByName($role_name);
            $role->givePermissionTo($permissions);
        }
    }
}
