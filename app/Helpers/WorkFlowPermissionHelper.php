<?php

namespace App\Helpers;

use App\Models\Codemaster;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\PermissionRegistrar;

class WorkFlowPermissionHelper
{
    public static function getSchemeId()
    {
        $schemeId = session('scheme_id');

        if (! $schemeId && session()->has('lgd_session.scheme_id')) {
            try {
                $schemeId = Crypt::decryptString(session('lgd_session.scheme_id'));
            } catch (\Exception $e) {
                $schemeId = null;
            }
        }

        return $schemeId ? (int) $schemeId : null;
    }

    public static function hasPermission($permissionKey, $schemeId = null)
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        if ($schemeId) {
            app(PermissionRegistrar::class)->setPermissionsTeamId((int) $schemeId);

            return $user->can($permissionKey);
        }

        $userSchemes = self::getUserSchemes();

        if (empty($userSchemes)) {
            return false;
        }

        foreach ($userSchemes as $scheme) {
            app(PermissionRegistrar::class)->setPermissionsTeamId((int) $scheme);
            if ($user->can($permissionKey)) {
                return true;
            }
        }

        return false;
    }

    public static function getUserId(): ?int
    {
        return session('lgd_session')
            ? (int) Crypt::decryptString(session('lgd_session.user_id'))
            : null;
    }

    private static function getUserSchemes(): array
    {
        $schemes = Session::get('lgd_session.scheme_id');

        $schemeList = [];

        if (! $schemes) {
            return [];
        }

        foreach ($schemes as $scheme) {
            $schemeList[] = Crypt::decryptString($scheme);
        }

        return $schemeList;
    }

    public static function canEntry($schemeId = null): bool
    {
        return self::hasPermission('submit-lb-form', $schemeId);
    }

    public static function canViewUser($schemeId = null): bool
    {
        return self::hasPermission('view users', $schemeId);
    }

    public static function canDraftList($schemeId = null): bool
    {
        return self::hasPermission('view draft list', $schemeId);
    }

    public static function canEditDraft($schemeId = null): bool
    {
        return self::hasPermission('edit draft', $schemeId);
    }

    public static function canViewBeneficiaries($schemeId = null): bool
    {
        return self::hasPermission('view beneficiaries', $schemeId);
    }

    public static function canViewReport($schemeId = null): bool
    {
        return self::hasPermission('view reports', $schemeId);
    }

    public static function canRoleMappings($schemeId = null): bool
    {
        return self::hasPermission('create role mappings', $schemeId);
    }

    public static function canApproveApplication($schemeId = null): bool
    {
        return self::hasPermission('approve application', $schemeId);
    }

    public static function canRevertApplication($schemeId = null): bool
    {
        return self::hasPermission('revert application', $schemeId);
    }

    public static function canCreateUsers($schemeId = null): bool
    {
        return self::hasPermission('create users', $schemeId);
    }

    public static function canNormalEntryAllow($schemeId = null): bool
    {
        return self::hasPermission('Normal Entry', $schemeId);
    }

    public static function canDuareSarkarEntryAllow($schemeId = null): bool
    {
        return self::hasPermission('Duare Sarkar Entry', $schemeId);
    }

    public static function canEntryAllow($schemeId = null): bool
    {
        return self::hasPermission('Entry Allow', $schemeId);
    }

    public static function canCreateEntry($schemeId = null): bool
    {
        return self::canNormalEntryAllow($schemeId) || self::canDuareSarkarEntryAllow($schemeId);
    }

    public static function getAllowedEntryTypes()
    {
        $entryTypes = collect();

        if (self::canNormalEntryAllow()) {
            $normal = Codemaster::where('short_name', 'entry_type_normal')->get();
            $entryTypes = $entryTypes->merge($normal);
        }

        if (self::canDuareSarkarEntryAllow()) {
            $duare = Codemaster::where('short_name', 'entry_type_duare_sarkar')->get();
            $entryTypes = $entryTypes->merge($duare);
        }

        return $entryTypes;
    }

    public static function canModifyCaste($schemeId = null): bool
    {
        return self::hasPermission('modify caste', $schemeId);
    }

    public static function canEditCaste($schemeId = null): bool
    {
        return self::hasPermission('edit caste', $schemeId);
    }

    public static function canUpdateCaste($schemeId = null): bool
    {
        return self::hasPermission('update caste', $schemeId);
    }

    public static function canCasteModification($schemeId = null): bool
    {
        return self::hasPermission('view caste modification list', $schemeId);
    }

    public static function canBeneficiaryDetails($schemeId = null): bool
    {
        return self::hasPermission('view beneficiary details', $schemeId);
    }

    public static function canVerifierIncomplet($schemeId = null): bool
    {
        return self::hasPermission('view verifier incomplete', $schemeId);
    }

    public static function canApproverIncomplet($schemeId = null): bool
    {
        return self::hasPermission('view approver incomplete', $schemeId);
    }

    public static function canUpdateIncomplet($schemeId = null): bool
    {
        return self::hasPermission('update incomplete', $schemeId);
    }

    public static function canRevertIncomplet($schemeId = null): bool
    {
        return self::hasPermission('revert incomplete', $schemeId);
    }

    public static function canViewOffices($schemeId = null): bool
    {
        return self::hasPermission('view offices', $schemeId);
    }

    public static function canRoleMapping($schemeId = null): bool
    {
        return self::hasPermission('manage role mappings', $schemeId);
    }

    public static function canViewPermission($schemeId = null): bool
    {
        return self::hasPermission('view permission', $schemeId);
    }

    public static function canUpdateBankDetails($schemeId = null): bool
    {
        return self::hasPermission('update bank details', $schemeId);
    }    

    public static function canUpdateMobile($schemeId = null): bool
    {
        return self::hasPermission('update mobile', $schemeId);
    }

    public static function canUpdateBank($schemeId = null): bool
    {
        return self::hasPermission('update bank', $schemeId);
    }

    public static function canViewUserPermisson($schemeId = null): bool
    {
        return self::hasPermission('view user permission', $schemeId);
    }

    public static function canRolePermissionManagement($schemeId = null): bool
    {
        return self::hasPermission('role-permission-management', $schemeId);
    }

    public static function canViewLbApplications($schemeId = null): bool
    {
        return self::hasPermission('lb-application-list', $schemeId);
    }

    public static function canViewApplication($schemeId = null): bool
    {
        return self::hasPermission('view application', $schemeId);
    }

    public static function canViewApprovedList($schemeId = null): bool
    {
        return self::hasPermission('view approved list', $schemeId);
    }

    public static function canViewIncompleteList($schemeId = null): bool
    {
        return self::hasPermission('view incomplete applications', $schemeId);
    }

    public static function canCreateOffices($schemeId = null): bool
    {
        return self::hasPermission('create offices', $schemeId);
    }

    public static function canApprovedWise($schemeId = null): bool
    {
        return self::hasPermission('view approved ba wise', $schemeId);
    }

    public static function canWorkflowPermission($schemeId = null): bool
    {
        return self::hasPermission('Workflow Permission', $schemeId);
    }

    public static function canVerificationAllow($schemeId = null): bool
    {
        return self::hasPermission('Verification Allow', $schemeId);
    }

    public static function canApproverAllow($schemeId = null): bool
    {
        return self::hasPermission('Approver Allow', $schemeId);
    }

    public static function canRejectAllow($schemeId = null): bool
    {
        return self::hasPermission('Reject Allow', $schemeId);
    }   
    
    public static function canUpdateBankDetailsPermission($schemeId = null): bool
    {
        return self::hasPermission('update bank details', $schemeId);
    }

    public static function canSearchBankUpdate($schemeId = null): bool
    {
        return self::hasPermission('search bank update', $schemeId);
    }

    public static function canRevertAllow($schemeId = null): bool
    {
        return self::hasPermission('Revert Allow', $schemeId);
    }

    public static function canAnyLbMenu($schemeId = null): bool
    {
        return self::hasPermission('lb-application-list', $schemeId) || self::hasPermission('submit-lb-form', $schemeId);
    }

    public static function canIncomplete($schemeId = null): bool
    {
        return self::hasPermission('view verifier incomplete', $schemeId)
            || self::hasPermission('view approver incomplete', $schemeId);
    }

    public static function canDutyManagement($schemeId = null): bool
    {
        return self::hasPermission('view users', $schemeId)
            || self::hasPermission('view offices', $schemeId) || self::hasPermission('manage role mappings', $schemeId);
    }

    public static function canCaste($schemeId = null): bool
    {
        return self::hasPermission('view caste modification list', $schemeId)
            || self::hasPermission('modify caste', $schemeId);
    }

    public static function canUserPermission($schemeId = null): bool
    {
        return self::hasPermission('view user permission', $schemeId)
            || self::hasPermission('view permission', $schemeId);
    }

    // public static function canBulkActionAllow(int $entryType, string $action, bool $isBulk = false): bool
    // {
    //     $user = Auth::user();

    //     switch ($entryType) {
    //         case 1:
    //             $prefix = 'Normal Entry';
    //             break;
    //         case 2:
    //             $prefix = 'Duare Sarkar Entry';
    //             break;
    //         default:
    //             return false;
    //     }

    //     switch (strtolower($action)) {
    //         case 'verification':
    //             $suffix = 'Verification Allow';
    //             break;
    //         case 'approver':
    //             $suffix = 'Approver Allow';
    //             break;
    //         case 'reject':
    //             $suffix = 'Reject Allow';
    //             break;
    //         case 'revert':
    //             $suffix = 'Revert Allow';
    //             break;
    //         default:
    //             return false;
    //     }

    //     $permission = $isBulk ? "Bulk Actions {$prefix} {$suffix}" : "{$prefix} {$suffix}";

    //     return $user->can($permission);
    // }

    public static function canBulkActionAllow(int $entryType, string $action, bool $isBulk = false, $schemeId = null): bool {

        switch ($entryType) {
            case 1:
                $prefix = 'Normal Entry';
                break;

            case 2:
                $prefix = 'Duare Sarkar Entry';
                break;

            default:
                return false;
        }

        switch (strtolower($action)) {

            case 'verification':
                $suffix = 'Verification Allow';
                break;

            case 'approver':
                $suffix = 'Approver Allow';
                break;

            case 'reject':
                $suffix = 'Reject Allow';
                break;

            case 'revert':
                $suffix = 'Revert Allow';
                break;

            default:
                return false;
        }

        $permission = $isBulk ? "Bulk Actions {$prefix} {$suffix}" : "{$prefix} {$suffix}";       

        return self::hasPermission($permission, $schemeId);
    }

    public static function canVerifyCastApplication($schemeId = null): bool
    {
        return self::hasPermission('VerifyCasteApplication', $schemeId);
    }

    public static function canApproveCastApplication($schemeId = null): bool
    {
        return self::hasPermission('ApproveCasteApplication', $schemeId);
    }

    public static function canViewCastApplication($schemeId = null): bool
    {
        return self::hasPermission('ViewCastApplication', $schemeId);
    }

    public static function canTakeActionForCaste($schemeId = null): bool
    {
        return self::hasPermission('TakeActionForCaste', $schemeId);
    }

    public static function canRevertCastApplication($schemeId = null): bool
    {
        return self::hasPermission('RevertCasteApplication', $schemeId);
    }

    public static function canEditRevertApplication($schemeId = null): bool
    {
        return self::hasPermission('EditRevertApplication', $schemeId);
    }

    public static function canRejectApprovedBeneficiary($schemeId = null): bool
    {
        return self::hasPermission('RejectApprovedBeneficiary', $schemeId);
    }

    public static function canFilterApplicantToReject($schemeId = null): bool
    {
        return self::hasPermission('Filter Applicant To Reject', $schemeId);
    }

    public static function canViewDetailsToReject($schemeId = null): bool
    {
        return self::hasPermission('View Details To Reject', $schemeId);
    }

    public static function canRejectBeneficiary($schemeId = null): bool
    {
        return self::hasPermission('Reject Beneficiary', $schemeId);
    }

    public static function canMasterTab($schemeId = null): bool
    {
        return self::hasPermission('master-tab', $schemeId);
    }

    public static function canRoleRankManagement($schemeId = null): bool
    {
        return self::hasPermission('role-rank-management', $schemeId);
    }

    public static function canDefineWorkflow($schemeId = null): bool
    {
        return self::hasPermission('define-workflow', $schemeId);
    }

    public static function canSchemeOnboard($schemeId = null): bool
    {
        return self::hasPermission('master-tab', $schemeId)
            || self::hasPermission('role-rank-management', $schemeId) || self::hasPermission('define-workflow', $schemeId);
    }

    public static function canSchemeCapacitySetting($schemeId = null): bool
    {
        return self::hasPermission('scheme-capacity-setting', $schemeId);
    }

    public static function canImportJanmaMrityuData($schemeId = null): bool
    {
        return self::hasPermission('import-janma-mrityu-data', $schemeId);
    }

    public static function canReActivateDeathIncident($schemeId = null): bool
    {
        return self::hasPermission('re-activate-death-incident', $schemeId);
    }

    public static function canJanmyaMrityuBeneficiaryList($schemeId = null): bool
    {
        return self::hasPermission('janmya-mrityu-beneficiary-list', $schemeId);
    }

    public static function canCMODataFetch($schemeId = null): bool
    {
        return self::hasPermission('cmo-data-fetch', $schemeId);
    }

    public static function canSarasoriMukhyamantri($schemeId = null): bool
    {
        return self::hasPermission('sarasori-mukhyamantri', $schemeId);
    }

    public static function canCMOGrievanceMark($schemeId = null): bool
    {
        return self::hasPermission('cmo-grievance-mark', $schemeId);
    }

    public static function canBackFromJb($schemeId = null): bool
    {
        return self::hasPermission('back-from-jb', $schemeId);
    }

    public static function canBackFromJbVerifierButton($schemeId = null): bool
    {
        return self::hasPermission('back-from-jb-verifier-button', $schemeId);
    }

    public static function canBackFromJbApproverButton($schemeId = null): bool
    {
        return self::hasPermission('back-from-jb-approver-button', $schemeId);
    }
}
