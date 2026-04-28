@php
// Map color names to their hex values
$colorMap = [
'blue' => '#3b82f6',
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
'teal' => '#14b8a6',
];

$bgColor = $colorMap[$ref_color] ?? '#6366f1'; // Default to indigo
$textColor = $colorMap[$ref_color] ?? '#6366f1';
@endphp

<div class="card-carousel-item original-item flex-shrink-0  w-full sm:w-1/2 lg:w-1/3 px-4 mb-8">
    <div class="perspective [perspective:1000px]">
        <div
            class="card-inner relative w-full h-80 transform transition-transform duration-700 [transform-style:preserve-3d] hover:[transform:rotateY(180deg)]">

            <div class="card-front absolute w-full h-full rounded-xl shadow-lg flex flex-col items-center justify-center text-center text-white [backface-visibility:hidden]"
                style="background-color: {{ $bgColor }};">
                <img src="{{ asset('images/home/biswo_logo.png') }}"
                    class="w-20 h-20 mb-6 object-contain rounded-full bg-white p-1 shadow-md" />
                <h3 class="font-bold text-xl">{{ $name }}</h3>
            </div>

            <div
                class="card-back absolute w-full h-full bg-white rounded-xl shadow-lg p-6 flex flex-col justify-center text-center [transform:rotateY(180deg)] [backface-visibility:hidden]">
                <h3 class="font-bold text-xl mb-3 text-gray-800">{{ $name }}</h3>
                <p class="text-gray-600 mb-4">{{ $about }}</p>
                <a href="{{ route('department_info', ['department' => $slug]) }}" class="font-semibold hover:underline"
                    style="color: {{ $textColor }};">
                    Learn More →
                </a>
            </div>

        </div>
    </div>
</div>