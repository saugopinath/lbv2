<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

function getMasterDataArray($filename, $varName)
{
    $filePath = public_path('js/master-data/' . $filename);
    if (! file_exists($filePath)) {
        $filePath = base_path('public/js/master-data/' . $filename);
        if (! file_exists($filePath)) {
            return ["error" => "File not found: " . $filePath];
        }
    }

    $content = file_get_contents($filePath);
    $startPos = strpos($content, '[');
    $endPos = strrpos($content, ']');
    if ($startPos === false || $endPos === false) {
        return ["error" => "Brackets not found"];
    }

    $jsArrayStr = substr($content, $startPos, $endPos - $startPos + 1);

    // Normalize JavaScript keys to valid double-quoted JSON keys
    $jsonStr = preg_replace('/([{,]\s*)([a-zA-Z_][a-zA-Z0-9_]*)\s*:/', '$1"$2":', $jsArrayStr);
    // Remove trailing commas before closing braces/brackets
    $jsonStr = preg_replace('/,\s*([}\]])/', '$1', $jsonStr);
    // Strip JS comments
    $jsonStr = preg_replace('!/\*.*?\*/!s', '', $jsonStr);
    $jsonStr = preg_replace('!//.*?[\r\n]!', '', $jsonStr);

    $decoded = json_decode($jsonStr, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        return ["error" => "JSON decode error: " . json_last_error_msg(), "raw" => substr($jsonStr, 0, 500)];
    }

    return $decoded;
}

$assemblies = getMasterDataArray('assemblies.js', 'assemblies');
if (isset($assemblies['error'])) {
    print_r($assemblies);
} else {
    echo "Successfully loaded " . count($assemblies) . " assemblies.\n";
    echo "First 3 assemblies:\n";
    print_r(array_slice($assemblies, 0, 3));
}
