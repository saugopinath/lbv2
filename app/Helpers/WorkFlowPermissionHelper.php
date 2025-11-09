<?php

namespace App\Helpers;

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
}
