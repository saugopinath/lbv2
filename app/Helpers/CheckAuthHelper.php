<?php

namespace App\Helpers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Cache;

class CheckAuthHelper
{
    /**
     * Get role_id from LGD session
     */
    public static function getRoleId(): ?int
    {
        return session('lgd_session')
            ? (int) Crypt::decryptString(session('lgd_session.role_id'))
            : null;
    }

    /**
     * Get current user from LGD session OR Auth
     * Also logs in the user to Laravel Auth if not already
     */
    public static function getUser(): ?User
    {
        // If already logged in
        if (Auth::check()) {
            return Auth::user();
        }

        // If LGD session exists but Auth not logged in
        if (session('lgd_session')) {
            $userId = Crypt::decryptString(session('lgd_session.user_id'));
            $user = User::find($userId);

            if ($user) {
                Auth::login($user); // Critical: Enable Spatie
                return $user;
            }
        }

        return null;
    }

    // ===================== PERMISSION CHECKS (Spatie) =====================

    public static function hasPermission(string $permission): bool
    {
        $user = self::getUser();
        if (!$user) return false;

        // Cache permissions for 30 mins
        return Cache::remember("user_{$user->id}_perm_{$permission}", 1800, function () use ($user, $permission) {
            return $user->hasPermissionTo($permission);
        });
    }

    public static function hasAnyPermission(array $permissions): bool
    {
        $user = self::getUser();
        if (!$user) return false;

        return Cache::remember("user_{$user->id}_any_perm", 1800, function () use ($user, $permissions) {
            return $user->hasAnyPermission($permissions);
        });
    }

    // ===================== ROLE CHECKS (Spatie) =====================

    public static function hasRole(string $role): bool
    {
        $user = self::getUser();
        return $user?->hasRole($role) ?? false;
    }

    public static function hasAnyRole(array $roles): bool
    {
        $user = self::getUser();
        return $user?->hasAnyRole($roles) ?? false;
    }

    // ===================== LEGACY ROLE-ID CHECKS (Keep for backward) =====================

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
}
