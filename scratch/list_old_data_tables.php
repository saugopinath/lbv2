<?php
use Illuminate\Support\Facades\DB;
include 'vendor/autoload.php';
$app = include 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$results = DB::select("SELECT table_schema, table_name FROM information_schema.columns WHERE column_name = 'old_data' LIMIT 20");
echo json_encode($results);
