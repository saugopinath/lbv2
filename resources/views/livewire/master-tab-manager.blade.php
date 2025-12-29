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
                @if($mappingSaved)
                    <x-button.primary type="button"
                        onclick="window.location='{{ route('tab-field-manager', ['scheme_id' => $selectedSchemeId]) }}'"
                        class="bg-green-600 hover:bg-green-700">
                        Add Field
                    </x-button.primary>
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
    {{-- PREVIEW MODAL --}}
    @if($showPreview)
        <div class="fixed inset-0 z-50 bg-black/75 flex items-center justify-center">
            <div class="bg-white rounded-xl shadow-lg w-auto max-w-auto">

                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b">
                    <h3 class="text-lg font-semibold text-gray-800">
                        Tab Preview
                    </h3>
                    <button wire:click="closePreview" class="text-gray-400 hover:text-gray-600 text-xl">
                        ✕
                    </button>
                </div>

                {{-- TAB NAV (USING YOUR COMPONENT) --}}
                <div class="px-6 pt-4">
                    <nav class="flex space-x-6 border-b">
                        @foreach($selectedTabs as $index => $tabCode)
                            @php
                                $tab = $allTabs->firstWhere('tab_code', $tabCode);
                            @endphp

                            <x-entrytab-nav-link :active="$index === 0" :icon="$tab?->tab_icon">
                                {{ $tab?->tab_name }}
                            </x-entrytab-nav-link>
                        @endforeach
                    </nav>
                </div>

                {{-- Dummy Content Area (Preview Only) --}}
                <div class="px-6 py-10 text-center text-gray-400">
                    Selected tab content preview will appear here
                </div>

                {{-- Footer --}}
                <div class="flex justify-end gap-3 px-6 py-4 border-t">
                    <x-button.primary wire:click="closePreview" type="button">
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