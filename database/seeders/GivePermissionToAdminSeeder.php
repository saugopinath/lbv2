<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\UserRoleSchemeOfficeMapping;

class GivePermissionToAdminSeeder extends Seeder
{
    // public function run(): void
    // {
    //     $permissions = [
    //         'RolePermissionManagement',
    //         'UserManagement',
    //         'DutyAssignManagement',
    //         'OfficeManagement',
    //     ];
    //     $role = Role::findByName('Super Admin');
    //     foreach ($permissions as $perm) {
    //         Permission::firstOrCreate(['name' => $perm]);
    //     }
    //     $admins = UserRoleSchemeOfficeMapping::where('role_id', $role->id)->with('user')->get()->pluck('user_id');

    //     foreach ($admins as $admin) {
    //         $admin->givePermissionTo($permissions);
    //     }
    // }
    public function run(): void
    {
        $permissions = [
            'RolePermissionManagement',
            'UserManagement',
            'DutyAssignManagement',
            'OfficeManagement',
        ];

        // 1) find role
        try {
            $role = Role::findByName('Super Admin');
        } catch (\Exception $e) {
            $this->command->error('Role "Super Admin" not found. Seeder aborted.');
            return;
        }

        // 2) Ensure permission records exist and collect Permission models
        $permissionModels = [];
        foreach ($permissions as $permName) {
            $permissionModels[] = Permission::firstOrCreate(
                ['name' => $permName],
                ['guard_name' => 'web']
            );
        }

        // 3) Get user_ids from mapping table for that role
        $adminUserIds = UserRoleSchemeOfficeMapping::where('role_id', $role->id)
            ->pluck('user_id')
            ->unique()
            ->values();

        if ($adminUserIds->isEmpty()) {
            $this->command->info('No users found in UserRoleSchemeOfficeMapping for role "Super Admin".');
            return;
        }

        // 4) Loop users and assign permissions, printing a message for each assign (or skip)
        foreach ($adminUserIds as $userId) {
            $user = User::find($userId);
            if (! $user) {
                $this->command->warn("User id={$userId} not found (skipping).");
                continue;
            }

            foreach ($permissionModels as $permission) {
                // check if user already has this permission
                if ($user->hasPermissionTo($permission->name)) {
                    $this->command->info("User id={$user->id} already has permission '{$permission->name}' (id={$permission->id}).");
                    continue;
                }

                // assign and print message
                $user->givePermissionTo($permission->name);
                $this->command->info("Assigned permission '{$permission->name}' (id={$permission->id}) to user id={$user->id}.");
            }
        }

        $this->command->info('GivePermissionToAdminSeeder finished.');
    }
}


