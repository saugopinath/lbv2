@php
    /**
     * Color Helper for Tailwind v4 Compatibility
     * 
     * This component provides a centralized color mapping system to convert
     * color names to hex values, ensuring compatibility with Tailwind v4.
     * 
     * Usage:
     * @include('frontend.components.color-helper', ['colorName' => 'indigo'])
     * Then use: $colorHex, $colorLight, $colorLighter, $colorDark
     */

    // Define the color map
    $colorMap = [
        'pink' => '#ec4899',
        'indigo' => '#6366f1',
        'green' => '#22c55e',
        'orange' => '#f97316',
        'violet' => '#8b5cf6',
        'lime' => '#84cc16',
        'sky' => '#0ea5e9',
        'amber' => '#f59e0b',
        'fuchsia' => '#d946ef',
        'rose' => '#f43f5e',
        'emerald' => '#10b981',
        'blue' => '#3b82f6',
        'teal' => '#14b8a6',
        'red' => '#ef4444',
        'yellow' => '#eab308',
        'purple' => '#a855f7',
        'cyan' => '#06b6d4',
    ];

    // Get the color name from the parameter
    $colorName = $colorName ?? 'indigo';

    // Get the base color hex value
    $colorHex = $colorMap[$colorName] ?? '#6366f1'; // Default to indigo

    // Generate color variations with opacity
    $colorLight = $colorHex . '1A';      // 10% opacity (for bg-{color}-50)
    $colorLighter = $colorHex . '33';    // 20% opacity (for bg-{color}-100)
    $colorBorder = $colorHex . '66';     // 40% opacity (for border-{color}-200)
    $colorDark = $colorHex . 'CC';       // 80% opacity (for darker shades)

    // For text colors (darker shades)
    $textColorLight = $colorHex . '99';  // 60% opacity (for text-{color}-600)
    $textColorDark = $colorHex;          // 100% opacity (for text-{color}-700/800)

    // For hover states
    $hoverBg = $colorHex . 'E6';         // 90% opacity
    $hoverBorder = $colorHex . '99';     // 60% opacity
@endphp
