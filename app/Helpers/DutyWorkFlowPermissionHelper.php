<?php

namespace App\Helpers;

use App\Models\Role;
use App\Models\UserRoleSchemeOfficeMapping;
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
     * Resolves the office type string (state, district, subdivision, block)
     * based on office_type_id or associated geographic identifiers.
     */
    public static function resolveOfficeType($office): string
    {
        if (!$office) {
            return 'district';
        }

        $typeId = (int) ($office->office_type_id ?? 0);
        if ($typeId === 151) {
            return 'state';
        }
        if ($typeId === 152) {
            return 'district';
        }
        if ($typeId === 153) {
            return 'block';
        }
        if ($typeId === 154) {
            return 'subdivision';
        }

        if (!empty($office->block_id)) {
            return 'block';
        }
        if (!empty($office->subdivision_id) || !empty($office->municipalitiy_id)) {
            return 'subdivision';
        }
        $typeName = strtolower(($office->officeType?->short_name ?? '') . ' ' . ($office->officeType?->name ?? '') . ' ' . ($office->name ?? ''));
        if (str_contains($typeName, 'block') || str_contains($typeName, 'bdo') || str_contains($typeName, 'rural')) {
            return 'block';
        }
        if (str_contains($typeName, 'sdo') || str_contains($typeName, 'subdiv') || str_contains($typeName, 'muni') || str_contains($typeName, 'urban') || str_contains($typeName, 'corp')) {
            return 'subdivision';
        }
        if (str_contains($typeName, 'state') || str_contains($typeName, 'hod')) {
            return 'state';
        }

        if (!empty($office->district_id)) {
            return 'district';
        }
        if (!empty($office->state_id)) {
            return 'state';
        }

        return 'district';
    }

    /**
     * Resolves the scheme_list array with id and name for the user's active duty.
     * Compatible with in-memory extraction in SchemeDropdownNew::mount().
     *
     * @return array<int, array{id: int, name: string, short_name: string, is_active: int}>
     */
    public static function resolveSchemeList($user, int $officeId, int $roleId): array
    {
        // 1. Fetch schemes mapped specifically to this user, office, and role
        $schemes = UserRoleSchemeOfficeMapping::with('Scheme')
            ->where('user_id', $user->id)
            ->where('office_id', $officeId)
            ->where('role_id', $roleId)
            ->get()
            ->pluck('Scheme')
            ->filter()
            ->unique('id');

        // // 2. If empty, check all mappings for this user
        // if ($schemes->isEmpty()) {
        //     $schemes = UserRoleSchemeOfficeMapping::with('Scheme')
        //         ->where('user_id', $user->id)
        //         ->get()
        //         ->pluck('Scheme')
        //         ->filter()
        //         ->unique('id');
        // }

        // // 3. Fallback: If still empty (e.g. Super Admin / HOD without explicit scheme rows), load all active schemes
        // if ($schemes->isEmpty()) {
        //     $schemes = Scheme::where('is_active', 1)
        //         ->select('id', 'name', 'short_name', 'is_active')
        //         ->orderBy('name')
        //         ->get();
        // }

        return $schemes->map(fn($s) => [
            'id'         => (int) $s->id,
            'name'       => (string) $s->name,
            'short_name' => (string) ($s->short_name ?? ''),
            'is_active'  => (int) ($s->is_active ?? 1),
        ])->values()->toArray();
    }

    /**
     * Resolves active duty context (office_id, office_type, role_id, scheme_list, etc.)
     * from session('current_duty'). If not present or incorrect, automatically falls back
     * to fetching the user's role and office mapping and sets session('current_duty').
     *
     * @return array
     */
    public static function getCurrentDuty(): array
    {
        $currentRaw = session('current_duty');
        $user = auth()->user();

        // 1. Check in-memory static cache
        if (self::$dutyCache !== null && self::$cachedRawDuty === $currentRaw && !empty(self::$dutyCache['office_id'])) {
            if (!$user || !isset(self::$dutyCache['user_id']) || (int) self::$dutyCache['user_id'] === (int) $user->id) {
                return self::$dutyCache;
            }
        }

        $duty = $currentRaw;

        // 2. Try to decrypt from session if present
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

            // Verify if duty is present and completely correct
            if (
                is_array($duty)
                && !empty($duty['office_id'])
                && !empty($duty['role_id'])
                && !empty($duty['office_type'])
                && isset($duty['scheme_list'])
                && is_array($duty['scheme_list'])
                && (!$user || !isset($duty['user_id']) || (int) $duty['user_id'] === (int) $user->id)
            ) {
                $duty['office_id'] = (int) $duty['office_id'];
                $duty['role_id'] = (int) $duty['role_id'];

                // Ensure it is encrypted in session if it was stored as raw array
                if (!is_string($currentRaw)) {
                    $encryptedDuty = Crypt::encrypt($duty);
                    session(['current_duty' => $encryptedDuty]);
                    self::$cachedRawDuty = $encryptedDuty;
                } else {
                    self::$cachedRawDuty = $currentRaw;
                }

                self::$dutyCache = $duty;
                return self::$dutyCache;
            }
        }

        // 3. Fallback: Fetch logged-in user's first role & office mapping from DB and set into session
        if ($user) {
            $schemeId = self::getSchemeId();
            $query = UserRoleSchemeOfficeMapping::with(['Office', 'Office.officeType', 'Role', 'Scheme'])
                ->where('user_id', $user->id);

            if ($schemeId) {
                $query->where('scheme_id', $schemeId);
            }

            $firstMapping = $query->first()
                ?? UserRoleSchemeOfficeMapping::with(['Office', 'Office.officeType', 'Role', 'Scheme'])
                ->where('user_id', $user->id)
                ->first();

            if ($firstMapping) {
                $office = $firstMapping->Office;
                $officeId = (int) $firstMapping->office_id;
                $roleId = (int) $firstMapping->role_id;
                $officeType = self::resolveOfficeType($office);
                $schemeList = self::resolveSchemeList($user, $officeId, $roleId);

                $dutyData = [
                    'user_id'          => (int) $user->id,
                    'office_id'        => $officeId,
                    'office_type'      => $officeType,
                    'role_id'          => $roleId,
                    'scheme_list'      => $schemeList,
                    // 'scheme_id'        => $firstMapping->scheme_id ? (int) $firstMapping->scheme_id : null,
                    // 'office_name'      => $office?->name ?? null,
                    // 'office_type_id'   => $office?->office_type_id ? (int) $office->office_type_id : null,
                    // 'role_name'        => $firstMapping->Role?->name ?? null,
                    // 'state_id'         => $office?->state_id ? (int) $office->state_id : null,
                    // 'district_id'      => $office?->district_id ? (int) $office->district_id : null,
                    // 'subdivision_id'   => $office?->subdivision_id ? (int) $office->subdivision_id : null,
                    // 'block_id'         => $office?->block_id ? (int) $office->block_id : null,
                    // 'panchayat_id'     => $office?->panchayat_id ? (int) $office->panchayat_id : null,
                    // 'municipalitiy_id' => $office?->municipalitiy_id ? (int) $office->municipalitiy_id : null,
                    // 'ward_id'          => $office?->ward_id ? (int) $office->ward_id : null,
                ];

                // Encrypt and persist into session
                $encryptedDuty = Crypt::encrypt($dutyData);
                session(['current_duty' => $encryptedDuty]);

                self::$cachedRawDuty = $encryptedDuty;
                self::$dutyCache = $dutyData;

                return self::$dutyCache;
            }
        }

        // Default empty duty context if user or mapping not found
        $emptyDuty = [
            'office_id'   => null,
            'office_type' => null,
            'role_id'     => null,
            'scheme_list' => [],
        ];

        self::$cachedRawDuty = null;
        self::$dutyCache = $emptyDuty;

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

        if ($schemeId) {
            $dutySchemes = [];
            if (!empty($duty['scheme_list'])) {
                $raw = is_string($duty['scheme_list']) ? json_decode($duty['scheme_list'], true) : $duty['scheme_list'];
                $dutySchemes = collect($raw)->map(fn($item) => (int)(is_array($item) || is_object($item) ? data_get($item, 'id') : $item))->filter()->toArray();
            }
            if (empty($dutySchemes)) {
                $dutySchemes = UserRoleSchemeOfficeMapping::where('user_id', $user->id)
                    ->where('office_id', $officeId)
                    ->where('role_id', $roleId)
                    ->pluck('scheme_id')
                    ->map(fn($id) => (int)$id)
                    ->toArray();
            }
            if (!empty($dutySchemes) && !in_array((int)$schemeId, $dutySchemes, true)) {
                self::$permCheckCache[$cacheKey] = false;
                return false;
            }
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
