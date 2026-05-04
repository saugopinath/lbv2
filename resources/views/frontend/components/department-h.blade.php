@php
// Map color names to their hex values and lighter variants
$colorMap = [
'blue' => ['bg' => 'bg-blue-50/80', 'border' => 'border-blue-100', 'text' => 'text-blue-900', 'accent' => '#3b82f6'],
'pink' => ['bg' => 'bg-pink-50/80', 'border' => 'border-pink-100', 'text' => 'text-pink-900', 'accent' => '#ec4899'],
'indigo' => ['bg' => 'bg-indigo-50/80', 'border' => 'border-indigo-100', 'text' => 'text-indigo-900', 'accent' => '#6366f1'],
'green' => ['bg' => 'bg-green-50/80', 'border' => 'border-green-100', 'text' => 'text-green-900', 'accent' => '#22c55e'],
'orange' => ['bg' => 'bg-orange-50/80', 'border' => 'border-orange-100', 'text' => 'text-orange-900', 'accent' => '#f97316'],
'violet' => ['bg' => 'bg-violet-50/80', 'border' => 'border-violet-100', 'text' => 'text-violet-900', 'accent' => '#8b5cf6'],
'lime' => ['bg' => 'bg-lime-50/80', 'border' => 'border-lime-100', 'text' => 'text-lime-900', 'accent' => '#84cc16'],
'sky' => ['bg' => 'bg-sky-50/80', 'border' => 'border-sky-100', 'text' => 'text-sky-900', 'accent' => '#0ea5e9'],
'amber' => ['bg' => 'bg-amber-50/80', 'border' => 'border-amber-100', 'text' => 'text-amber-900', 'accent' => '#f59e0b'],
'fuchsia' => ['bg' => 'bg-fuchsia-50/80', 'border' => 'border-fuchsia-100', 'text' => 'text-fuchsia-900', 'accent' => '#d946ef'],
'rose' => ['bg' => 'bg-rose-50/80', 'border' => 'border-rose-100', 'text' => 'text-rose-900', 'accent' => '#f43f5e'],
'emerald' => ['bg' => 'bg-emerald-50/80', 'border' => 'border-emerald-100', 'text' => 'text-emerald-900', 'accent' => '#10b981'],
'teal' => ['bg' => 'bg-teal-50/80', 'border' => 'border-teal-100', 'text' => 'text-teal-900', 'accent' => '#14b8a6'],
];

$selectedColor = $colorMap[$ref_color ?? 'indigo'] ?? $colorMap['indigo'];
$bgColor = $selectedColor['bg'];
$borderColor = $selectedColor['border'];
$textColor = $selectedColor['text'];
$accentColor = $selectedColor['accent'];
@endphp

<div class="card-carousel-item original-item flex-shrink-0 w-full sm:w-1/2 lg:w-1/3 px-4 mb-8">
    <div class="perspective [perspective:1000px]">
        <div
            class="card-inner relative w-full h-80 transform transition-transform duration-700 [transform-style:preserve-3d] hover:[transform:rotateY(180deg)]">

            <div class="card-front absolute w-full h-full rounded-2xl shadow-sm border {{ $borderColor }} flex flex-col items-center justify-center text-center {{ $textColor }} {{ $bgColor }} backdrop-blur-md [backface-visibility:hidden]">
                <img src="{{ asset('images/home/biswo_logo.png') }}"
                    class="w-20 h-20 mb-6 object-contain rounded-full bg-white p-1 shadow-md" />
                <h3 class="font-bold text-xl">{{ $name ?? 'Department Name' }}</h3>
            </div>

            <div
                class="card-back absolute w-full h-full bg-white rounded-2xl shadow-sm border {{ $borderColor }} p-6 flex flex-col justify-center text-center [transform:rotateY(180deg)] [backface-visibility:hidden]">
                <h3 class="font-bold text-xl mb-3 text-gray-800">{{ $name ?? 'Department Name' }}</h3>
                <p class="text-gray-600 mb-4">{{ $about ?? 'No description available' }}</p>
                <a href="{{ route('department_info', ['department' => $slug ?? '#']) }}" class="font-semibold hover:underline" style="color: {{ $accentColor }};">
                    Learn More →
                </a>
            </div>

        </div>
    </div>
</div>