<?php
namespace App\Services;
use Illuminate\Support\Facades\Crypt;
use App\Models\WorkflowsteproleMapping;
class WorkflowService
{
    public function getLevelRoles($schemeId, $rank = null)
    {
        $encryptedRoleId = session('lgd_session.role_id');
        if (!$encryptedRoleId) {
            return null;
        }
        try {
            $roleId = Crypt::decryptString($encryptedRoleId);
            return WorkflowsteproleMapping::getLevelRoleIdsByRole($schemeId, $roleId, $rank);
        } catch (\Exception $e) {
            return null;
        }
    }
}