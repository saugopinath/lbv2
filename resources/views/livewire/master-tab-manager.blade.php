<div class="max-w-3xl mx-auto bg-white border border-gray-200 rounded-xl shadow-sm p-6 space-y-6">
    <div>
        <x-form.select name="scheme_id" id="scheme_id" label="Scheme" wire:model.live="selectedSchemeId"
            class="border rounded px-3 py-2 w-full">
            <option value="">-- Select Scheme --</option>
            @foreach($schemes as $scheme)
            <option value="{{ $scheme->id }}">{{ $scheme->name }}</option>
            @endforeach
        </x-form.select>
    </div>

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
    <div class="border-t pt-4"
        x-data
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
             ">
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
                class="flex items-center justify-between bg-gray-50 border border-gray-200 rounded-lg px-4 py-3">
                <div class="flex items-center gap-3">
                    <span class="drag-handle cursor-move text-gray-400">
                        ☰
                    </span>
                    <span class="text-sm font-medium text-gray-800">
                        {{ $positions[$tabCode] }}.
                        {{ $tab?->tab_name }}
                    </span>
                </div>
                <button
                    wire:click="removeTab({{ $tabCode }})"
                    class="text-red-600 hover:text-red-800 font-bold"
                    type="button">
                    ✕
                </button>
            </li>
            @endforeach
        </ul>

        <div class="mt-4 flex  gap-2 justify-end">
            <x-button.primary
                wire:click="submit"
                type="button"
                class="py-2 px-4 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg
                           border border-transparent bg-indigo-600 text-white hover:bg-indigo-700
                           focus:outline-none focus:ring-2 focus:ring-indigo-500">
                Save Mapping
            </x-button.primary>
            <x-button.primary
                wire:click="submit"
                type="button"
                class="py-2 px-4 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg
                           border border-transparent bg-green-600 text-white hover:bg-green-700
                           focus:outline-none focus:ring-2 focus:ring-green-500">
                Preview
            </x-button.primary>
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

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>