<div class="space-y-3 mt-4">
@foreach($selfDeclarationDisplay as $row)

    @if($row['show_section_start'])
        <div class="px-3 py-2 bg-indigo-50 border-l-4 border-indigo-600 rounded">
            <strong>{{ $row['section_title'] }}</strong>
        </div>
    @endif

    <x-form.checkbox
        name="{{ $row['field']->field_name }}"
        label="{{ $row['field']->level_name }}"
        value="1"
        wire:model="formData.{{ $row['field']->field_name }}"
    />

@endforeach
</div>