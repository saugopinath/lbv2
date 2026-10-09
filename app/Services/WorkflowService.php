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

    /**
     * Resolves all matching step ranks across active scheme modules for a user's role.
     * Enforces jurisdiction isolation (Block = strictly Rural, Subdivision = strictly Urban).
     * Merging across multiple modules is ONLY applied for the rare case where a higher converging
     * office (e.g. District) has the same role and user assigned to the final step (is_final_step).
     */
    public function getAllLevelRoleRanks($schemeId, $schemeModuleId = null): array
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
            $dutyOffice = $dutyOfficeId ? \App\Models\OfficeMaster::with('officeType')->find($dutyOfficeId) : null;
            $isBlockOffice = false;
            $isSubdivOffice = false;

            if ($dutyOffice) {
                if ((int) $dutyOffice->office_type_id === 153 || !empty($dutyOffice->block_id)) {
                    $isBlockOffice = true;
                } elseif ((int) $dutyOffice->office_type_id === 154 || !empty($dutyOffice->subdivision_id) || !empty($dutyOffice->municipalitiy_id)) {
                    $isSubdivOffice = true;
                } else {
                    $typeShort = strtolower($dutyOffice->officeType?->short_name ?? $dutyOffice->officeType?->name ?? '');
                    if (str_contains($typeShort, 'block') || str_contains($typeShort, 'bdo') || str_contains($typeShort, 'rural')) {
                        $isBlockOffice = true;
                    } elseif (str_contains($typeShort, 'sdo') || str_contains($typeShort, 'subdiv') || str_contains($typeShort, 'muni') || str_contains($typeShort, 'urban')) {
                        $isSubdivOffice = true;
                    }
                }
            }

            // 2. Fetch all active scheme modules
            $activeSchemeModules = DynamicWorkflowSchemeModule::with('module')
                ->where('scheme_id', $schemeId)
                ->where('is_disabled', false)
                ->when($schemeModuleId, fn($q) => $q->where('id', $schemeModuleId))
                ->get();

            if ($activeSchemeModules->isEmpty()) {
                return [];
            }

            // 3. JURISDICTION ENFORCEMENT:
            // - If user sits in a Block office, restrict strictly to Rural (no Urban merge)
            // - If user sits in a Subdivision office, restrict strictly to Urban (no Rural merge)
            // - Merging across multiple modules is ONLY allowed for higher/converging offices (District/State)
            //   where the user/role/office is the same on the final step (is_final_step = true)
            $targetModules = $activeSchemeModules;

            if ($isBlockOffice) {
                $filtered = $activeSchemeModules->filter(fn($sm) => strtolower($sm->module?->workflow_type ?? 'normal') === 'rural');
                if ($filtered->isEmpty()) {
                    $filtered = $activeSchemeModules->filter(fn($sm) => strtolower($sm->module?->workflow_type ?? 'normal') === 'normal');
                }
                $targetModules = $filtered->isNotEmpty() ? $filtered : $activeSchemeModules;
            } elseif ($isSubdivOffice) {
                $filtered = $activeSchemeModules->filter(fn($sm) => strtolower($sm->module?->workflow_type ?? 'normal') === 'urban');
                if ($filtered->isEmpty()) {
                    $filtered = $activeSchemeModules->filter(fn($sm) => strtolower($sm->module?->workflow_type ?? 'normal') === 'normal');
                }
                $targetModules = $filtered->isNotEmpty() ? $filtered : $activeSchemeModules;
            }

            $targetModuleIds = $targetModules->pluck('id')->toArray();

            // 4. Resolve user roles
            $rawRoleIds = [];
            if ($dutyRoleId) {
                $rawRoleIds[] = $dutyRoleId;
            }

            $encryptedRoleId = session('lgd_session.role_id');
            if ($encryptedRoleId) {
                try {
                    $rawRoleIds[] = Crypt::decryptString($encryptedRoleId);
                } catch (\Exception $e) {}
            }

            if ($user) {
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
                } catch (\Throwable $e) {}
                return null;
            }, $rawRoleIds))));

            if (empty($roleIds)) {
                return [];
            }

            // 5. Query matching step mappings for the resolved modules
            $mappings = WorkflowsteproleMapping::where('scheme_id', $schemeId)
                ->whereIn('role_id', $roleIds)
                ->whereIn('module_id', $targetModuleIds)
                ->get();

            // Verify step-level specific user assignment if configured
            $validMappings = $mappings->filter(function ($mapping) use ($user) {
                $label = \App\Models\DynamicWorkflowLabel::find($mapping->workflow_step_id);
                if ($label && $label->assign_specific_users) {
                    $allowedUserIds = (array) (is_array($label->user_ids) ? $label->user_ids : json_decode($label->user_ids, true));
                    if ($user && !in_array((string)$user->id, array_map('strval', $allowedUserIds), true)) {
                        return false;
                    }
                }
                return true;
            });

            $matchedModuleIds = $validMappings->pluck('module_id')->unique();

            // Merging across multiple modules is strictly reserved for the rare case where:
            // 1. Office is a higher converging office (District/State, not Block or Subdivision)
            // 2. Both/all modules' matched steps are the FINAL step (is_final_step = true)
            // 3. The user, role, and office are identical at this final step
            if ($matchedModuleIds->count() > 1) {
                $isConvergingOffice = !$isBlockOffice && !$isSubdivOffice;
                $allAreFinalSteps = $validMappings->every(fn($m) => (bool) $m->is_final_step);

                if ($isConvergingOffice && $allAreFinalSteps) {
                    // Valid rare case: merge both queues across modules
                    return array_values(array_unique(array_filter($validMappings->map(fn($m) => (int) ($m->rank ?? $m->workflow_step_id))->toArray())));
                }

                // If not qualifying for rare merge, do not merge across modules
                $finalStepMappings = $validMappings->filter(fn($m) => (bool) $m->is_final_step);
                if ($finalStepMappings->isNotEmpty()) {
                    $validMappings = $finalStepMappings;
                } else {
                    $firstModuleId = $matchedModuleIds->first();
                    $validMappings = $validMappings->filter(fn($m) => $m->module_id === $firstModuleId);
                }
            }

            $finalRanks = [];
            foreach ($validMappings as $mapping) {
                $rank = $mapping->rank ?? $mapping->workflow_step_id;
                if ($rank) {
                    $finalRanks[] = (int) $rank;
                }
            }

            return array_values(array_unique($finalRanks));
        } catch (\Exception $e) {
            return [];
        }
    }
}
