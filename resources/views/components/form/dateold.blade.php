@props([
    'disabled' => false,
    'lib' => 'flatpickr',
    'name',
    'options' => '',
    'label' => null
])


<x-form.field>

    @if ($attributes->has('required'))
        <div class="flex items-center gap-1">
            <x-form.label name="{{ $name }}" label="{{ $label }}" />
            <span class="text-red-700 font-bold">*</span>
        </div>
    @else
        <x-form.label name="{{ $name }}" label="{{ $label }}"/>
    @endif

    <div class="relative">
        <span class="absolute inset-y-0 right-0 flex items-center pr-2 text-gray-500">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
        </span>
        <input
            class="border rounded-md shadow-sm
                border-gray-300 focus:border-indigo-300
                focus:outline-none focus:ring focus:ring-indigo-200 focus:ring-opacity-50
                p-2 w-full"
            name="{{ $name }}" id="{{ $name }}" {{ $disabled ? 'disabled' : '' }}
            {{ $attributes(['value' => old($name)]) }} {{ $attributes }}>

        <x-form.error name="{{ $name }}" />
    </div>

</x-form.field>

@if($lib === 'flatpickr')
    @push('scripts')
        <script>
            const options_{!! $name !!} = @json($options, JSON_HEX_TAG);
            const config_{!! $name !!} = options_{!! $name !!} ? JSON.parse(options_{!! $name !!}) : {};
            const field_{!! $name !!} = document.getElementById(@json($name));
            const fp_{!! $name !!} = flatpickr(field_{!! $name !!}, config_{!! $name !!});
        </script>
    @endpush
@else
    @push('scripts')
        <script>
            const options_{!! $name !!} = @json($options, JSON_HEX_TAG) ? JSON.parse(@json($options, JSON_HEX_TAG)) : {};
            const input_{!! $name !!} = {
                field : document.getElementById(@json($name)),
                onSelect: function(date){
                    document.getElementById(@json($name)).dispatchEvent(new Event('input', {value: date.toString()}));
            }};
            const config_{!! $name !!} = Object.assign(input_{!! $name !!}, options_{!! $name !!});
            var picker_{!! $name !!} = new Pikaday(config_{!! $name !!});
        </script>
    @endpush
@endif
