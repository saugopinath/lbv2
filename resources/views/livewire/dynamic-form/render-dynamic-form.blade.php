<div class="p-2">
    <form wire:submit.prevent="save">
        @foreach ($fields as $sectionId => $groupFields)
            @php $section = $sections[$sectionId] ?? null; @endphp

            {{-- Divider logic --}}
            <div class="flex items-center my-8">
                <div class="flex-grow border-t border-dashed border-gray-400"></div>
            </div>

            @if($section)
                <h2 class="text-lg font-bold text-indigo-700 mb-2 px-2">{{ $section['section_name'] }}</h2>
            @endif

            <div class="bg-gray-50 rounded-xl p-6 mb-8 shadow-sm border border-gray-100">
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach ($groupFields as $field)
                        @if ($this->shouldShowField($field))
                            <div wire:key="field-{{ $field['id'] }}">
                                
                                {{-- TEXT / DATE / NUMBER / PASSWORD --}}
                                @if (in_array($field['field_type'], ['text', 'date', 'number', 'password']))
                                    <x-form.input 
                                        name="{{ $field['field_label'] }}" 
                                        wire:model="formData.{{ $field['field_label'] }}"
                                        label="{{ $field['level_name'] }}" 
                                        placeholder="Enter {{ $field['level_name'] }}"
                                        type="{{ $field['field_type'] }}" />

                                {{-- TEXTAREA --}}
                                @elseif ($field['field_type'] === 'textarea')
                                    <x-form.textarea 
                                        name="{{ $field['field_label'] }}" 
                                        wire:model="formData.{{ $field['field_label'] }}"
                                        label="{{ $field['level_name'] }}" 
                                        placeholder="Enter {{ $field['level_name'] }}" />

                                {{-- FILE --}}
                                @elseif ($field['field_type'] === 'file')
                                    <x-form.input
                                        name="{{ $field['field_label'] }}"
                                        wire:model="formData.{{ $field['field_label'] }}"
                                        label="{{ $field['level_name'] }}"
                                        type="file" />

                                {{-- SELECT (SINGLE) --}}
                                @elseif ($field['field_type'] === 'select' && ($field['is_multiple'] ?? false) === false)
                                    <x-form.select 
                                        name="{{ $field['field_label'] }}" 
                                        label="{{ $field['level_name'] }}" 
                                        wire:model.live="formData.{{ $field['field_label'] }}">
                                        <option value="">-- Select {{ $field['level_name'] }} --</option>
                                        @foreach ($this->getFieldOptions($field) as $key => $option)
                                            <option value="{{ $key }}">{{ $option }}</option>
                                        @endforeach
                                    </x-form.select>

                                {{-- MULTISELECT --}}
                                @elseif ($field['field_type'] === 'select' && ($field['is_multiple'] ?? false) === true)
                                    <x-form.multiselect
                                        name="{{ $field['field_label'] }}[]"
                                        label="{{ $field['level_name'] }}"
                                        wire:model="formData.{{ $field['field_label'] }}"
                                        :options="$this->getFieldOptions($field)"
                                        multiple />

                                {{-- RADIO --}}
                                @elseif ($field['field_type'] === 'radio')
                                    <x-form.label name="{{ $field['level_name'] }}" class="mt-3" />
                                    <div class="flex flex-wrap items-center gap-4">
                                        @foreach ($this->getFieldOptions($field) as $key => $option)
                                            <x-form.radio
                                                name="{{ $field['field_label'] }}"
                                                value="{{ $key }}"
                                                label="{{ $option }}"
                                                wire:model.live="formData.{{ $field['field_label'] }}" />
                                        @endforeach
                                    </div>

                                {{-- CHECKBOX --}}
                                @elseif ($field['field_type'] === 'checkbox')
                                    <x-form.label name="{{ $field['level_name'] }}" />
                                    <div class="space-y-2">
                                        @foreach ($this->getFieldOptions($field) as $key => $option)
                                            <x-form.checkbox
                                                name="{{ $field['field_label'] }}[]"
                                                value="{{ $key }}"
                                                label="{{ $option }}"
                                                wire:model="formData.{{ $field['field_label'] }}" />
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endforeach

        <div class="flex justify-end mt-4 px-6">
            <button type="submit" class="bg-indigo-600 text-white px-8 py-2 rounded shadow hover:bg-indigo-700 transition">
                Save & Submit
            </button>
        </div>
    </form>
</div>