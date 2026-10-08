<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class NormalEntryPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $parent = Permission::firstOrCreate(
            ['name' => 'Normal Entry Permission', 'guard_name' => 'web'],
            [
                'parent_id'   => null,
                'description' => 'Parent group for permissions managing regular direct portal applications and routine scheme registration workflows.',
            ]
        );

        $childPermissions = [
            'Normal Entry Allow' => 'Enables data entry operators to fill and submit standard, non-camp beneficiary applications through regular scheme intake.',
            'Normal Entry Verification Allow' => 'Grants verifiers the authority to review, scrutinize, and verify routine scheme applications.',
            'Normal Entry Approver Allow' => 'Grants approving officers permission to sanction and approve regular beneficiary applications.',
            'Normal Entry Reject Allow' => 'Authorizes the rejection of regular intake applications that do not satisfy scheme eligibility criteria.',
            'Normal Entry Revert Allow' => 'Allows reviewers to revert routine applications back to the applicant or operator for document corrections.',
            'Bulk Actions Normal Entry Verification Allow' => 'Authorizes batch verification of multiple standard scheme intake applications simultaneously.',
            'Bulk Actions Normal Entry Approver Allow' => 'Authorizes batch approval and enrollment of multiple regular beneficiary applications.',
            'Bulk Actions Normal Entry Reject Allow' => 'Enables batch rejection of regular intake records failing verification parameters.',
            'Bulk Actions Normal Entry Revert Allow' => 'Enables batch reverting of regular applications back to data entry operators.',
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

        $this->command->info('✅ Normal Entry Permission and its child permissions seeded successfully!');
    }
}
