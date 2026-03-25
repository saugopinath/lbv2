<?php

namespace Database\Seeders\AssignPermission;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\UserRoleSchemeOfficeMapping;

class ApproverPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'viewlb applications',
            'Normal Entry Approver Allow',
            'Normal Entry Reject Allow',
            'Normal Entry Revert Allow',
            'view beneficiaries',
            'view reports',
            'update bank details',
            'search bank update',
            'update mobile',
            'update bank details',
            'view approver incomplete',
            'view users',
            'create users',
            'view caste modification list',
            'view beneficiary details',
            'TakeActionForCaste',
            'ApproveCasteApplication',
            'RevertCasteApplication',
            'RejectApprovedBeneficiary',
            'Filter Applicant To Reject',
            'View Details To Reject',
            'Reject Beneficiary',
            'lb-application-list',
            'Bulk Actions Normal Entry Approver Allow',
            'Bulk Actions Normal Entry Reject Allow',
            'Bulk Actions Normal Entry Revert Allow',
            'Bulk Actions Duare Sarkar Entry Approver Allow',
            'Bulk Actions Duare Sarkar Entry Reject Allow',
            'Bulk Actions Duare Sarkar Entry Revert Allow',
            'Duare Sarkar Entry Approver Allow',
            'Duare Sarkar Entry Reject Allow',
            'Duare Sarkar Entry Revert Allow',
            'Normal Entry Revert Allow',
            'Normal Entry Approver Allow',
            'Normal Entry Reject Allow',
            're-activate-death-incident',
            'back-from-jb',
            'cmo-grievance-mark',
            'sarasori-mukhyamantri',
            'back-from-jb-approver-button'
        ];
        try {
            $role = Role::findByName('Approver');
        } catch (\Exception $e) {
            $this->command->error('Role "Approver" not found. Seeder aborted.');
            return;
        }
        $permissionModels = [];
        foreach ($permissions as $permName) {
            $permissionModels[] = Permission::firstOrCreate(
                ['name' => $permName],
                ['guard_name' => 'web']
            );
        }
        $adminUserIds = UserRoleSchemeOfficeMapping::where('role_id', $role->id)
            ->pluck('user_id')
            ->unique()
            ->values();
        if ($adminUserIds->isEmpty()) {
            $this->command->info('No users found in UserRoleSchemeOfficeMapping for role "Approver".');
            return;
        }
        foreach ($adminUserIds as $userId) {
            $user = User::find($userId);
            if (! $user) {
                $this->command->warn("User id={$userId} not found (skipping).");
                continue;
            }
            foreach ($permissionModels as $permission) {

                if ($user->hasPermissionTo($permission->name)) {
                    $this->command->info("User id={$user->id} already has permission '{$permission->name}' (id={$permission->id}).");
                    continue;
                }
                $user->givePermissionTo($permission->name);
                $this->command->info("Assigned permission '{$permission->name}' (id={$permission->id}) to user id={$user->id}.");
            }
        }
        $this->command->info('Give Permission To Approver  finished.');
    }
}
