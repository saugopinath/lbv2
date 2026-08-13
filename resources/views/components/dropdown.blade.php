@props(['align' => 'right', 'width' => '48', 'contentClasses' => 'py-1 bg-white dark:bg-gray-800'])

@php
switch ($align) {
case 'left':
$alignmentClasses = 'ltr:origin-top-left rtl:origin-top-right start-0';
break;
case 'top':
$alignmentClasses = 'origin-top';
break;
case 'right':
default:
$alignmentClasses = 'ltr:origin-top-right rtl:origin-top-left end-0';
break;
}

switch ($width) {
case '32':
$width = 'w-32';
break;
case '48':
$width = 'w-48';
break;
case '56':
$width = 'w-56';
break;
case '60':
$width = 'w-60';
break;
case '64':
$width = 'w-64';
break;
default:
$width = $width;
break;
}
@endphp

<div class="relative" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">
    <!-- Trigger -->
    <div @click="open = !open" class="cursor-pointer">
        {{ $trigger }}
    </div>

    <!-- Dropdown Menu -->
    <div x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute z-50 mt-2 {{ $width }} {{ $alignmentClasses }}"
        style="display: none;">

        <div class="rounded-xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden {{ $contentClasses }}">
            {{ $content }}
        </div>
    </div>
</div>