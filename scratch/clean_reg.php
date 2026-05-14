<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

use App\Helpers\SchemewiseStoreDataJsonHelper;

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$schemeId = 20;
$data = SchemewiseStoreDataJsonHelper::generateSchemeJson($schemeId);
SchemewiseStoreDataJsonHelper::storeSchemeJson($schemeId, $data);
SchemewiseStoreDataJsonHelper::store($schemeId, $data['tabs']);

echo "Done.\n";
