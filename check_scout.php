<?php
require 'vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$files = glob('app/Models/*.php');
foreach($files as $file) {
    if(strpos(file_get_contents($file), 'Laravel\Scout\Searchable') !== false) {
        $class = 'App\\Models\\' . basename($file, '.php');
        if(class_exists($class)) {
            $model = new $class;
            echo $class . " => " . $model->searchableAs() . "\n";
        }
    }
}
