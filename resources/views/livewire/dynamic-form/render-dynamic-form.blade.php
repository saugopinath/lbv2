<form  x-on:submit.prevent="
            Livewire.dispatch('showLoader');
            $wire.save();
        ">
    <div class="bg-white shadow rounded-xl p-2 mb-2">
        @foreach ($fields as $sectionId => $groupFields)
            @php
                $section = $sections[$sectionId] ?? null;
             @endphp
            {{-- ================= SECTION EXISTS ================= --}}
            @if ($section)
                <fieldset class="border border-gray-300 rounded-lg pl-4 pr-4 mb-2">
                    <legend class="text-lg font-semibold mb-1 text-indigo-700">
                        {{ $section['section_name'] }}
                    </legend>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-2">
                        @foreach ($groupFields as $field)
                         @if ($this->shouldShowField($field))
                          <div wire:key="field-{{ $field['id'] }}">

                                @if ($field['field_type'] === 'text' || $field['field_type'] === 'date')
                                    <x-form.input name="{{ $field['field_label'] }}" id="{{ $field['field_id'] }}"
                                        wire:model="formData.{{ $field['field_label'] }}" label="{{ $field['level_name'] }}"
                                        placeholder="Enter {{ $field['level_name'] }}" required type="{{ $field['field_type'] }}" />
                                @endif
                                {{-- NUMBER --}}
                                @if ($field['field_type'] === 'textarea')
                                    <x-form.textarea name="{{ $field['field_label'] }}" id="{{ $field['field_id'] }}"
                                        wire:model="formData.{{ $field['field_label'] }}" label="{{ $field['level_name'] }}"
                                        placeholder="Enter {{ $field['level_name'] }}" required type="{{ $field['field_type'] }}" />
                                @endif
                                {{-- NUMBER --}}
                                @if ($field['field_type'] === 'number')
                                    <x-form.input name="{{ $field['field_label'] }}" id="{{ $field['field_id'] }}"
                                        wire:model="formData.{{ $field['field_label'] }}" label="{{ $field['level_name'] }}"
                                        placeholder="Enter {{ $field['level_name'] }}" required type="{{ $field['field_type'] }}" />
                                @endif
                                @if ($field['field_type'] === 'file')
                                    <x-form.input name="{{ $field['field_label'] }}" id="{{ $field['field_id'] }}"
                                        wire:model="formData.{{ $field['field_label'] }}" label="{{ $field['level_name'] }}" type="file"
                                        required />
                                @endif
                                @if ($field['field_type'] === 'password')
                                    <x-form.input name="{{ $field['field_label'] }}" id="{{ $field['field_id'] }}"
                                        wire:model="formData.{{ $field['field_label'] }}" label="{{ $field['level_name'] }}"
                                        placeholder="Enter {{ $field['level_name'] }}" required type="{{ $field['field_type'] }}" />
                                @endif
                                {{-- SELECT (MULTIPLE) --}}
                                @if ($field['field_type'] === 'select' && $field['is_multiple'] === false)
                                    <x-form.select name="{{ $field['field_label'] }}" id="{{ $field['field_id'] }}"
                                        label="{{ $field['level_name'] }}" wire:model.live="formData.{{ $field['field_label'] }}">
                                        <option value="">-- Select {{ $field['field_label'] }} --</option>
                                        @foreach ($field['options'] as $key => $option)
                                        <option value="{{ $key }}">{{ $option }}</option>
                                    @endforeach

                                    </x-form.select>
                                @elseif ($field['field_type'] === 'select' && $field['is_multiple'] === true)
                                    <x-form.multiselect name="{{ $field['field_label'] }}[]" id="{{ $field['field_id'] }}"
                                        label="{{ $field['level_name'] }}" wire:model.live="formData.{{ $field['field_label'] }}"
                                        :options="$field['options']" multiple />
                                @endif
                                {{-- RADIO (treated as multiple) --}}
                                @if ($field['field_type'] === 'radio')
                                    <x-form.label name="{{ $field['level_name'] }}" class="mt-3" />
                                    <div class="flex flex-wrap items-center gap-4">
                                        @foreach ($field['options'] as $option)
                                            <x-form.radio name="{{ $field['field_label'] }}" value="{{ $option }}" label="{{ $option }}"
                                                wire:model="formData.{{ $field['field_label'] }}" />
                                        @endforeach
                                    </div>
                                    {{-- VALIDATION ERROR --}}
                                    <x-form.error name="formData.{{ $field['field_label'] }}" />
                                @endif
                                {{-- CHECKBOX --}}
                                @if ($field['field_type'] === 'checkbox')
                                    <x-form.label name="{{ $field['level_name'] }}" />
                                    <div class="space-y-2">
                                        @foreach ($field['options'] as $option)
                                            <x-form.checkbox name="{{ $field['field_id'] }}[]" value="{{ $option }}" label="{{ $option }}"
                                                wire:model="formData.{{ $field['field_label'] }}" />
                                        @endforeach
                                    </div>
                                    <x-form.error name="formData.{{ $field['field_label'] }}" />
                                @endif

                            </div>
                            @endif
                        @endforeach
                    </div>
                </fieldset>
            @else
                {{-- Divider --}}
                <div class="flex items-center my-8">
                    <div class="flex-grow border-t border-dashed border-gray-400"></div>
                </div>
                <div class="bg-gray-50 rounded-xl p-6 mb-8">
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        @foreach ($groupFields as $field)
                        @if ($this->shouldShowField($field))
                            <div wire:key="field-{{ $field['id'] }}">

                                @if ($field['field_type'] === 'text' || $field['field_type'] === 'date')
                                    <x-form.input name="{{ $field['field_label'] }}" id="{{ $field['field_id'] }}"
                                        wire:model="formData.{{ $field['field_label'] }}" label="{{ $field['level_name'] }}"
                                        placeholder="Enter {{ $field['level_name'] }}" type="{{ $field['field_type'] }}" />
                                @endif
                                {{-- NUMBER --}}
                                @if ($field['field_type'] === 'textarea')
                                    <x-form.textarea name="{{ $field['field_label'] }}" id="{{ $field['field_id'] }}"
                                        wire:model="formData.{{ $field['field_label'] }}" label="{{ $field['level_name'] }}"
                                        placeholder="Enter {{ $field['level_name'] }}" type="{{ $field['field_type'] }}" />
                                @endif
                                {{-- NUMBER --}}
                                @if ($field['field_type'] === 'number')
                                    <x-form.input name="{{ $field['field_label'] }}" id="{{ $field['field_id'] }}"
                                        wire:model="formData.{{ $field['field_label'] }}" label="{{ $field['level_name'] }}"
                                        placeholder="Enter {{ $field['level_name'] }}" type="{{ $field['field_type'] }}" />
                                @endif
                                @if ($field['field_type'] === 'file')
                                    <x-form.input name="{{ $field['field_label'] }}" id="{{ $field['field_id'] }}"
                                        wire:model="formData.{{ $field['field_label'] }}" label="{{ $field['level_name'] }}"
                                        type="file" />
                                @endif
                                @if ($field['field_type'] === 'password')
                                    <x-form.input name="{{ $field['field_label'] }}" id="{{ $field['field_id'] }}"
                                        wire:model="formData.{{ $field['field_label'] }}" label="{{ $field['level_name'] }}"
                                        placeholder="Enter {{ $field['level_name'] }}" type="{{ $field['field_type'] }}" />
                                @endif
                                {{-- SELECT (MULTIPLE) --}}
                                @if ($field['field_type'] === 'select' && $field['is_multiple'] === false)
                                    @if($field['field_class'])
                                       <x-form.select wire:ignore
                                                data-field="{{ $field['field_class'] }}" name="{{ $field['field_label'] }}"
                                            wire:model="formData.{{ $field['field_label'] }}"
                                                label="{{ $field['level_name'] }}"
                                            >
                                        <option value="">-- Select {{ $field['level_name'] }} --</option>
                                    </x-form.select>

                                    @else
                                        <x-form.select name="{{ $field['field_label'] }}" id="{{ $field['field_id'] }}"
                                            label="{{ $field['level_name'] }}" wire:model.live="formData.{{ $field['field_label'] }}">
                                            <option value="">-- Select {{ $field['field_label'] }} --</option>
                                            @foreach ($field['options'] as $key => $option)
                                                <option value="{{ $key }}">{{ $option }}</option>
                                            @endforeach

                                        </x-form.select>
                                    @endif

                                @elseif ($field['field_type'] === 'select' && $field['is_multiple'] === true)
                                    <x-form.multiselect name="{{ $field['field_label'] }}[]" id="{{ $field['field_id'] }}"
                                        label="{{ $field['level_name'] }}" wire:model="formData.{{ $field['field_label'] }}"
                                        :options="$field['options']" multiple />

                                @endif
                                {{-- RADIO (treated as multiple) --}}
                                @if ($field['field_type'] === 'radio')
                                    <x-form.label name="{{ $field['level_name'] }}" class="mt-3" />

                                    <div class="flex flex-wrap items-center gap-4">
                                        @foreach ($field['options'] as $option)
                                            <x-form.radio name="{{ $field['field_label'] }}" value="{{ $option }}" label="{{ $option }}"
                                                wire:model="formData.{{ $field['field_label'] }}" />
                                        @endforeach
                                    </div>
                                @endif
                                {{-- CHECKBOX --}}
                                @if ($field['field_type'] === 'checkbox')
                                    <x-form.label name="{{ $field['level_name'] }}" />

                                    <div class="space-y-2">
                                        @foreach ($field['options'] as $option)
                                            <x-form.checkbox name="{{ $field['field_label'] }}[]" value="{{ $option }}"
                                                label="{{ $option }}" wire:model="formData.{{ $field['field_label'] }}" />
                                        @endforeach

                                    </div>
                                @endif

                            </div>
                        @endif
                        @endforeach

                    </div>
                </div>
            @endif
        @endforeach
        <div class="flex justify-between mt-4 pl-6 pr-6">
            @if ($mode != '0')
                <x-button.danger wire:click="$dispatch('goPrevious')">Previous</x-button.danger>
            @endif
            <x-button.success type="submit">
                {{ $mode == '0' ? 'Save' : 'Preview and Submit' }}
            </x-button.success>
        </div>

    </div>
