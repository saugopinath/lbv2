<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "Adding created_by_dist_code and created_by_local_body_code to dbt_apy.families table if not exists...\n";
    
    DB::connection('pgsql_annapurna')->statement("
        ALTER TABLE dbt_apy.families 
        ADD COLUMN IF NOT EXISTS created_by_dist_code smallint,
        ADD COLUMN IF NOT EXISTS created_by_local_body_code integer
    ");
    
    echo "Alter statement executed successfully!\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
