<?php

namespace Database\Seeders\AssignPermission;

use App\Models\Role;
use App\Models\User;
use App\Models\UserRoleSchemeOfficeMapping;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class ApproverPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'viewlb applications'                            => 'Allows approvers to inspect and review Lakshmir Bhandar applications awaiting sanction.',
            'Normal Entry Approver Allow'                    => 'Grants approving officers permission to sanction and approve regular beneficiary applications.',
            'Normal Entry Reject Allow'                      => 'Authorizes the rejection of regular intake applications that do not satisfy scheme eligibility criteria.',
            'Normal Entry Revert Allow'                      => 'Allows reviewers to revert routine applications back to the applicant or operator for document corrections.',
            'view beneficiaries'                             => 'Enables searching and viewing beneficiary records, application status, and personal details.',
            'view reports'                                   => 'Grants access to scheme summary reports, MIS dashboards, and operational performance metrics.',
            'update bank details'                            => 'Permits approving updates and corrections to beneficiary bank account and IFSC details.',
            'search bank update'                             => 'Enables searching and filtering bank account update requests across administrative boundaries.',
            'update mobile'                                  => 'Authorizes updating beneficiary mobile contact numbers with verification logs.',
            'view approver incomplete'                       => 'Allows approvers to view applications flagged with incomplete parameters in the approval queue.',
            'view users'                                     => 'Allows viewing registered user lists, user profiles, and assigned duty allocations.',
            'create users'                                   => 'Permits registration and creation of new user accounts across administrative tiers.',
            'view caste modification list'                   => 'Permits viewing submitted requests for beneficiary caste category updates and corrections.',
            'view beneficiary details'                       => 'Authorizes viewing full beneficiary personal, bank, contact, and enclosure information.',
            'TakeActionForCaste'                             => 'Authorizes taking review actions (verify, revert, reject) on submitted caste modification requests.',
            'ApproveCasteApplication'                        => 'Grants final sanction and approval for beneficiary caste category modifications.',
            'RevertCasteApplication'                         => 'Allows reverting caste modification requests back to applicant or operator for clarification.',
            'RejectApprovedBeneficiary'                      => 'Permits initiating de-activation or rejection procedures for previously approved beneficiaries.',
            'Filter Applicant To Reject'                     => 'Enables filtering and querying enrolled beneficiaries for de-duplication or rejection review.',
            'View Details To Reject'                         => 'Authorizes inspecting comprehensive audit history of a beneficiary prior to final rejection.',
            'Reject Beneficiary'                             => 'Authorizes approving officers to execute final rejection and termination of beneficiary benefits.',
            'lb-application-list'                            => 'Enables access to the processing queue of Lakshmir Bhandar applications for verification/approval.',
            'Bulk Actions Normal Entry Approver Allow'       => 'Authorizes batch approval and enrollment of multiple regular beneficiary applications.',
            'Bulk Actions Normal Entry Reject Allow'         => 'Enables batch rejection of regular intake records failing verification parameters.',
            'Bulk Actions Normal Entry Revert Allow'         => 'Enables batch reverting of regular applications back to data entry operators.',
            'Bulk Actions Duare Sarkar Entry Approver Allow' => 'Enables batch approval and sanctioning of verified Duare Sarkar camp applications.',
            'Bulk Actions Duare Sarkar Entry Reject Allow'   => 'Enables bulk rejection of non-qualifying records from Duare Sarkar outreach drives.',
            'Bulk Actions Duare Sarkar Entry Revert Allow'   => 'Enables bulk reverting of defective Duare Sarkar camp applications for rectification.',
            'Duare Sarkar Entry Approver Allow'              => 'Authorizes designated camp approving officers to sanction and approve applications collected in Duare Sarkar camps.',
            'Duare Sarkar Entry Reject Allow'                => 'Permits rejection of ineligible or duplicate applications received during Duare Sarkar camps.',
            'Duare Sarkar Entry Revert Allow'                => 'Allows reverting Duare Sarkar camp applications back to operators for missing documents or invalid data.',
            're-activate-death-incident'                     => 'Allows authorized officers to review and re-activate beneficiaries incorrectly marked as deceased.',
            'manage-menus'                                   => 'Permits administrators to manage and customize navigation menus across user roles.',
            'manage-permissions'                             => 'Authorizes creating, updating, and structuring permission trees in the system.',
            'manage-roles'                                   => 'Authorizes managing and configuring user roles and their assigned access privileges.',
            'manage-users'                                   => 'Permits managing user credentials, account status, and role assignments.',
            'manage-departments'                             => 'Authorizes managing government departments and their associated scheme mappings.',
            'manage-schemes'                                 => 'Permits creating, editing, and managing government schemes and their onboarding settings.',
            'modify caste'                                   => 'Authorizes accessing the Caste Management module to process caste revision requests.',
            'back-from-jb'                                   => 'Authorizes reviewing applications returned from Jai Bangla portal for rectification.',
            'back-from-jb-approver-button'                   => 'Enables approver action buttons on applications returned from Jai Bangla.',
            'sarasori-mukhyamantri'                          => 'Grants access to Sarasori Mukhyamantri module for reviewing and handling citizen grievances.',
            'cmo-grievance-mark'                             => 'Permits marking, updating, and linking beneficiary records against CMO grievance numbers.',
        ];
        try {
            $role = Role::findByName('Approver');
        } catch (\Exception $e) {
            $this->command->error('Role "Approver" not found. Seeder aborted.');

            return;
        }
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
            $this->command->info('No users found in UserRoleSchemeOfficeMapping for role "Approver".');

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

        $this->command->info('Give Permission To Approver  finished.');
    }
}
