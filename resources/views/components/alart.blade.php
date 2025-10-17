@php
    $colors = [
        'xerror'   => 'bg-red-100 text-red-800 border border-red-400',
        'xwarning' => 'bg-yellow-100 text-yellow-800 border border-yellow-400',
        'xinfo'    => 'bg-blue-100 text-blue-800 border border-blue-400',
    ];
@endphp

@foreach (['xerror', 'xwarning', 'xinfo'] as $type)
    @if (session($type))
        <div 
            x-data="{ show: true }" 
            x-show="show"
            x-transition:leave="transition ease-in duration-500"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="mb-4 p-3 mt-3 rounded-lg shadow-sm flex justify-between items-center {{ $colors[$type] }}"
        >
            <span class="font-medium">{{ session($type) }}</span>
            <button 
                @click="show = false" 
                class="ml-3 text-lg font-bold text-current hover:opacity-70 focus:outline-none"
            >
                &times;
            </button>
        </div>
    @endif
@endforeach
