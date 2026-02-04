<div class="max-w-3xl mx-auto bg-white border border-gray-200 rounded-xl shadow-sm p-6 space-y-6">

    {{-- Scheme Select --}}
    <div>
        <x-form.select name="scheme_id" id="scheme_id" label="Scheme" wire:model.live="selectedSchemeId"
            class="border rounded px-3 py-2 w-full" required>
            <option value="">-- Select Scheme --</option>
            @foreach($schemes as $scheme)
            <option value="{{ $scheme->id }}">{{ $scheme->name }}</option>
            @endforeach
        </x-form.select>
    </div>

    {{-- Tab Select --}}
    @if($selectedSchemeId && !$isFinalSubmitted)
    <div>
        <x-form.select name="tab_code" id="tab_code" label="Select Tab" wire:model.live="selectedTabCode"
            class="border rounded px-3 py-2 w-full" required>
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
                         @if(!$isFinalSubmitted)
                            new Sortable($refs.tabList, {
                                animation: 150,
                                handle: '.drag-handle',
                                onEnd() {
                                    let ordered = Array.from($refs.tabList.children)
                                        .map(el => el.dataset.code);
                                    $wire.updateOrder(ordered);
                                }
                            })
                        @endif
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
                    <span
                        class="drag-handle text-gray-400 text-lg
                                 {{ $isFinalSubmitted ? 'opacity-40 cursor-not-allowed' : 'cursor-move' }}">
                        ☰
                    </span>

                    <span class="text-sm font-medium text-gray-800">
                        {{ $positions[$tabCode] }}.
                        {{ $tab?->tab_name }}
                    </span>
                </div>

                <button wire:click="removeTab({{ $tabCode }})" @if($isFinalSubmitted) disabled @endif
                    class="text-red-500 font-bold text-lg mr-2
                                {{ $isFinalSubmitted ? 'opacity-50 cursor-not-allowed' : 'hover:text-red-600' }}">
                    ✕
                </button>
            </li>
            @endforeach
        </ul>

        <div class="mt-4 flex gap-2 justify-center">

            @if($isFinalSubmitted)
            <div class="inline-flex items-center gap-2 px-4 py-2
                bg-green-50 border border-green-300 rounded-lg
                text-green-700 font-semibold shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 117.72 117.72"
                    class="w-6 h-6 text-green-600"
                    fill="currentColor">
                    <path d="M58.86,0c9.13,0,17.77,2.08,25.49,5.79c-3.16,2.5-6.09,4.9-8.82,7.21
                c-5.2-1.89-10.81-2.92-16.66-2.92c-13.47,0-25.67,5.46-34.49,14.29
                c-8.83,8.83-14.29,21.02-14.29,34.49c0,13.47,5.46,25.66,14.29,34.49
                c8.83,8.83,21.02,14.29,34.49,14.29s25.67-5.46,34.49-14.29
                c8.83-8.83,14.29-21.02,14.29-34.49c0-3.2-0.31-6.34-0.9-9.37
                c2.53-3.3,5.12-6.59,7.77-9.85c2.08,6.02,3.21,12.49,3.21,19.22
                c0,16.25-6.59,30.97-17.24,41.62c-10.65,10.65-25.37,17.24-41.62,17.24
                c-16.25,0-30.97-6.59-41.62-17.24C6.59,89.83,0,75.11,0,58.86
                c0-16.25,6.59-30.97,17.24-41.62S42.61,0,58.86,0z
                M31.44,49.19L45.8,49l1.07,0.28c2.9,1.67,5.63,3.58,8.18,5.74
                c1.84,1.56,3.6,3.26,5.27,5.1c5.15-8.29,10.64-15.9,16.44-22.9
                c6.35-7.67,13.09-14.63,20.17-20.98l1.4-0.54H114l-3.16,3.51
                C101.13,30,92.32,41.15,84.36,52.65C76.4,64.16,69.28,76.04,62.95,88.27
                l-1.97,3.8l-1.81-3.87c-3.34-7.17-7.34-13.75-12.11-19.63 c-4.77-5.88-10.32-11.1-16.79-15.54L31.44,49.19z" />
                </svg>
                Form Configured Successfully
            </div>
            @else
            @if ($mappingSaved)
            {{-- ADD FIELD --}}
            <a href="{{ route('tab-field-manager', [
                'scheme_id' => Crypt::encryptString($selectedSchemeId)
            ]) }}">
                <x-button.primary class="bg-green-600 hover:bg-green-700">
                    Add Field
                </x-button.primary>
            </a>
            @else
            {{-- SAVE MAPPING --}}
            <x-button.primary
                wire:click="submit"
                class="bg-green-600 hover:bg-green-700">
                Save Mapping
            </x-button.primary>
            @endif
            @endif
            <x-button.primary wire:click="openPreview" class="bg-blue-600 hover:bg-blue-700">
                Preview
            </x-button.primary>
        </div>
    </div>
    @endif
    @if($showFinalPreview)
    <div class="fixed inset-0 z-50 bg-black/75 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-4xl max-h-[90vh] flex flex-col overflow-hidden">

            {{-- HEADER --}}
            <div class="flex items-center justify-between px-6 py-5 border-b">
                <h3 class="text-xl font-semibold text-gray-800">configured Form Preview</h3>
                <button
                    wire:click="closePreview"
                    class="text-gray-400 hover:text-gray-600 transition-colors text-xl">
                    ×
                </button>
            </div>

            {{-- CONTENT --}}
            <div class="flex-1 overflow-y-auto p-6">
                <div class="space-y-6">
                    <livewire:final-preview
                        :scheme-id="$selectedSchemeId"
                        :ram="1"
                        :form_preview="1"
                        :wire:key="'final-preview-'.$selectedSchemeId" />
                </div>
            </div>

            {{-- FOOTER --}}
            <div class="px-6 py-4 border-t bg-gray-50">
                <div class="flex justify-end">
                    <x-button.primary
                        wire:click="closePreview"
                        class="px-5 py-2.5">
                        Close
                    </x-button.primary>
                </div>
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
