<div class="max-w-3xl mx-auto bg-white border border-gray-200 rounded-xl shadow-sm p-6 space-y-6">

    {{-- Scheme Select --}}
    <div>
        <x-form.select name="scheme_id" id="scheme_id" label="Scheme" wire:model.live="selectedSchemeId"
            class="border rounded px-3 py-2 w-full">
            <option value="">-- Select Scheme --</option>
            @foreach($schemes as $scheme)
                <option value="{{ $scheme->id }}">{{ $scheme->name }}</option>
            @endforeach
        </x-form.select>
    </div>

    {{-- Tab Select --}}
    @if($selectedSchemeId)
        <div>
            <x-form.select name="tab_code" id="tab_code" label="Select Tab" wire:model.live="selectedTabCode"
                class="border rounded px-3 py-2 w-full">
                <option value="">-- Select Tab --</option>

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

    {{-- Selected Tabs (Drag & Drop) --}}
    @if(count($selectedTabs))
        <div class="border-t pt-4" x-data x-init="
                    new Sortable($refs.tabList, {
                        animation: 150,
                        handle: '.drag-handle',
                        onEnd() {
                            let ordered = Array.from($refs.tabList.children)
                                .map(el => el.dataset.code);
                            $wire.updateOrder(ordered);
                        }
                    })
                    ">
            <h3 class="text-base font-semibold text-gray-800 mb-3">
                Selected Tabs (Drag to reorder)
            </h3>

            <ul class="space-y-2" x-ref="tabList">
                @foreach($selectedTabs as $tabCode)
                    @php
                        $tab = $allTabs->firstWhere('tab_code', $tabCode);
                    @endphp

                    <li data-code="{{ $tabCode }}"
                        class="flex items-center justify-between bg-gray-50 border border-gray-200 rounded-lg px-4 py-3">
                        <div class="flex items-center gap-3">
                            {{-- Drag Handle --}}
                            <span class="drag-handle cursor-move text-gray-400">
                                ☰
                            </span>

                            <span class="text-sm font-medium text-gray-800">
                                {{ $positions[$tabCode] }}.
                                {{ $tab?->tab_name }}
                            </span>
                        </div>

                        <button wire:click="removeTab({{ $tabCode }})" class="text-red-600 hover:text-red-800 font-bold"
                            type="button">
                            ✕
                        </button>
                    </li>
                @endforeach
            </ul>

            <div class="mt-4 flex gap-2 justify-center">
                @if ($mappingSaved)
                            <a href="{{ $selectedSchemeId
                            ? route('tab-field-manager', ['scheme_id' => Crypt::encryptString($selectedSchemeId)])
                            : route('tab-field-manager')
                    }}">
                                <x-button.primary class="bg-green-600 hover:bg-green-700">
                                    Add Field
                                </x-button.primary>
                            </a>

                @endif

                {{-- Save Mapping --}}
                @unless($mappingSaved)
                    <x-button.primary wire:click="submit" class="bg-green-600 hover:bg-green-700">
                        Save Mapping
                    </x-button.primary>
                @endunless

                {{-- Preview --}}
                <x-button.primary wire:click="openPreview" class="bg-blue-600 hover:bg-blue-700">
                    Preview
                </x-button.primary>
            </div>
        </div>
    @endif

    {{-- Preview Modal --}}
    @if($showPreview)
        <div class="fixed inset-0 z-50 bg-black/75 flex items-center justify-center">
            <div class="bg-white rounded-xl shadow-lg w-auto max-w-auto max-h-[90vh] flex flex-col overflow-hidden">

            {{-- HEADER --}}
            <div class="flex items-center justify-between px-6 py-4 border-b shrink-0">
                <h3 class="text-lg font-semibold text-gray-800">
                    Preview
                </h3>
                <button wire:click="closePreview"
                    class="text-gray-400 hover:text-gray-600 text-xl">✕</button>
            </div>

            {{-- TAB NAV --}}
            <div class="px-6 pt-4 border-b shrink-0">
                <nav class="flex space-x-6">
                    @foreach($selectedTabs as $tabCode)
                        @php $tab = $allTabs->firstWhere('tab_code', $tabCode); @endphp

                        <button
                            wire:click="setPreviewTab({{ $tabCode }})"
                            class="flex items-center gap-2 pb-2 text-sm font-medium
                            {{ $previewActiveTabCode == $tabCode
                                ? 'border-indigo-600 text-indigo-600'
                                : 'border-transparent text-gray-500 hover:text-gray-700' }}">

                            <x-entrytab-nav-link
                                :active="$previewActiveTabCode == $tabCode"
                                :icon="$tab?->tab_icon">
                                {{ $tab?->tab_name }}
                            </x-entrytab-nav-link>
                        </button>
                    @endforeach
                </nav>
            </div>

            {{-- CONTENT --}}
            <div class="p-6 max-h-[70vh] overflow-y-auto">

                {{-- ========== TAB 104 : ENCLOSURE ========== --}}
                @if($previewActiveTabCode == 104)
                    <livewire:enclosure-list
                        :scheme_id="$selectedSchemeId"
                        :form_preview="1"
                        :tabCode="$previewActiveTabCode" />

                {{-- ========== TAB 105 : SELF DECLARATION ========== --}}
                @elseif($previewActiveTabCode == 105)

                    @if(empty($selfDeclarationDisplay))
                        <div class="text-center text-gray-400">
                            No self declaration fields configured
                        </div>
                    @else
                        <div class="space-y-2">
                            @foreach($selfDeclarationDisplay as $row)

                                {{-- SECTION START --}}
                                @if($row['show_section_start'])
                                    <div class="mt-4 mb-2 px-3 py-2 bg-indigo-50 border-l-4 border-indigo-600 rounded">
                                        <span class="font-semibold text-indigo-700">
                                            {{ $row['section_title'] }}
                                        </span>
                                    </div>
                                @endif

                                {{-- FIELD --}}
                                <div class="py-2 bg-white {{ $row['field']->section_level_id ? 'pl-6 bg-gray-50' : '' }}">
                                    @php $name = 'preview_'.$row['field']->id; @endphp

                                    @switch($row['field']->field_type)
                                        @case('text')
                                            <x-form.input name="{{ $name }}" label="{{ $row['field']->level_name }}" disabled />
                                            @break

                                        @case('number')
                                            <x-form.input type="number" name="{{ $name }}" label="{{ $row['field']->level_name }}" disabled />
                                            @break

                                        @case('date')
                                            <x-form.input type="date" name="{{ $name }}" label="{{ $row['field']->level_name }}" disabled />
                                            @break

                                        @case('textarea')
                                            <x-form.textarea name="{{ $name }}" label="{{ $row['field']->level_name }}" disabled />
                                            @break

                                        @case('select')
                                            <x-form.select name="{{ $name }}" label="{{ $row['field']->level_name }}" disabled>
                                                <option value="">-- Select --</option>
                                                @foreach($row['field']->options ?? [] as $opt)
                                                    <option>{{ $opt }}</option>
                                                @endforeach
                                            </x-form.select>
                                            @break
                                    @endswitch
                                </div>

                                {{-- SECTION END --}}
                                @if($row['show_section_end'])
                                    <div class="my-3"></div>
                                @endif

                            @endforeach
                        </div>
                    @endif

                {{-- ========== OTHER TABS ========== --}}
                @else

                    @if($previewFormFields->isEmpty())
                        <div class="text-center text-gray-400">
                            No fields configured for this tab
                        </div>
                    @else
                        <div class="grid md:grid-cols-2 gap-4">
                            @foreach($previewFormFields as $field)
                                @php $name = 'preview_'.$field->id; @endphp

                                @if($field->field_type === 'text')
                                    <x-form.input name="{{ $name }}" label="{{ $field->level_name }}" disabled />

                                @elseif($field->field_type === 'number')
                                    <x-form.input type="number" name="{{ $name }}" label="{{ $field->level_name }}" disabled />

                                @elseif($field->field_type === 'date')
                                    <x-form.input type="date" name="{{ $name }}" label="{{ $field->level_name }}" disabled />

                                @elseif($field->field_type === 'textarea')
                                    <x-form.textarea name="{{ $name }}" label="{{ $field->level_name }}" disabled />

                                @elseif($field->field_type === 'select')
                                    <x-form.select name="{{ $name }}" label="{{ $field->level_name }}" disabled>
                                        <option value="">-- Select --</option>
                                        @foreach($field->options ?? [] as $opt)
                                            <option>{{ $opt }}</option>
                                        @endforeach
                                    </x-form.select>
                                @endif
                            @endforeach
                        </div>
                    @endif

                @endif
            </div>

            {{-- FOOTER --}}
            <div class="flex justify-end gap-3 px-6 py-4 border-t">
                <x-button.primary wire:click="closePreview">
                    Close
                </x-button.primary>
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
