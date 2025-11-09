@php
    $colors = [
        'xerror'   => 'bg-red-100 text-red-800 border border-red-400',
        'xwarning' => 'bg-yellow-100 text-yellow-800 border border-yellow-400',
        'xinfo'    => 'bg-blue-100 text-blue-800 border border-blue-400',
    ];
@endphp

@foreach (['xerror', 'xwarning', 'xinfo'] as $type)
    @if (session($type))
<<<<<<< HEAD
        <div 
            x-data="{ show: true }" 
=======
        <div
            x-data="{ show: true }"
>>>>>>> d726694e2ff4cbf8a12d9642a72f953c3c34c7b5
            x-show="show"
            x-transition:leave="transition ease-in duration-500"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="mb-4 p-3 mt-3 rounded-lg shadow-sm flex justify-between items-center {{ $colors[$type] }}"
        >
            <span class="font-medium">{{ session($type) }}</span>
<<<<<<< HEAD
            <button 
                @click="show = false" 
=======
            <button
                @click="show = false"
>>>>>>> d726694e2ff4cbf8a12d9642a72f953c3c34c7b5
                class="ml-3 text-lg font-bold text-current hover:opacity-70 focus:outline-none"
            >
                &times;
            </button>
        </div>
    @endif
@endforeach
