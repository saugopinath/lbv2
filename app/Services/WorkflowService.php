<?php
namespace App\Services;
use Illuminate\Support\Facades\Crypt;
use App\Models\WorkflowsteproleMapping;
class WorkflowService
{
    public function getLabelRoles()
    {
        try {
            $roleId = Crypt::decryptString(session('lgd_session.role_id'));
            $schemeId = Crypt::decryptString(session('lgd_session.scheme_id'));
            return WorkflowsteproleMapping::getLabelRoleIdsByRole($schemeId, $roleId);
        } catch (\Exception $e) {
            return null;
        }
    }
}