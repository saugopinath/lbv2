<?php

use Illuminate\Support\Facades\DB;

$rows = DB::table('model_has_permissions')
    ->orderBy('model_id', 'desc')
    ->take(100)
    ->get();

echo "ID\tModel\tPermID\tSchemeID\n";
foreach ($rows as $row) {
    echo "{$row->model_id}\t{$row->model_type}\t{$row->permission_id}\t" . ($row->scheme_id ?? 'NULL') . "\n";
}
