@php
    // Map color names to their hex values
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
    ];

    // Get the color hex value, default to indigo if not found
    $colorHex = $colorMap[$color] ?? '#6366f1';

    // Calculate lighter shade for background (add opacity)
    $bgColor = $colorHex . '1A'; // 10% opacity
    $borderColor = $colorHex . '66'; // 40% opacity
@endphp

<a href="{{ route('scheme_info', ['scheme' => $slug]) }}"
    class="department-card block bg-white rounded-lg shadow-md hover:shadow-lg transition-all duration-300 p-4 border border-gray-200"
    style="--hover-border-color: {{ $borderColor }};" onmouseenter="this.style.borderColor = '{{ $borderColor }}'"
    onmouseleave="this.style.borderColor = '#e5e7eb'">
    <div class="flex items-center">
        <div class="w-12 h-12 rounded-lg flex items-center justify-center mr-4"
            style="background-color: {{ $bgColor }};">
            <i class="{{$icon}} text-xl" style="color: {{ $colorHex }};"></i>
        </div>
        <h3 class="text-lg font-semibold text-gray-800">
            {{$name}}
        </h3>
    </div>
</a>