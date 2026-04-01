<?php
namespace App\Services;
use Illuminate\Support\Facades\Crypt;
use App\Models\WorkflowsteproleMapping;
class WorkflowService
{
    public function getLabelRoles($schemeId, $rank = null, $module = null)
    {
        $encryptedRoleId = session('lgd_session.role_id');
        if (!$encryptedRoleId) {
            return null;
        }
        try {
            $roleId = Crypt::decryptString($encryptedRoleId);
            return WorkflowsteproleMapping::getLabelRoleIdsByRole($schemeId, $roleId, $rank, $module);
        } catch (\Exception $e) {
            return null;
        }
    }
}