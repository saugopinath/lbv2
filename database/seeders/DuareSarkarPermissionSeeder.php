<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class DuareSarkarPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $parent = Permission::firstOrCreate(
            ['name' => 'Duare Sarkar Entry Permission', 'guard_name' => 'web'],
            [
                'parent_id'   => null,
                'description' => 'Parent group for permissions governing special outreach camp applications under the Duare Sarkar initiative.',
            ]
        );

        $childPermissions = [
            'Duare Sarkar Entry Allow' => 'Enables camp-level operators to capture and register beneficiary applications collected during Duare Sarkar outreach camps.',
            'Duare Sarkar Entry Verification Allow' => 'Authorizes designated camp verifiers to authenticate and verify applications submitted under Duare Sarkar camps.',
            'Duare Sarkar Entry Approver Allow' => 'Authorizes designated camp approving officers to sanction and approve applications collected in Duare Sarkar camps.',
            'Duare Sarkar Entry Reject Allow' => 'Permits rejection of ineligible or duplicate applications received during Duare Sarkar camps.',
            'Duare Sarkar Entry Revert Allow' => 'Allows reverting Duare Sarkar camp applications back to operators for missing documents or invalid data.',
            'Bulk Actions Duare Sarkar Entry Verification Allow' => 'Enables bulk processing and batch verification of applications collected during Duare Sarkar camps.',
            'Bulk Actions Duare Sarkar Entry Approver Allow' => 'Enables batch approval and sanctioning of verified Duare Sarkar camp applications.',
            'Bulk Actions Duare Sarkar Entry Reject Allow' => 'Enables bulk rejection of non-qualifying records from Duare Sarkar outreach drives.',
            'Bulk Actions Duare Sarkar Entry Revert Allow' => 'Enables bulk reverting of defective Duare Sarkar camp applications for rectification.',
        ];

        foreach ($childPermissions as $permissionName => $description) {
            Permission::firstOrCreate(
                ['name' => $permissionName, 'guard_name' => 'web'],
                [
                    'parent_id'   => $parent->id,
                    'description' => $description,
                ]
            );
        }

        $this->command->info('✅ Duare Sarkar Permission and its child permissions seeded successfully!');
    }
}
