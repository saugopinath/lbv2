<?php

namespace App\Helpers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Cache;

class CheckAuthHelper
{
    public static function getRoleId(): ?int
    {
        return session('lgd_session')
            ? (int) Crypt::decryptString(session('lgd_session.role_id'))
            : null;
    }

    public static function isSuperAdmin(): bool
    {
        return in_array(self::getRoleId(), [1]);
    }

    public static function isHOD(): bool
    {
        return in_array(self::getRoleId(), [2]);
    }

    public static function isDelegatedHOD(): bool
    {
        return in_array(self::getRoleId(), [3]);
    }

    public static function isCommonHOD(): bool
    {
        return in_array(self::getRoleId(), [2, 3]);
    }

    public static function isApprover(): bool
    {
        return in_array(self::getRoleId(), [4]);
    }

    public static function isDelegatedApprover(): bool
    {
        return in_array(self::getRoleId(), [5]);
    }

    public static function isCommonApprover(): bool
    {
        return in_array(self::getRoleId(), [4, 5]);
    }

    public static function isVerifier(): bool
    {
        return in_array(self::getRoleId(), [6]);
    }

    public static function isDelegatedVerifier(): bool
    {
        return in_array(self::getRoleId(), [7]);
    }

    public static function isCommmonVerifier(): bool
    {
        return in_array(self::getRoleId(), [6, 7]);
    }

    public static function isOperator(): bool
    {
        return in_array(self::getRoleId(), [8]);
    }

    public static function isDelegatedOperator(): bool
    {
        return in_array(self::getRoleId(), [9]);
    }

    public static function isCommonOperator(): bool
    {
        return in_array(self::getRoleId(), [8, 9]);
    }

    public static function isDDO(): bool
    {
        return in_array(self::getRoleId(), [10]);
    }

    public static function isDelegatedDDO(): bool
    {
        return in_array(self::getRoleId(), [11]);
    }

    public static function isCommonDDO(): bool
    {
        return in_array(self::getRoleId(), [10, 11]);
    }

    public static function isCommonWorkFlow2ndStep(): bool
    {
        return self::isCommmonVerifier() || self::isCommonApprover();
    }

    public static function isCommonWorkFlow3rdStep(): bool
    {
        return self::isCommonOperator() || self::isCommmonVerifier() || self::isCommonApprover();
    }

    public static function isCommonWorkFlow4thStep(): bool
    {
        return self::isCommonOperator() || self::isCommmonVerifier() || self::isCommonApprover() || self::isCommonDDO() || self::isSuperAdmin();
    }
    public static function isCommonPrivilegedUser(): bool
    {
        return self::isSuperAdmin() || self::isCommmonVerifier() || self::isCommonApprover() || self::isCommonHOD();
    }
    public static function isCommonReportChecker(): bool
    {
        return self::isCommonOperator() || self::isCommmonVerifier() || self::isCommonApprover() || self::isCommonHOD();
    }
    public static function isCommonAllChecker(): bool
    {
        return self::isCommonOperator() || self::isCommmonVerifier() || self::isCommonApprover() || self::isCommonHOD() || self::isCommonDDO();
    }
}
