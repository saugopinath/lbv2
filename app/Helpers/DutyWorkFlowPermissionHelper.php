<?php

namespace App\Helpers;

use App\Models\Role;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\PermissionRegistrar;

class DutyWorkFlowPermissionHelper
{
    private static ?array $dutyCache = null;
    private static $cachedRawDuty = null;
    private static array $permCheckCache = [];
    private static ?Role $cachedRole = null;
    private static ?int $cachedRoleId = null;

    /**
     * Clears in-memory static cache.
     */
    public static function clearCache(): void
    {
        self::$dutyCache = null;
        self::$cachedRawDuty = null;
        self::$permCheckCache = [];
        self::$cachedRole = null;
        self::$cachedRoleId = null;
    }

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

    /**
     * Resolves active duty context (office_id and role_id) set in session('current_duty').
     * If session('current_duty') is null or empty, returns null duty (0 DB queries).
     *
     * @return array ['office_id' => int|null, 'role_id' => int|null]
     */
    public static function getCurrentDuty(): array
    {
        $currentRaw = session('current_duty');

        if (self::$dutyCache !== null && self::$cachedRawDuty === $currentRaw) {
            return self::$dutyCache;
        }

        self::$cachedRawDuty = $currentRaw;
        $duty = $currentRaw;

        if (!empty($duty)) {
            if (is_string($duty)) {
                try {
                    $duty = Crypt::decrypt($duty);
                } catch (\Exception $e) {
                    try {
                        $duty = json_decode(Crypt::decryptString($duty), true);
                    } catch (\Exception $e2) {
                        $duty = null;
                    }
                }
            }

            if (is_array($duty) && !empty($duty['office_id']) && !empty($duty['role_id'])) {
                self::$dutyCache = [
                    'office_id'   => (int) $duty['office_id'],
                    'role_id'     => (int) $duty['role_id'],
                ];
                return self::$dutyCache;
            }
        }

        self::$dutyCache = [
            'office_id'   => null,
            'role_id'     => null,
        ];

        return self::$dutyCache;
    }

    /**
     * Checks permission specifically for the selected duty role_id using Spatie's native hasPermissionTo().
     * Caches the Role model instance in memory until session duty role_id changes.
     */
    public static function hasPermission($permissionKey, $schemeId = null): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }

        $duty = self::getCurrentDuty();
        $roleId = $duty['role_id'] ?? null;
        $officeId = $duty['office_id'] ?? null;

        if (! $roleId || ! $officeId) {
            return false;
        }

        $schemeId = $schemeId ?? self::getSchemeId();
        $cacheKey = $roleId . '_' . ($schemeId ?? '0') . '_' . $permissionKey;

        if (isset(self::$permCheckCache[$cacheKey])) {
            return self::$permCheckCache[$cacheKey];
        }

        // Fetch Role model from static cache, re-querying only if role_id mismatches
        if (self::$cachedRole !== null && self::$cachedRoleId === (int) $roleId) {
            $role = self::$cachedRole;
        } else {
            $role = Role::select(['id', 'name', 'guard_name'])->find($roleId);
            self::$cachedRole = $role;
            self::$cachedRoleId = (int) $roleId;
        }

        if (! $role) {
            self::$permCheckCache[$cacheKey] = false;
            return false;
        }

        $registrar = app(PermissionRegistrar::class);
        $originalTeamId = $registrar->getPermissionsTeamId();

        if ($schemeId) {
            $registrar->setPermissionsTeamId((int) $schemeId);
            $user->unsetRelation('roles')->unsetRelation('permissions');
        }

        $hasPerm = false;
        try {
            // Spatie's native hasPermissionTo check on the active duty Role model
            $hasPerm = $role->hasPermissionTo($permissionKey);
        } catch (\Throwable $e) {
            $hasPerm = false;
        }

        if ($schemeId) {
            $registrar->setPermissionsTeamId($originalTeamId);
            $user->unsetRelation('roles')->unsetRelation('permissions');
        }

        self::$permCheckCache[$cacheKey] = $hasPerm;
        return $hasPerm;
    }

    /**
     * Permission check for Operator
     */
    public static function canFormEntry($schemeId = null): bool
    {
        return self::hasPermission('submit-lb-form', $schemeId);
    }

    /**
     * Permission check for Verifier and Approver
     */
    public static function canViewApplications($schemeId = null): bool
    {
        return self::hasPermission('lb-application-list', $schemeId);
    }

    /**
     * Permission check for HOD
     */
    public static function canViewApplicationsHod($schemeId = null): bool
    {
        return self::hasPermission('application recommanded', $schemeId) || self::hasPermission('hod-application-list', $schemeId);
    }

    /**
     * Permission check for any LB duty menu item (Operator, Verifier/Approver, HOD)
     */
    public static function canAnyDutyMenu($schemeId = null): bool
    {
        return self::canFormEntry($schemeId)
            || self::canViewApplications($schemeId)
            || self::canViewApplicationsHod($schemeId);
    }
}
