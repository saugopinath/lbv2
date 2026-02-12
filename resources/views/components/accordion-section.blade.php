@props([
    'title' => '',
    'sectionId' => '',
    'color' => 'gray-500'
])

@php
    $colorMap = [
        'slate-500' => '#64748b',
        'gray-500' => '#6b7280',
        'zinc-500' => '#71717a',
        'neutral-500' => '#737373',
        'stone-500' => '#78716c',
        'red-500' => '#ef4444',
        'orange-500' => '#f97316',
        'amber-500' => '#f59e0b',
        'yellow-500' => '#eab308',
        'lime-500' => '#84cc16',
        'green-500' => '#22c55e',
        'emerald-500' => '#10b981',
        'teal-500' => '#14b8a6',
        'cyan-500' => '#06b6d4',
        'sky-500' => '#0ea5e9',
        'blue-500' => '#3b82f6',
        'indigo-500' => '#6366f1',
        'violet-500' => '#8b5cf6',
        'purple-500' => '#a855f7',
        'fuchsia-500' => '#d946ef',
        'pink-500' => '#ec4899',
        'rose-500' => '#f43f5e',
    ];
    
    // Fallback if the specific color-shade combination isn't in the map
    // This simple map covers the 500 shade usage seen in the codebase.
    // If other shades are used, they will fallback to gray-500 or need to be added.
    $resolvedColor = $colorMap[$color] ?? $colorMap['gray-500'];
@endphp

<div class="rounded overflow-hidden">
    <button @click="toggleSection('{{ $sectionId }}')"
        class="w-full flex justify-between items-center text-left p-3 bg-gray-200 font-semibold">
        <div class="flex items-center space-x-3">
            <span class="h-6 w-1 rounded-full" style="background-color: {{ $resolvedColor }}"></span>
            <span>{{ $title }}</span>
        </div>
        <svg x-show="openSection !== '{{ $sectionId }}'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" class="h-6 w-6 text-gray-600 transition-transform duration-300">
            <path d="M320 576C461.4 576 576 461.4 576 320C576 178.6 461.4 64 320 64C178.6 64 64 178.6 64 320C64 461.4 178.6 576 320 576zM296 408L296 344L232 344C218.7 344 208 333.3 208 320C208 306.7 218.7 296 232 296L296 296L296 232C296 218.7 306.7 208 320 208C333.3 208 344 218.7 344 232L344 296L408 296C421.3 296 432 306.7 432 320C432 333.3 421.3 344 408 344L344 344L344 408C344 421.3 333.3 432 320 432C306.7 432 296 421.3 296 408z" />
        </svg>
        <svg x-show="openSection === '{{ $sectionId }}'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" class="h-6 w-6 text-gray-600 transition-transform duration-300">
            <path d="M320 576C461.4 576 576 461.4 576 320C576 178.6 461.4 64 320 64C178.6 64 64 178.6 64 320C64 461.4 178.6 576 320 576zM232 344C218.7 344 208 333.3 208 320C208 306.7 218.7 296 232 296L408 296C421.3 296 432 306.7 432 320C432 333.3 421.3 344 408 344L232 344z" />
        </svg>
    </button>
    <div x-show="openSection === '{{ $sectionId }}'" x-transition.opacity
     class="transition duration-500 p-4 bg-green-50 shadow border-l-4 space-x-2"
     style="border-color: {{ $resolvedColor }}">
        {{ $slot }}
    </div>
</div>
