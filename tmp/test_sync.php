<?php

use App\Models\User;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Support\Facades\DB;

$user = User::find(20);
if (!$user) {
    echo "User 20 not found.\n";
    exit;
}

$schemeId = 30; // Test scheme ID
echo "Syncing permissions for User 20, Scheme 30...\n";

app(PermissionRegistrar::class)->setPermissionsTeamId($schemeId);

// Sync some permissions
$user->syncPermissions([47, 51]);

$rows = DB::table('model_has_permissions')
    ->where('model_id', 20)
    ->get();

echo "Results for User 20:\n";
echo "PermID\tSchemeID\n";
foreach ($rows as $row) {
    echo "{$row->permission_id}\t" . ($row->scheme_id ?? 'NULL') . "\n";
}
