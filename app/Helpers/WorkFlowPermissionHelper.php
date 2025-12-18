<?php

namespace App\Helpers;

use App\Models\Codemaster;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Cache;

class WorkFlowPermissionHelper
{
    public static function getUserId(): ?int
    {
        return session('lgd_session')
            ? (int) Crypt::decryptString(session('lgd_session.user_id'))
            : null;
    }

    public static function canEntry(): bool
    {
        return Auth::user()->can('submit lb form');
    }
    public static function canViewUser(): bool
    {
        return Auth::user()->can('view users');
    }
    public static function canDraftList(): bool
    {
        return Auth::user()->can('view draft list');
    }
    public static function canEditDraft(): bool
    {
        return Auth::user()->can('edit draft');
    }
    public static function canViewBeneficiaries(): bool
    {
        return Auth::user()->can('view beneficiaries');
    }
    public static function canViewReport(): bool
    {
        return Auth::user()->can('view reports');
    }
    public static function canRoleMappings(): bool
    {
        return Auth::user()->can('create role mappings');
    }
    public static function canApproveApplication(): bool
    {
        return Auth::user()->can('approve application');
    }
    public static function canRevertApplication(): bool
    {
        return Auth::user()->can('revert application');
    }
    public static function canCreateUsers(): bool
    {
        return Auth::user()->can('create users');
    }
    public static function canNormalEntryAllow(): bool
    {
        return Auth::user()->can('Normal Entry Allow');
    }
    public static function canEntryAllow(): bool
    {
        return Auth::user()->can('Entry Allow');
    }
    public static function canDuareSarkarEntryAllow(): bool
    {
        return Auth::user()->can('Duare Sarkar Entry Allow');
    }
    public static function canCreateEntry(): bool
    {
        return self::canNormalEntryAllow() || self::canDuareSarkarEntryAllow();
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
    public static function canModifyCaste(): bool
    {
        return Auth::user()->can('modify caste');
    }
    public static function canEditCaste(): bool
    {
        return Auth::user()->can('edit caste');
    }
    public static function canUpdateCaste(): bool
    {
        return Auth::user()->can('update caste');
    }
    public static function canCasteModification(): bool
    {
        return Auth::user()->can('view caste modification list');
    }
    public static function canBeneficiaryDetails(): bool
    {
        return Auth::user()->can('view beneficiary details');
    }
    public static function canVerifierIncomplet(): bool
    {
        return Auth::user()->can('view verifier incomplete');
    }
    public static function canApproverIncomplet(): bool
    {
        return Auth::user()->can('view approver incomplete');
    }
    public static function canUpdateIncomplet(): bool
    {
        return Auth::user()->can('update incomplete');
    }
    public static function canRevertIncomplet(): bool
    {
        return Auth::user()->can('revert incomplete');
    }
    public static function canViewOffices(): bool
    {
        return Auth::user()->can('view offices');
    }
    public static function canRoleMapping(): bool
    {
        return Auth::user()->can('manage role mappings');
    }
    public static function canViewPermission(): bool
    {
        return Auth::user()->can('view permission');
    }
    public static function canUpdateBankDetails(): bool
    {
        return Auth::user()->can('update bank details');
    }
    public static function canSearchBankUpdate(): bool
    {
        return Auth::user()->can('search bank update');
    }
    public static function canUpdateMobile(): bool
    {
        return Auth::user()->can('update mobile');
    }
    public static function canUpdateBank(): bool
    {
        return Auth::user()->can('update bank');
    }
    public static function canViewUserPermisson(): bool
    {
        return Auth::user()->can('view user permission');
    }
    public static function canViewLbApplications(): bool
    {
        return Auth::user()->can('view lb applications');
    }
    public static function canViewApplication(): bool
    {
        return Auth::user()->can('view application');
    }
    public static function canViewApprovedList(): bool
    {
        return Auth::user()->can('view approved list');
    }
    public static function canViewIncompleteList(): bool
    {
        return Auth::user()->can('view incomplete applications');
    }
    public static function canCreateOffices(): bool
    {
        return Auth::user()->can('create offices');
    }
    public static function canApprovedWise(): bool
    {
        return Auth::user()->can('view approved ba wise');
    }
    public static function canWorkflowPermission(): bool
    {
        return Auth::user()->can('Workflow Permission');
    }
    public static function canVerificationAllow(): bool
    {
        return Auth::user()->can('Verification Allow');
    }
    public static function canApproverAllow(): bool
    {
        return Auth::user()->can('Approver Allow');
    }
    public static function canRejectAllow(): bool
    {
        return Auth::user()->can('Reject Allow');
    }
    public static function canRevertAllow(): bool
    {
        return Auth::user()->can('Revert Allow');
    }
    public static function canAnyLbMenu(): bool
    {
        return Auth::user()->can('submit lb form')
            || Auth::user()->can('view draft list')
            || Auth::user()->can('view lb applications');
    }
    public static function canIncomplete(): bool
    {
        return Auth::user()->can('view verifier incomplete')
            || Auth::user()->can('view approver incomplete');
    }
    public static function canDutyManagement(): bool
    {
        return Auth::user()->can('view users')
            || Auth::user()->can('view offices') || Auth::user()->can('manage role mappings');
    }
    public static function canCaste(): bool
    {
        return Auth::user()->can('view caste modification list')
            || Auth::user()->can('modify caste');
    }
    public static function canUserPermission(): bool
    {
        return Auth::user()->can('view user permission')
            || Auth::user()->can('view permission');
    }


    public static function canBulkActionAllow(int $entryType, string $action, bool $isBulk = false): bool
    {
        $user = Auth::user();

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

        return $user->can($permission);
    }

    public static function canVerifyCastApplication(): bool
    {
        return Auth::user()->can('VerifyCasteApplication');
    }
    public static function canApproveCastApplication(): bool
    {
        return Auth::user()->can('ApproveCasteApplication');
    }

    public static function canViewCastApplication(): bool
    {
        return Auth::user()->can('ViewCastApplication');
    }
    public static function canImportJanmaMrityuData(): bool
    {
        return Auth::user()->can('import-janma-mrityu-data');
    }
    public static function canReActivateDeathIncident(): bool
    {
        return Auth::user()->can('re-activate-death-incident');
    }
    public static function canJanmyaMrityuBeneficiaryList(): bool
    {
        return Auth::user()->can('janmya-mrityu-beneficiary-list');
    }
    public static function canTakeActionForCaste(): bool
    {
        return Auth::user()->can('TakeActionForCaste');
    }
    public static function canRevertCastApplication(): bool
    {
        return Auth::user()->can('RevertCasteApplication');
    }
    public static function canEditRevertApplication(): bool
    {
        return Auth::user()->can('EditRevertApplication');
    }
     public static function canRolePermissionManagement(): bool
    {
        return Auth::user()->can('RolePermissionManagement');
    }
    public static function canRejectApprovedBeneficiary(): bool
    {
        return Auth::user()->can('RejectApprovedBeneficiary');
    }
    public static function canFilterApplicantToReject(): bool
    {
        return Auth::user()->can('Filter Applicant To Reject');
    }
    public static function canViewDetailsToReject(): bool
    {
        return Auth::user()->can('View Details To Reject'); 
    }
    public static function canRejectBeneficiary(): bool
    {
        return Auth::user()->can('Reject Beneficiary'); 
    }


}
