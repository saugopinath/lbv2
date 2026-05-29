<?php
// scratch/test_live_verhoeff.php

// Bootstrap Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Livewire\AnnapurnaYojanaForm;

$form = new AnnapurnaYojanaForm();

// Reflect validateVerhoeff method
$reflector = new ReflectionMethod(AnnapurnaYojanaForm::class, 'validateVerhoeff');
$reflector->setAccessible(true);

$aadhaar = '895555555555';
$result = $reflector->invoke($form, $aadhaar);

echo "validateVerhoeff('$aadhaar') result: " . ($result ? "TRUE (VALID)" : "FALSE (INVALID)") . "\n";

$rulesReflector = new ReflectionMethod(AnnapurnaYojanaForm::class, 'getValidationRulesFromJson');
$rulesReflector->setAccessible(true);
$rules = $rulesReflector->invoke($form);
echo "Rules parsed successfully: " . (!empty($rules) ? "YES" : "NO") . "\n";
if (empty($rules)) {
    echo "Path checked: " . public_path('js/masterData.json') . "\n";
    echo "File exists? " . (file_exists(public_path('js/masterData.json')) ? "YES" : "NO") . "\n";
}
