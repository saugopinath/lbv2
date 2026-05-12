@php
    $levelName = $field->level_name ?? '';
    $hasPlaceholder = str_contains($levelName, '[[input]]');
    $parts = $hasPlaceholder ? explode('[[input]]', $levelName) : [$levelName, ''];
@endphp

<div class="{{ $hasPlaceholder ? 'flex flex-wrap items-center gap-1 text-gray-700 dark:text-white' : '' }}">
    @if($hasPlaceholder)
        <span>{!! $parts[0] !!}</span>
        
        @switch($field->field_type)
            @case('select')
                <select class="border border-gray-300 rounded-lg p-1 inline-block mx-1" disabled>
                    <option value="">-- Select --</option>
                    @foreach($field->options ?? [] as $opt)
                        <option>{{ $opt }}</option>
                    @endforeach
                </select>
                @break
            @case('radio')
                @foreach($field->options ?? [] as $opt)
                    <label class="inline-flex items-center gap-1 mx-1 text-gray-700">
                        <input type="radio" disabled /> {{ $opt }}
                    </label>
                @endforeach
                @break
            @case('checkbox')
                <input type="checkbox" disabled class="mx-1" />
                @break
            @default
                <input type="text" disabled class="border border-gray-300 rounded-lg p-1 inline-block w-auto mx-1 focus:ring-indigo-500" placeholder="Input" />
        @endswitch

        <span>{!! $parts[1] !!}</span>
    @else
        @switch($field->field_type)
            @case('text')
            @case('email')
            <div>
                <x-form.input
                    type="{{ $field->field_type }}"
                    name="{{ $field->field_name ?? $field->level_name }}"
                    label="{!! $field->level_name !!}"
                    placeholder="Enter {{ $field->level_name }}"
                    disabled
                />
            </div>
            @break

            @case('number')
            <div>
                <x-form.input
                    type="number"
                    name="{{ $field->field_name ?? $field->level_name }}"
                    label="{!! $field->level_name !!}"
                    placeholder="Enter {{ $field->level_name }}"
                    disabled
                />
            </div>
            @break

            @case('date')
            <div>
                <x-form.input
                    type="date"
                    name="{{ $field->field_name ?? $field->level_name }}"
                    label="{!! $field->level_name !!}"
                    disabled
                />
            </div>
            @break

            @case('textarea')
            <div>
                <x-form.textarea
                    name="{{ $field->field_name ?? $field->level_name }}"
                    label="{!! $field->level_name !!}"
                    placeholder="Enter {{ $field->level_name }}"
                    disabled
                />
            </div>
            @break

            @case('select')
            <div>
                <x-form.select
                    name="{{ $field->field_name ?? $field->level_name }}"
                    label="{!! $field->level_name !!}"
                    disabled
                >
                    <option value="">-- Select {{ $field->level_name }} --</option>
                    @foreach($field->options ?? [] as $opt)
                        <option>{{ $opt }}</option>
                    @endforeach
                </x-form.select>
            </div>
            @break

            @case('radio')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    {!! $field->level_name !!}
                </label>
                <div class="flex flex-wrap gap-4">
                    @foreach($field->options ?? [] as $opt)
                        <label class="flex items-center gap-2 text-gray-700">
                            <input type="radio" disabled />
                            {{ $opt }}
                        </label>
                    @endforeach
                </div>
            </div>
            @break

            @case('checkbox')
            <div class="flex items-center gap-2 mt-2">
                <input type="checkbox" disabled />
                <label class="text-gray-700">
                    {!! $field->level_name !!}
                </label>
            </div>
            @break

            @case('label')
            @case('heading')
            <div class="text-lg font-semibold text-gray-800">
                {!! $field->level_name !!}
            </div>
            @break

            @default
            <div class="text-sm text-red-500">
                Unsupported field type: {{ $field->field_type }}
            </div>
        @endswitch
    @endif
</div>