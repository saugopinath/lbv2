<?php
$content = file_get_contents('resources/views/livewire/annapurna-yojana-form.blade.php');

$tags = ['div', 'select', 'label', 'span', 'option', 'form'];

foreach ($tags as $tag) {
    $opens = substr_count(strtolower($content), "<$tag");
    $closes = substr_count(strtolower($content), "</$tag>");
    echo "$tag: opens=$opens, closes=$closes\n";
}
