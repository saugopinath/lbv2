@props([
    'title' => '',
    'sectionId' => '',
    'color' => 'gray-500'
])

<div class="rounded overflow-hidden">
    <button @click="toggleSection('{{ $sectionId }}')"
        class="w-full flex justify-between items-center text-left p-3 bg-gray-200 font-semibold">
        <div class="flex items-center space-x-3">
            <span class="h-6 w-1 bg-{{ $color }} rounded-full"></span>
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
     class="transition duration-500 p-4 bg-green-50 shadow border-l-4 border-{{ $color }} space-x-2">
        {{ $slot }}
    </div>
</div>
