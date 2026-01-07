<div class="p-6 bg-gray-50">
    <form wire:submit.prevent="save">
        @foreach ($fields as $sectionId => $groupFields)
            @php $section = $sections[$sectionId] ?? null; @endphp
            
            <fieldset class="mb-6 border p-4 rounded-lg bg-white shadow-sm">
                @if($section)
                    <legend class="text-indigo-700 font-bold px-2 text-lg">
                        {{ $section['section_name'] }}
                    </legend>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach ($groupFields as $field)
                        {{-- ডাইনামিক ভিজিবিলিটি লুপ --}}
                        @if ($this->shouldShowField($field))
                            <div wire:key="field-{{ $field['id'] }}" class="flex flex-col">
                                <label class="text-sm font-semibold text-gray-700 mb-1">
                                    {{ $field['level_name'] }}
                                </label>

                                {{-- ড্রপডাউন রেন্ডারিং --}}
                                @if ($field['field_type'] === 'select')
                                    <select wire:model.live="formData.{{ $field['field_label'] }}" 
                                            class="border rounded p-2 focus:ring-2 focus:ring-indigo-500">
                                        <option value="">-- Select --</option>
                                        @foreach ($this->getFieldOptions($field) as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>

                                {{-- টেক্সট রেন্ডারিং (যেমন Caste Certificate No) --}}
                                @elseif ($field['field_type'] === 'text')
                                    <input type="text" 
                                           wire:model="formData.{{ $field['field_label'] }}" 
                                           class="border rounded p-2"
                                           placeholder="Enter {{ $field['level_name'] }}">
                                @endif

                                @error('formData.'.$field['field_label']) 
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span> 
                                @enderror
                            </div>
                        @endif
                    @endforeach
                </div>
            </fieldset>
        @endforeach

        <div class="mt-4">
            <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded shadow hover:bg-indigo-700 transition">
                Submit Application
            </button>
        </div>
    </form>
</div>