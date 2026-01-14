<div class="max-w-3xl mx-auto bg-white border border-gray-200 rounded-xl shadow-sm p-6 space-y-6">

    {{-- Scheme Select --}}
    <div>
        <x-form.select
            name="scheme_id"
            id="scheme_id"
            label="Scheme"
            wire:model.live="selectedSchemeId"
            class="border rounded px-3 py-2 w-full"
        >
            <option value="">-- Select Scheme --</option>
            @foreach($schemes as $scheme)
                <option value="{{ $scheme->id }}">{{ $scheme->name }}</option>
            @endforeach
        </x-form.select>
    </div>

    {{-- Tab Select --}}
    @if($selectedSchemeId)
        <div>
            <x-form.select
                name="tab_code"
                id="tab_code"
                label="Select Tab"
                wire:model.live="selectedTabCode"
                class="border rounded px-3 py-2 w-full"
                :disabled="$isFinal"
            >
                <option value="">
                    {{ $isFinal ? 'Final Submitted (Read Only)' : '-- Select Tab --' }}
                </option>

                @foreach($allTabs as $tab)
                    @if(!in_array($tab->tab_code, $selectedTabs))
                        <option value="{{ $tab->tab_code }}">
                            {{ $tab->tab_name }}
                        </option>
                    @endif
                @endforeach
            </x-form.select>
        </div>
    @endif

    {{-- Selected Tabs --}}
    @if(count($selectedTabs))
        <div
            class="border-t pt-4"
            x-data
            @if(!$isFinal)
                x-init="
                    new Sortable($refs.tabList, {
                        animation: 150,
                        handle: '.drag-handle',
                        onEnd() {
                            let ordered = Array.from($refs.tabList.children)
                                .map(el => el.dataset.code);
                            $wire.updateOrder(ordered);
                        }
                    })
                "
            @endif
        >
            <h3 class="text-base font-semibold text-gray-800 mb-3">
                Selected Tabs (Drag to reorder)
            </h3>

            <ul class="space-y-2" x-ref="tabList">
                @foreach($selectedTabs as $tabCode)
                    @php
                        $tab = $allTabs->firstWhere('tab_code', $tabCode);
                    @endphp

                    <li
                        data-code="{{ $tabCode }}"
                        class="flex items-center justify-between border rounded-lg px-4 py-3
                            {{ $isFinal ? 'bg-gray-100 opacity-70 cursor-not-allowed' : 'bg-gray-50' }}"
                    >
                        <div class="flex items-center gap-3">

                            {{-- Drag Handle --}}
                            <span class="drag-handle
                                {{ $isFinal ? 'cursor-not-allowed text-gray-300' : 'cursor-move text-gray-400' }}">
                                ☰
                            </span>

                            <span class="text-sm font-medium text-gray-800">
                                {{ $positions[$tabCode] }}.
                                {{ $tab?->tab_name }}
                            </span>
                        </div>

                        {{-- Remove --}}
                        @unless($isFinal)
                            <button
                                wire:click="removeTab({{ $tabCode }})"
                                class="text-red-600 hover:text-red-800 font-bold"
                                type="button"
                            >
                                ✕
                            </button>
                        @endunless
                    </li>
                @endforeach
            </ul>

            {{-- Action Buttons --}}
            <div class="mt-4 flex gap-2 justify-center">

                {{-- Add Field --}}
                @if ($mappingSaved)
                    <a
                        href="{{ $selectedSchemeId
                            ? route('tab-field-manager', ['scheme_id' => Crypt::encryptString($selectedSchemeId)])
                            : route('tab-field-manager') }}"
                        class="{{ $isFinal ? 'pointer-events-none opacity-50' : '' }}"
                    >
                        <x-button.primary class="bg-green-600 hover:bg-green-700">
                            Add Field
                        </x-button.primary>
                    </a>
                @endif

                {{-- Save Mapping --}}
                @unless($mappingSaved)
                    <x-button.primary
                        wire:click="submit"
                        class="bg-green-600 hover:bg-green-700"
                        :disabled="$isFinal"
                    >
                        Save Mapping
                    </x-button.primary>
                @endunless

                {{-- Preview --}}
                <x-button.primary
                    wire:click="openPreview"
                    class="bg-blue-600 hover:bg-blue-700"
                >
                    Preview
                </x-button.primary>

            </div>

            {{-- Final Notice --}}
            @if($isFinal)
                <div class="mt-3 text-center text-sm text-red-600 font-semibold">
                    This scheme is final submitted. Editing is disabled.
                </div>
            @endif
        </div>
    @endif


        @if($showFinalPreview)
            <div class="fixed inset-0 z-50 bg-black/75 flex items-center justify-center">
                <div class="bg-white rounded-xl shadow-lg w-auto max-h-[90vh] flex flex-col overflow-hidden">

                    {{-- HEADER --}}
                    <div class="flex justify-between px-6 py-4 border-b">
                        <h3 class="font-semibold">Final Preview</h3>
                        <button wire:click="closePreview">✕</button>
                    </div>

                    {{-- TAB NAV --}}
                    <div class="px-6 pt-4 border-b">
                        <nav class="flex space-x-6">
                            @foreach($tabs as $tab)
                            <button wire:click="setFinalPreviewTab({{ $tab['tab_code'] }})"
                                class="{{ $finalActiveTabCode == $tab['tab_code'] ? 'text-indigo-600 border-b-2' : '' }}">
                                {{ $tab['tab_name'] }}
                            </button>
                            @endforeach
                        </nav>
                    </div>

                    {{-- CONTENT --}}
                    <div class="p-6 overflow-y-auto">

                        {{-- TAB 105 --}}
                        @if($finalActiveTabCode == 105)
                        @if(!empty($selfDeclarationDisplay))
                        @foreach($selfDeclarationDisplay as $row)
                        <label class="flex gap-2">
                            <input type="checkbox" disabled>
                            {{ $row['field']->level_name }}
                        </label>
                        @endforeach
                        @else
                        <div class="text-gray-400">No self declaration fields</div>
                        @endif

                        @elseif($finalActiveTabCode == 104)

                            @if(!empty($docTypeIds))
                                <livewire:enclosure-list
                                    :scheme_id="$selectedSchemeId"
                                    :docTypeIds="$docTypeIds"
                                    :form_preview="1"
                                />
                            @else
                                <div class="text-gray-400">
                                    No enclosure documents found in JSON
                                </div>
                            @endif

                        {{-- OTHER TABS --}}
                        @else
                        @if(!empty($finalPreviewFields))
                        <div class="grid md:grid-cols-2 gap-4">
                            @foreach($finalPreviewFields as $field)

                            @switch($field->field_type)

                            {{-- TEXT --}}
                            @case('text')
                            <x-form.input
                                name="{{ $field->field_name ?? $field->field_id }}"
                                label="{{ $field->level_name }}"
                                disabled />
                            @break

                            {{-- NUMBER --}}
                            @case('number')
                            <x-form.input
                                type="number"
                                name="{{ $field->field_name ?? $field->field_id }}"
                                label="{{ $field->level_name }}"
                                disabled />
                            @break

                            {{-- DATE --}}
                            @case('date')
                            <x-form.input
                                type="date"
                                name="{{ $field->field_name ?? $field->field_id }}"
                                label="{{ $field->level_name }}"
                                disabled />
                            @break

                            {{-- TEXTAREA --}}
                            @case('textarea')
                            <x-form.textarea
                                name="{{ $field->field_name ?? $field->field_id }}"
                                label="{{ $field->level_name }}"
                                disabled />
                            @break

                            {{-- SELECT --}}
                            @case('select')
                            <x-form.select
                                name="{{ $field->field_name ?? $field->field_id }}"
                                label="{{ $field->level_name }}"
                                disabled>
                                <option value="">
                                    -- Select {{ $field->level_name }} --
                                </option>

                                @foreach($field->options ?? [] as $opt)
                                <option>{{ $opt }}</option>
                                @endforeach
                            </x-form.select>
                            @break

                            {{-- RADIO --}}
                            @case('radio')
                            <div class="space-y-1">
                                <label class="block text-sm font-medium text-gray-700">
                                    {{ $field->level_name }}
                                </label>

                                <div class="flex flex-wrap gap-4">
                                    @foreach($field->options ?? [] as $opt)
                                    <label class="flex items-center gap-2 text-gray-700">
                                        <input
                                            type="radio"
                                            name="{{ $field->field_name ?? $field->field_id }}"
                                            disabled>
                                        {{ $opt }}
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                            @break

                            {{-- CHECKBOX --}}
                            @case('checkbox')
                            <label class="flex items-center gap-2 text-gray-700">
                                <input
                                    type="checkbox"
                                    name="{{ $field->field_name ?? $field->field_id }}"
                                    disabled>
                                {{ $field->level_name }}
                            </label>
                            @break

                            {{-- FILE / UPLOAD --}}
                            @case('file')
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ $field->level_name }}
                                </label>
                                <input
                                    type="file"
                                    name="{{ $field->field_name ?? $field->field_id }}"
                                    disabled
                                    class="block w-full text-sm text-gray-400">
                            </div>
                            @break

                            {{-- EMAIL --}}
                            @case('email')
                            <x-form.input
                                type="email"
                                name="{{ $field->field_name ?? $field->field_id }}"
                                label="{{ $field->level_name }}"
                                disabled />
                            @break

                            {{-- FALLBACK --}}
                            @default
                            <div class="text-sm text-red-500">
                                Unsupported field type: {{ $field->field_type }}
                            </div>

                            @endswitch

                            @endforeach
                        </div>
                        @else
                        <div class="text-gray-400">No fields configured</div>
                        @endif
                        @endif

                    </div>

                    <div class="flex justify-end px-6 py-4 border-t">
                        <x-button.primary wire:click="closePreview">Close</x-button.primary>
                    </div>

                </div>
            </div>
            @endif
    {{-- Success Message --}}
    @if(session()->has('message'))
        <div class="rounded-lg bg-green-50 border border-green-200 p-3 text-green-700 text-sm font-medium">
            {{ session('message') }}
        </div>
    @endif

</div>
