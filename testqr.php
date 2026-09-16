<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo class_exists('BaconQrCode\Writer') ? 'bacon_exists' : 'bacon_missing';
} catch (Exception $e) {
    echo $e->getMessage();
}
