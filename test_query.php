<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $table = app()->make(App\Livewire\Frontend\TrackBen\PaymentStatusTable::class);
    $table->scheme_id = 20;
    $table->ben_id = 1;
    $table->fin_year = '2025-2026';
    $cols = $table->columns();
    echo "Columns set up correctly: " . count($cols) . "\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n" . $e->getTraceAsString();
}
