<?php
$portal = config('app.app_portal', 'lb');
if ($portal == 'lb') {
    $title = 'Lakshmir Bhandar | Government of West Bengal';
    $headLine = 'Lakshmir Bhandar';
    $logo = 'biswo-1.png';
    $headerlogo = 'biswo_bangla.png';
    $logo_class = 'w-48 sm:w-64 mb-4';
    $das_logo = 'biswo-1.png';
    $logo_das_width = 'w-8';
    $das_logo_class = 'p-1';
    $bg_image = 'background-cover.jpg';
} else if ($portal == 'jb') {
    $title = 'Jai Bangla Portal | Government of West Bengal';
    $headLine = 'Jai Bangla';
    $logo = 'jb_logo.png';
    $headerlogo = 'biswo_bangla.png';
    $logo_class = 'w-56 sm:w-64 mb-4';
    $das_logo = 'jb_logo.png';
    $logo_das_width = 'w-20';
    $das_logo_class = 'p-2';
    $bg_image = 'jb-bg.jpg';
} else {
    $title = 'Government of West Bengal';
    $headLine = 'Government of West Bengal';
    $logo = 'biswo_bangla.png';
    $headerlogo = 'biswo_bangla.png';
    $logo_class = 'w-48 sm:w-64 mb-4';
    $das_logo = 'biswo_bangla.png';
    $logo_das_width = 'w-8';
    $das_logo_class = 'p-1';
    $bg_image = 'testimonial-bg1.png';
}

return [
    'app_portal' => $portal,
    'title' => $title,
    'headLine' => $headLine,
    'logo' => $logo,
    'headerlogo' => $headerlogo,
    'logo_class' => $logo_class,
    'das_logo' => $das_logo,
    'das_logo_class' => $das_logo_class,
    'logo_das_width' => $logo_das_width,
    'is_jb' => ($portal == 'jb'),
    'is_lb' => ($portal == 'lb'),
    'bg_image' => $bg_image,
];
