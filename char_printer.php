<?php
$content = file('resources/views/livewire/annapurna-yojana-form.blade.php');
$line = $content[671];
echo "Line: " . trim($line) . "\n";
for ($i = 0; $i < strlen($line); $i++) {
    $char = $line[$i];
    $code = ord($char);
    if ($code < 32 || $code > 126) {
        echo "[$code]";
    } else {
        echo $char;
    }
}
echo "\n";
