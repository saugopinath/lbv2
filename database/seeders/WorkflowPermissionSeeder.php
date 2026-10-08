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
            [
                'parent_id'   => null,
                'description' => 'Parent group for core dynamic workflow permissions controlling application progression, approvals, reverts, and rejections across multi-stage schemes.',
            ]
        );

        $childPermissions = [
            'Entry Allow' => 'Authorizes operators to create, enter, and submit initial beneficiary applications within the dynamic workflow process.',
            'Verification Allow' => 'Authorizes verifier roles to inspect, validate, and verify submitted beneficiary applications before moving them to the approval stage.',
            'Approver Allow' => 'Authorizes approver authorities to give final approval and sanction beneficiary applications for scheme enrollment.',
            'Reject Allow' => 'Grants permission to reject ineligible or non-compliant beneficiary applications at assigned workflow stages.',
            'Revert Allow' => 'Enables authorized reviewers to revert applications back to previous workflow steps or operators for corrections and re-submission.',
            'Bulk Actions Verification Allow' => 'Permits batch verification of multiple beneficiary applications simultaneously in a single bulk operation.',
            'Bulk Actions Approver Allow' => 'Permits bulk sanction and approval of multiple verified beneficiary applications in a single action.',
            'Bulk Actions Reject Allow' => 'Permits bulk rejection of multiple beneficiary applications failing eligibility requirements.',
            'Bulk Actions Revert Allow' => 'Permits batch reverting of multiple incomplete applications back to the previous processing tier.',
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

        $this->command->info('✅ Workflow Permission and its child permissions seeded successfully!');
    }
}
