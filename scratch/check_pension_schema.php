<?php
use Illuminate\Support\Facades\DB;
include 'vendor/autoload.php';
$app = include 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$results = DB::select("SELECT table_name FROM information_schema.tables WHERE table_schema = 'pension' AND table_name LIKE '%accept%'");
echo json_encode($results);
