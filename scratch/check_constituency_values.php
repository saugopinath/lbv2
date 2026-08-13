<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    $results = DB::connection('pgsql_annapurna')->select("
        SELECT assembly_constituency_no, COUNT(*) as count 
        FROM dbt_apy.family_members 
        WHERE assembly_constituency_no IS NOT NULL 
        GROUP BY assembly_constituency_no 
        LIMIT 10
    ");
    echo "Existing values in assembly_constituency_no:\n";
    foreach ($results as $r) {
        echo "- Value: '" . $r->assembly_constituency_no . "' (Count: " . $r->count . ")\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
