<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;
use App\Models\WorkflowsteproleMapping;
use App\Models\DynamicWorkflowSchemeModule;

class WorkflowService
{
    public function getLevelRoles($schemeId, $rank = null, $schemeModuleId = null, $moduleCode = null)
    {
        $encryptedRoleId = session('lgd_session.role_id');
        if (!$encryptedRoleId) {
            return null;
        }
        try {
            $roleId = Crypt::decryptString($encryptedRoleId);

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
                        ->where('main_module_code', $moduleCode)
                        ->where('is_disabled', false)
                        ->value('id');
                }
            }

            return WorkflowsteproleMapping::getLevelRoleIdsByRole($schemeId, $roleId, $rank, $schemeModuleId);
        } catch (\Exception $e) {
            return null;
        }
    }
}
