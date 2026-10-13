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

            // 1st Priority: Strictly use active duty role from session('current_duty') if present
            $rawRoleIds = [];
            $currentDuty = WorkFlowPermissionHelper::getCurrentDuty();

            if (!empty($currentDuty['role_id'])) {
                $rawRoleIds[] = (int) $currentDuty['role_id'];
            } else {
                // Fallback only if active duty role_id is not set
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
            }

            /* OLD CODE COMMENTED OUT FOR BACKWARD COMPATIBILITY:
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
            */

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

    /**
     * Resolves all matching step targets across active scheme modules for a user's role.
     * Enforces jurisdiction isolation (Block = Rural & Normal, Subdivision = Urban & Normal).
     * Higher converging offices (District/State) can process across Rural, Urban, and Normal.
     */
    public function getWorkflowStepTargets($schemeId, $schemeModuleId = null): array
    {
        if (empty($schemeId)) {
            return [];
        }

        try {
            $user = auth()->user();
            $currentDuty = WorkFlowPermissionHelper::getCurrentDuty();
            $dutyOfficeId = $currentDuty['office_id'] ?? null;
            $dutyRoleId = $currentDuty['role_id'] ?? null;

            // 1. Identify user's jurisdiction from active duty office
            $officeType = $currentDuty['office_type'] ?? null;
            $isBlockOffice = ($officeType === 'block');
            $isSubdivOffice = ($officeType === 'subdivision');

            // Fallback only if office_type is missing from duty
            if (!$officeType && $dutyOfficeId) {
                $dutyOffice = \App\Models\OfficeMaster::with(['officeType' => fn($q) => $q->select('id', 'name', 'short_name')])
                    ->select('id', 'office_type_id', 'block_id', 'subdivision_id', 'municipalitiy_id')
                    ->find($dutyOfficeId);
                if ($dutyOffice) {
                    $resolvedType = \App\Helpers\DutyWorkFlowPermissionHelper::resolveOfficeType($dutyOffice);
                    $isBlockOffice = ($resolvedType === 'block');
                    $isSubdivOffice = ($resolvedType === 'subdivision');
                }
            }

            // 2. Fetch all active scheme modules
            $activeSchemeModules = DynamicWorkflowSchemeModule::with(['module' => fn($q) => $q->select('id', 'workflow_type')])
                ->select('id', 'scheme_id', 'module_id', 'main_module_code', 'is_disabled')
                ->where('scheme_id', $schemeId)
                ->where('is_disabled', false)
                ->when($schemeModuleId, fn($q) => $q->where('id', $schemeModuleId))
                ->get();

            if ($activeSchemeModules->isEmpty()) {
                return [];
            }

            // 3. JURISDICTION ENFORCEMENT:
            // - If user sits in a Block office, restrict to Rural and Normal (no Urban)
            // - If user sits in a Subdivision office, restrict to Urban and Normal (no Rural)
            // - Converging offices (District/State) can access Rural, Urban, and Normal
            $targetModules = $activeSchemeModules;

            if ($isBlockOffice) {
                $filtered = $activeSchemeModules->filter(function ($sm) {
                    $type = strtolower($sm->module?->workflow_type ?? 'normal');
                    return $type === 'rural' || $type === 'normal';
                });
                $targetModules = $filtered->isNotEmpty() ? $filtered : $activeSchemeModules;
            } elseif ($isSubdivOffice) {
                $filtered = $activeSchemeModules->filter(function ($sm) {
                    $type = strtolower($sm->module?->workflow_type ?? 'urban');
                    return $type === 'urban' || $type === 'normal';
                });
                $targetModules = $filtered->isNotEmpty() ? $filtered : $activeSchemeModules;
            }

            $targetModuleIds = $targetModules->pluck('id')->toArray();

            // 4. Resolve user roles (Strictly use active duty role)
            $roleIds = [];
            if ($dutyRoleId) {
                $roleIds[] = (int) $dutyRoleId;
            } else {
                $encryptedRoleId = session('lgd_session.role_id');
                if ($encryptedRoleId) {
                    try {
                        $roleIds[] = (int) Crypt::decryptString($encryptedRoleId);
                    } catch (\Exception $e) {}
                }

                if ($user && empty($roleIds)) {
                    $mappingRoleIds = UserRoleSchemeOfficeMapping::where('user_id', $user->id)
                        ->when($schemeId, fn($q) => $q->where('scheme_id', $schemeId))
                        ->when($dutyOfficeId, fn($q) => $q->where('office_id', $dutyOfficeId))
                        ->pluck('role_id')
                        ->map(fn($id) => (int) $id)
                        ->toArray();

                    $roleIds = array_merge($roleIds, $mappingRoleIds);
                }
            }

            $roleIds = array_values(array_unique(array_filter($roleIds)));

            if (empty($roleIds)) {
                return [];
            }

            // 5. Query matching step mappings for the resolved modules and roles
            $mappings = WorkflowsteproleMapping::select('id', 'scheme_id', 'role_id', 'module_id', 'workflow_step_id', 'rank', 'is_final_step', 'is_first_step', 'next_level_role_id')
                ->where('scheme_id', $schemeId)
                ->whereIn('role_id', $roleIds)
                ->whereIn('module_id', $targetModuleIds)
                ->get();

            // Batch-fetch dynamic workflow labels to prevent N+1 queries during step verification
            $stepIds = $mappings->pluck('workflow_step_id')->filter()->unique();
            $labels = $stepIds->isNotEmpty()
                ? \App\Models\DynamicWorkflowLabel::select('id', 'assign_specific_users', 'user_ids')->whereIn('id', $stepIds)->get()->keyBy('id')
                : collect();

            // Verify step-level specific user assignment if configured
            $validMappings = $mappings->filter(function ($mapping) use ($user, $labels) {
                $label = $labels->get($mapping->workflow_step_id);
                if ($label && $label->assign_specific_users) {
                    $allowedUserIds = (array) (is_array($label->user_ids) ? $label->user_ids : json_decode($label->user_ids, true));
                    if ($user && !in_array((string)$user->id, array_map('strval', $allowedUserIds), true)) {
                        return false;
                    }
                }
                return true;
            });

            $targets = [];
            foreach ($validMappings as $mapping) {
                $rank = $mapping->rank ?? $mapping->workflow_step_id;
                if ($rank) {
                    $targets[] = [
                        'scheme_module_id'   => (int) $mapping->module_id,
                        'rank'               => (int) $rank,
                        'is_final_step'      => (bool) $mapping->is_final_step,
                        'is_first_step'      => (bool) $mapping->is_first_step,
                        'role_id'            => (int) $mapping->role_id,
                        'next_level_role_id' => $mapping->next_level_role_id ? (int) $mapping->next_level_role_id : null,
                    ];
                }
            }

            return $targets;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Resolves all matching step ranks across active scheme modules for a user's role.
     */
    public function getAllLevelRoleRanks($schemeId, $schemeModuleId = null): array
    {
        $targets = $this->getWorkflowStepTargets($schemeId, $schemeModuleId);
        return array_values(array_unique(array_column($targets, 'rank')));
    }
}
