<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;
use App\Models\WorkflowsteproleMapping;
use App\Models\DynamicWorkflowSchemeModule;
use App\Models\UserRoleSchemeOfficeMapping;
use Spatie\Permission\PermissionRegistrar;
use App\Helpers\WorkFlowPermissionHelper;

class WorkflowService
{
    public function getLevelRoles($schemeId, $rank = null, $schemeModuleId = null, $moduleCode = null)
    {
        $encryptedRoleId = session('lgd_session.role_id');
        $sessionRoleId = null;
        if ($encryptedRoleId) {
            try {
                $sessionRoleId = Crypt::decryptString($encryptedRoleId);
            } catch (\Exception $e) {
                $sessionRoleId = null;
            }
        }

        try {
            if (!$schemeModuleId && $schemeId) {
                if (!$moduleCode && request()->has('module')) {
                    try {
                        $moduleCode = decrypt(request()->query('module'));
                    } catch (\Exception $e) {
                        $moduleCode = request()->query('module');
                    }
                }

                if ($moduleCode) {
                    $schemeModuleId = DynamicWorkflowSchemeModule::where('scheme_id', $schemeId)
                        ->where(function ($q) use ($moduleCode) {
                            $q->where('main_module_code', 'LIKE', $moduleCode)
                                ->orWhereRaw("CAST(id AS TEXT) = ?", [$moduleCode]);
                        })
                        ->where('is_disabled', false)
                        ->value('id');
                }
            }

            // Collect all role IDs associated with this user (current duty, session role, spatie roles, office mapping roles)
            $rawRoleIds = [];

            $currentDuty = WorkFlowPermissionHelper::getCurrentDuty();
            if (!empty($currentDuty['role_id'])) {
                $rawRoleIds[] = $currentDuty['role_id'];
            }

            if ($sessionRoleId) {
                $rawRoleIds[] = $sessionRoleId;
            }

            if (auth()->check()) {
                $user = auth()->user();
                if ($schemeId) {
                    app(PermissionRegistrar::class)->setPermissionsTeamId($schemeId);
                }
                $spatieRoleIds = $user->roles->pluck('id')->toArray();
                $mappingRoleIds = UserRoleSchemeOfficeMapping::where('user_id', $user->id)
                    ->when($schemeId, fn($q) => $q->where('scheme_id', $schemeId))
                    ->pluck('role_id')
                    ->toArray();

                $rawRoleIds = array_merge($rawRoleIds, $spatieRoleIds, $mappingRoleIds);
            }

            $roleIds = array_values(array_unique(array_filter(array_map(function ($id) {
                if (is_numeric($id)) return (int) $id;
                try {
                    $un = @unserialize($id);
                    if (is_numeric($un)) return (int) $un;
                } catch (\Throwable $e) {
                }
                return null;
            }, $rawRoleIds))));

            if (empty($roleIds) && $rank === null) {
                return null;
            }

            return WorkflowsteproleMapping::getLevelRoleIdsByRole($schemeId, $roleIds, $rank, $schemeModuleId);
        } catch (\Exception $e) {
            return null;
        }
    }
}
