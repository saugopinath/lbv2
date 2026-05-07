<?php

$portal = config('app.app_portal', 'lb');
if ($portal == 'lb') {
    $title = 'Annapurna Bhandar | Government of West Bengal';
    $headLine = 'Annapurna Bhandar';
    $logo = 'biswo-1.png';
    $headerlogo = 'Emblem_of_India.png';
    $logo_class = 'w-48 sm:w-64 mb-4';
    $das_logo = 'biswo-1.png';
    $logo_das_width = 'w-8';
    $das_logo_class = 'p-1';
    $bg_image = 'background-cover.jpg';
    $schemeIds = [20];
    $footerText = 'Design and develpod By NIC.';
    $deptName = 'Women and Child Development & Social Welfare Department';
    $indexName = 'Annapurna Bhandar | Government of West Bengal';
    $initiallogo = 'biswo-1.png';
    $dept_logo = 'biswo_logo.png';
} elseif ($portal == 'jb') {
    $title = 'Jai Bangla Portal | Government of West Bengal';
    $headLine = 'Jai Bangla';
    $logo = 'jb_logo.png';
    $headerlogo = 'Emblem_of_India.png';
    $logo_class = 'w-56 sm:w-64 mb-4';
    $das_logo = 'biswo_bangla.png';
    $logo_das_width = 'w-20';
    $das_logo_class = 'p-2';
    $bg_image = 'jb-bg.jpg';
    $schemeIds = [1, 2, 3, 5, 6, 7, 8, 9, 10, 11, 13, 17, 19, 20];
    $footerText = 'Design and develpod By NIC.';
    $deptName = 'Finance Department';
    $dept_logo = 'biswo_logo.png';
    $initiallogo = 'jb_logo.png';
    $indexName = 'Department of Finance | Government of West Bengal';
} else if ($portal == 'ub') {
    $title = 'Unnayan Bangla | Government of West Bengal';
    $headLine = 'Unnayan Bangla';
    $logo = 'ub_logo.png';
    $headerlogo = 'header_logo.png';
    $initiallogo = 'ub_logo.png';
    $logo_class = 'w-56 sm:w-64 mb-4';
    $das_logo = 'ub_logo.png';
    $logo_das_width = 'w-12';
    $das_logo_class = 'p-2';
    $bg_image = 'jb-bg.jpg';
    $schemeIds = [1, 2, 3, 5, 6, 7, 8, 9, 10, 11, 13, 17, 19, 20];
    $footerText = 'Design and develpod By NIC.';
    $deptName = 'Finance Department';
    $indexName = 'Finance Department | Government of West Bengal';
    $dept_logo = 'biswo_logo.png';
} else {
    $title = 'Government of West Bengal';
    $headLine = 'Government of West Bengal';
    $logo = 'biswo_bangla.png';
    $headerlogo = 'header_logo.png';
    $initiallogo = 'biswo_bangla.png';
    $logo_class = 'w-48 sm:w-64 mb-4';
    $das_logo = 'biswo_bangla.png';
    $logo_das_width = 'w-8';
    $das_logo_class = 'p-1';
    $bg_image = 'testimonial-bg1.png';
    $schemeIds = [21];
    $footerText = 'Design and develpod By NIC.';
    $deptName = 'WCD';
    $dept_logo = 'biswo_logo.png';
    $indexName = 'Department of Finance | Government of West Bengal';
}
return [
    'app_portal' => $portal,
    'title' => $title,
    'headLine' => $headLine,
    'indexName' => $indexName,
    'logo' => $logo,
    'headerlogo' => $headerlogo,
    'logo_class' => $logo_class,
    'das_logo' => $das_logo,
    'das_logo_class' => $das_logo_class,
    'logo_das_width' => $logo_das_width,
    'is_jb' => ($portal == 'jb'),
    'is_lb' => ($portal == 'lb'),
    'bg_image' => $bg_image,
    'schemeIds' => $schemeIds,
    'footerText' => $footerText,
    'deptName' => $deptName,
    'initiallogo' => $initiallogo,
    'dept_logo' => $dept_logo,
];
