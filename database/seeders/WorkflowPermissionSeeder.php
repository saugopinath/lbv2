<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class WorkflowPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $parent = Permission::firstOrCreate(
            ['name' => 'Workflow Permission', 'guard_name' => 'web'],
            ['parent_id' => null]
        );

        $childPermissions = [
            'Entry Allow',
            'Verification Allow',
            'Approver Allow',
            'Reject Allow',
            'Revert Allow',
        ];

        foreach ($childPermissions as $permissionName) {
            Permission::firstOrCreate(
                ['name' => $permissionName, 'guard_name' => 'web'],
                ['parent_id' => $parent->id]
            );
        }

        $this->command->info('✅ Workflow Permission and its child permissions seeded successfully!');
    }
}
