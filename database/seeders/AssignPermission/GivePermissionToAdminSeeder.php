<?php

namespace Database\Seeders\AssignPermission;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\UserRoleSchemeOfficeMapping;

use Spatie\Permission\PermissionRegistrar;

class GivePermissionToAdminSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'RolePermissionManagement'    => 'Grants full administrative access to create, edit, copy, and configure system roles and permission sets.',
            'UserManagement'              => 'Enables administration of user accounts, status management, login logs, and credential management.',
            'DutyAssignManagement'        => 'Authorizes assignment and management of operational duty contexts, role mappings, and office designations.',
            'OfficeManagement'            => 'Grants administrative privileges to configure, onboard, and maintain administrative office masters.',
            'create role mappings'        => 'Allows administrators to create new mappings between roles, offices, and schemes.',
            'manage role mappings'        => 'Enables viewing, modifying, and updating existing role-to-office-to-scheme mappings.',
            'view offices'                => 'Authorizes viewing the master list and organizational hierarchy of government offices.',
            'create offices'              => 'Permits creating and registering new office master records in the system hierarchy.',
            'view users'                  => 'Allows viewing registered user lists, user profiles, and assigned duty allocations.',
            'create users'                => 'Permits registration and creation of new user accounts across administrative tiers.',
            'view user permission'        => 'Authorizes viewing assigned permissions for specific user accounts and role profiles.',
            'view permission'             => 'Enables viewing and managing system permission definitions and configuration tables.',
            'master-tab'                  => 'Grants access to configure master tabs and dynamic form sections across scheme workflows.',
            'role-rank-management'        => 'Authorizes configuring role hierarchy, seniority ranks, and workflow progression sequences.',
            'define-workflow'             => 'Permits configuring dynamic multi-tier workflows, step labels, and stage routing rules.',
            'role-permission-management'  => 'Allows accessing the Role Permission Management dashboard to customize role assignments.',
            'scheme-capacity-setting'     => 'Authorizes configuring scheme capacity thresholds, quota allocations, and capacity actions.',
            'import-janma-mrityu-data'    => 'Permits importing vital birth and death registration data from Janma-Mrityu portal.',
            'dynamic-workflow-management' => 'Grants full control over dynamic workflow engine modules, stages, and transition rules.',
            'cmo-data-fetch'              => 'Authorizes synchronization and automated retrieval of grievance data from CMO portal.',
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
        foreach ($permissions as $permName => $desc) {
            $permissionModels[] = Permission::firstOrCreate(
                ['name' => $permName],
                [
                    'guard_name'  => 'web',
                    'description' => $desc,
                ]
            );
        }

        // 3) Get mappings for that role
        $mappings = UserRoleSchemeOfficeMapping::where('role_id', $role->id)->get();

        if ($mappings->isEmpty()) {
            $this->command->info('No users found in UserRoleSchemeOfficeMapping for role "Super Admin".');
            return;
        }

        // 4) Loop mappings and assign permissions
        foreach ($mappings as $mapping) {
            $user = User::find($mapping->user_id);
            if (!$user) {
                $this->command->warn("User id={$mapping->user_id} not found (skipping).");
                continue;
            }

            // Set the permissions team ID for this scheme
            app(PermissionRegistrar::class)->setPermissionsTeamId($mapping->scheme_id);

            foreach ($permissionModels as $permission) {
                // check if user already has this permission
                if ($user->hasPermissionTo($permission->name)) {
                    $this->command->info("User id={$user->id} already has permission '{$permission->name}' for scheme {$mapping->scheme_id} (id={$permission->id}).");
                    continue;
                }
                // assign and print message
                $user->givePermissionTo($permission->name);
                $this->command->info("Assigned permission '{$permission->name}' (id={$permission->id}) to user id={$user->id} for scheme {$mapping->scheme_id}.");
            }
        }
        // Reset the permissions team ID
        app(PermissionRegistrar::class)->setPermissionsTeamId(null);

        $this->command->info('GivePermissionToAdminSeeder finished.');
    }
}