<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "Columns in dbt_apy.family_members:\n";
    $columns = DB::connection('pgsql_annapurna')->select("
        SELECT column_name, data_type 
        FROM information_schema.columns 
        WHERE table_schema = 'dbt_apy' AND table_name = 'family_members'
        ORDER BY ordinal_position
    ");
    foreach ($columns as $c) {
        echo "- " . $c->column_name . " (" . $c->data_type . ")\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
