@switch($field->field_type)

{{-- TEXT --}}
@case('text')
<div>
    <x-form.input
        name="{{ $field->field_name ?? $field->level_name }}"
        label="{!! $field->level_name !!}"
        placeholder="Enter {{ $field->level_name }}"
        disabled
    />
</div>
@break

{{-- NUMBER --}}
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

{{-- DATE --}}
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

{{-- TEXTAREA --}}
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

{{-- SELECT --}}
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

{{-- RADIO --}}
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

{{-- CHECKBOX --}}
@case('checkbox')
<div class="flex items-center gap-2 mt-2">
    <input type="checkbox" disabled />
    <label class="text-gray-700">
        {!! $field->level_name !!}
    </label>
</div>
@break

{{-- FALLBACK --}}
@default
<div class="text-sm text-red-500">
    Unsupported field type: {{ $field->field_type }}
</div>

@endswitch