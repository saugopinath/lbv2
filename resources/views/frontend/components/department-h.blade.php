@php
// Map color names to their hex values
$colorMap = [
'blue' => ['bg' => 'from-blue-500 to-blue-600', 'accent' => '#3b82f6'],
'pink' => ['bg' => 'from-pink-500 to-pink-600', 'accent' => '#ec4899'],
'indigo' => ['bg' => 'from-indigo-500 to-indigo-600', 'accent' => '#6366f1'],
'green' => ['bg' => 'from-green-500 to-green-600', 'accent' => '#22c55e'],
'orange' => ['bg' => 'from-orange-500 to-orange-600', 'accent' => '#f97316'],
'violet' => ['bg' => 'from-violet-500 to-violet-600', 'accent' => '#8b5cf6'],
'lime' => ['bg' => 'from-lime-500 to-lime-600', 'accent' => '#84cc16'],
'sky' => ['bg' => 'from-sky-500 to-sky-600', 'accent' => '#0ea5e9'],
'amber' => ['bg' => 'from-amber-500 to-amber-600', 'accent' => '#f59e0b'],
'fuchsia' => ['bg' => 'from-fuchsia-500 to-fuchsia-600', 'accent' => '#d946ef'],
'rose' => ['bg' => 'from-rose-500 to-rose-600', 'accent' => '#f43f5e'],
'emerald' => ['bg' => 'from-emerald-500 to-emerald-600', 'accent' => '#10b981'],
'teal' => ['bg' => 'from-teal-500 to-teal-600', 'accent' => '#14b8a6'],
];

$selectedColor = $colorMap[$ref_color ?? 'indigo'] ?? $colorMap['indigo'];
$bgColor = $selectedColor['bg'];
$accentColor = $selectedColor['accent'];
@endphp

<div class="card-carousel-item original-item flex-shrink-0 w-full sm:w-1/2 lg:w-1/3 px-4 mb-8">
    <div class="perspective [perspective:1000px]">
        <div
            class="card-inner relative w-full h-80 transform transition-transform duration-700 [transform-style:preserve-3d] hover:[transform:rotateY(180deg)]">

            <div class="card-front absolute w-full h-full rounded-xl shadow-lg flex flex-col items-center justify-center text-center text-white bg-gradient-to-br {{ $bgColor }} [backface-visibility:hidden]">
                <img src="{{ asset('images/home/biswo_logo.png') }}"
                    class="w-20 h-20 mb-6 object-contain rounded-full bg-white p-1 shadow-md" />
                <h3 class="font-bold text-xl">{{ $name ?? 'Department Name' }}</h3>
            </div>

            <div
                class="card-back absolute w-full h-full bg-white rounded-xl shadow-lg p-6 flex flex-col justify-center text-center [transform:rotateY(180deg)] [backface-visibility:hidden]">
                <h3 class="font-bold text-xl mb-3 text-gray-800">{{ $name ?? 'Department Name' }}</h3>
                <p class="text-gray-600 mb-4">{{ $about ?? 'No description available' }}</p>
                <a href="{{ route('department_info', ['department' => $slug ?? '#']) }}" class="font-semibold hover:underline" style="color: {{ $accentColor }};">
                    Learn More →
                </a>
            </div>

        </div>
    </div>
</div>