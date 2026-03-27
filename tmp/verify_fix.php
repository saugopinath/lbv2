<?php

use App\Models\User;
use App\Models\UserRoleSchemeOfficeMapping;
use App\Helpers\WorkFlowPermissionHelper;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;

// Mock a user and their mappings
$user = User::first();
if (!$user) {
    echo "No user found in DB.\n";
    exit;
}

$mapping = UserRoleSchemeOfficeMapping::where('user_id', $user->id)->first();
if (!$mapping) {
    echo "No mapping found for user {$user->id}.\n";
    exit;
}

echo "Testing for User ID: {$user->id}, Scheme ID: {$mapping->scheme_id}\n";

// Mock Session for Dashboard simulation
$lgd_session = [
    'scheme_id' => Crypt::encryptString($mapping->scheme_id),
];
Session::put('lgd_session', $lgd_session);

// 1. Test WorkFlowPermissionHelper::hasPermission
echo "Testing WorkFlowPermissionHelper::hasPermission...\n";
auth()->login($user);
// We don't know the exact permission name, but we can check if it sets the team ID correctly.
WorkFlowPermissionHelper::hasPermission('some-random-permission');

$teamId = app(PermissionRegistrar::class)->getPermissionsTeamId();
echo "Detected Team ID in Registrar: " . ($teamId ?? 'NULL') . "\n";

if ($teamId == $mapping->scheme_id) {
    echo "SUCCESS: Team ID correctly set from encrypted lgd_session.\n";
} else {
    echo "FAILURE: Team ID mismatch.\n";
}

// 2. Test without encrypted session but with root session
Session::forget('lgd_session');
Session::put('scheme_id', $mapping->scheme_id);
WorkFlowPermissionHelper::hasPermission('some-random-permission');
$teamId = app(PermissionRegistrar::class)->getPermissionsTeamId();
echo "Detected Team ID from root session: " . ($teamId ?? 'NULL') . "\n";

if ($teamId == $mapping->scheme_id) {
    echo "SUCCESS: Team ID correctly set from root session.\n";
} else {
    echo "FAILURE: Team ID mismatch.\n";
}
