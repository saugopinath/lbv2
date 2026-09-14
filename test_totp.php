<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use PragmaRX\Google2FALaravel\Support\Authenticator;

$google2fa = app('pragmarx.google2fa');
$secret = $google2fa->generateSecretKey();
$currentTotp = $google2fa->getCurrentOtp($secret);

$isValid = $google2fa->verifyKey($secret, $currentTotp);

echo "Secret: $secret\n";
echo "Current TOTP: $currentTotp\n";
echo "Is Valid: " . ($isValid ? 'true' : 'false') . "\n";
