<?php

namespace Database\Seeders\AssignPermission;

use App\Models\Role;
use App\Models\User;
use App\Models\UserRoleSchemeOfficeMapping;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class VerifierPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'Normal Entry Verification Allow'                    => 'Grants verifiers the authority to review, scrutinize, and verify routine scheme applications.',
            'Normal Entry Reject Allow'                          => 'Authorizes the rejection of regular intake applications that do not satisfy scheme eligibility criteria.',
            'Normal Entry Revert Allow'                          => 'Allows reviewers to revert routine applications back to the applicant or operator for document corrections.',
            'view beneficiaries'                                 => 'Enables searching and viewing beneficiary records, application status, and personal details.',
            'view reports'                                       => 'Grants access to scheme summary reports, MIS dashboards, and operational performance metrics.',
            'view verifier incomplete'                           => 'Allows verifiers to view applications marked as incomplete or needing document clarifications.',
            'view caste modification list'                       => 'Permits viewing submitted requests for beneficiary caste category updates and corrections.',
            'view beneficiary details'                           => 'Authorizes viewing full beneficiary personal, bank, contact, and enclosure information.',
            'TakeActionForCaste'                                 => 'Authorizes taking review actions (verify, revert, reject) on submitted caste modification requests.',
            'VerifyCasteApplication'                             => 'Permits verifiers to validate and recommend caste certificate modifications for approval.',
            'RevertCasteApplication'                             => 'Allows reverting caste modification requests back to applicant or operator for clarification.',
            'lb-application-list'                                => 'Enables access to the processing queue of Lakshmir Bhandar applications for verification/approval.',
            'Bulk Actions Duare Sarkar Entry Verification Allow' => 'Enables bulk processing and batch verification of applications collected during Duare Sarkar camps.',
            'Bulk Actions Duare Sarkar Entry Reject Allow'       => 'Enables bulk rejection of non-qualifying records from Duare Sarkar outreach drives.',
            'Bulk Actions Duare Sarkar Entry Revert Allow'       => 'Enables bulk reverting of defective Duare Sarkar camp applications for rectification.',
            'Duare Sarkar Entry Reject Allow'                    => 'Permits rejection of ineligible or duplicate applications received during Duare Sarkar camps.',
            'Duare Sarkar Entry Verification Allow'              => 'Authorizes designated camp verifiers to authenticate and verify applications submitted under Duare Sarkar camps.',
            'Duare Sarkar Entry Revert Allow'                    => 'Allows reverting Duare Sarkar camp applications back to operators for missing documents or invalid data.',
            'Bulk Actions Normal Entry Revert Allow'             => 'Enables batch reverting of regular applications back to data entry operators.',
            'Bulk Actions Normal Entry Reject Allow'             => 'Enables batch rejection of regular intake records failing verification parameters.',
            'Bulk Actions Normal Entry Verification Allow'       => 'Authorizes batch verification of multiple standard scheme intake applications simultaneously.',
            'modify caste'                                       => 'Authorizes accessing the Caste Management module to process caste revision requests.',
            'back-from-jb'                                       => 'Authorizes reviewing applications returned from Jai Bangla portal for rectification.',
            'back-from-jb-verifier-button'                       => 'Enables verifier action buttons on applications returned from Jai Bangla.',
            'sarasori-mukhyamantri'                              => 'Grants access to Sarasori Mukhyamantri module for reviewing and handling citizen grievances.',
            'cmo-grievance-mark'                                 => 'Permits marking, updating, and linking beneficiary records against CMO grievance numbers.',
        ];

        // 1) find role
        try {
            $role = Role::findByName('Verifier');
        } catch (\Exception $e) {
            $this->command->error('Role "Verifier" not found. Seeder aborted.');

            return;
        }

        // Ensure permission records exist and collect Permission models
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
        // Get mappings for that role
        $mappings = UserRoleSchemeOfficeMapping::where('role_id', $role->id)->get();

        if ($mappings->isEmpty()) {
            $this->command->info('No users found in UserRoleSchemeOfficeMapping for role "Verifier".');

            return;
        }

        // 4) Loop mappings and assign permissions
        foreach ($mappings as $mapping) {
            $user = User::find($mapping->user_id);
            if (! $user) {
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

        $this->command->info('Give Permission To Verifier finished.');
    }
}