</form>

<script src="{{ asset('js/master-data/master-data-v2.js') }}"></script>

<script>
(function () {

    function clearSelect(select) {
        if (select) select.innerHTML = '<option value="">--Select--</option>';
    }

    function fillSelect(select, list) {
        clearSelect(select);
        list.forEach(row => {
            const opt = document.createElement('option');
            opt.value = row.id;
            opt.textContent = row.text;
            select.appendChild(opt);
        });
    }

    function syncLivewire(select) {
        const root = select.closest('[wire\\:id]');
        if (!root) return;

        const wireKey = select.dataset.wire;
        if (!wireKey) return;

        Livewire.find(root.getAttribute('wire:id'))
            ?.set('formData.' + wireKey, select.value);
    }

    function init() {
        if (!window.masterDataV2) return;

        const md = window.masterDataV2;
        {{--  console.log('Dynamic Form Master Data Loaded', md);  --}}

        document.querySelectorAll('[wire\\:id]').forEach(root => {

            const district  = root.querySelector('select[data-field="district"]');
            const assembly  = root.querySelector('select[data-field="assembly"]');
            const urban     = root.querySelector('select[data-field="rural_urban"]');
            const localbody = root.querySelector('select[data-field="block"]');
            const gpward    = root.querySelector('select[data-field="panchayat"]');

            if (!district) return;

            // ✅ INITIAL DISTRICT LOAD (MISSING PIECE)
            if (!district.dataset.loaded) {
                fillSelect(district, md.districts || []);
                district.dataset.loaded = '1';
            }


            // =========================
            // DISTRICT CHANGE
            // =========================
            district.onchange = () => {
                const districtCode = district.value;
                {{--  alert(districtCode);  --}}

                if (assembly && md.assemblies) {
                    fillSelect(
                        assembly,
                        md.assemblies.filter(a => a.district_code == districtCode)
                    );
                }

                if (urban) urban.value = '';
                clearSelect(localbody);
                clearSelect(gpward);

                syncLivewire(district);
            };

            // =========================
            // RURAL / URBAN
            // =========================
            if (urban) {

            if (!urban.dataset.loaded) {
                fillSelect(urban, md.rural_urban || []);
                urban.dataset.loaded = '1';
            }
                {{--  alert('urban found');  --}}
                urban.onchange = () => {
                    clearSelect(localbody);
                    clearSelect(gpward);

                    if (urban.value == 2 && md.blocks) {
                        fillSelect(
                            localbody,
                            md.blocks.filter(b => b.district_code == district.value)
                        );
                    }

                    if (urban.value == 1 && md.ulbs) {
                        fillSelect(
                            localbody,
                            md.ulbs.filter(u => u.district_code == district.value)
                        );
                    }

                    syncLivewire(urban);
                };
            }

            // =========================
            // LOCALBODY
            // =========================
            if (localbody) {
                localbody.onchange = () => {
                    clearSelect(gpward);

                    if (urban.value == 2 && md.gps) {
                        fillSelect(
                            gpward,
                            md.gps.filter(g =>
                                g.district_code == district.value &&
                                g.block_code == localbody.value
                            )
                        );
                    }

                    if (urban.value == 1 && md.ulb_wards) {
                        fillSelect(
                            gpward,
                            md.ulb_wards.filter(w =>
                                w.urban_body_code == localbody.value
                            )
                        );
                    }

                    syncLivewire(localbody);
                };
            }

            if (gpward) {
                gpward.onchange = () => syncLivewire(gpward);
            }
        });
    }

    // 🔥 wait for master data
    window.addEventListener('masterdata:ready', init);

    // 🔥 livewire re-render safe
    document.addEventListener('livewire:load', () => {
        Livewire.hook('message.processed', init);
    });

})();
</script>








