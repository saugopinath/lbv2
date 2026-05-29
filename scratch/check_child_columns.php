<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

foreach (['member_employment_natures', 'member_govt_schemes', 'member_other_ids'] as $table) {
    echo "\nColumns in dbt_apy.{$table}:\n";
    $columns = DB::connection('pgsql_annapurna')->select("
        SELECT column_name, data_type 
        FROM information_schema.columns 
        WHERE table_schema = 'dbt_apy' AND table_name = '{$table}'
        ORDER BY ordinal_position
    ");
    foreach ($columns as $c) {
        echo "- " . $c->column_name . " (" . $c->data_type . ")\n";
    }
}
