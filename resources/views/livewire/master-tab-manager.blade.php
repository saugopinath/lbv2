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
    @if($selectedSchemeId && !$isFinal)
        <div>
            <x-form.select name="tab_code" id="tab_code" label="Select Tab" wire:model.live="selectedTabCode"
                class="border rounded px-3 py-2 w-full">
                <option value="">
                    -- Select Tab --
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
        <div class="border-t pt-4" x-data @if(!$isFinal) x-init="
            new Sortable($refs.tabList, {
                animation: 150,
                handle: '.drag-handle',
                onEnd() {
                    let ordered = Array.from($refs.tabList.children)
                        .map(el => el.dataset.code);
                    $wire.updateOrder(ordered);
                }
            })
        " @endif>
            <h3 class="text-base font-semibold text-gray-800 mb-3">
                Selected Tabs (Drag to reorder)
            </h3>

            <ul class="space-y-2" x-ref="tabList">
                @foreach($selectedTabs as $tabCode)
                    @php
                        $tab = $allTabs->firstWhere('tab_code', $tabCode);
                    @endphp

                    <li data-code="{{ $tabCode }}" class="flex items-center justify-between border rounded-lg px-4 py-3
                                            {{ $isFinal ? 'bg-gray-100 opacity-70 cursor-not-allowed' : 'bg-gray-50' }}">
                        <div class="flex items-center gap-3">

                            {{-- Drag Handle --}}
                            <span
                                class="drag-handle
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
                            <button wire:click="removeTab({{ $tabCode }})" class="text-red-600 hover:text-red-800 font-bold"
                                type="button">
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
                    <a href="{{ $selectedSchemeId
                    ? route('tab-field-manager', ['scheme_id' => Crypt::encryptString($selectedSchemeId)])
                    : route('tab-field-manager') }}" class="{{ $isFinal ? 'pointer-events-none opacity-50' : '' }}">
                        <x-button.primary class="bg-green-600 hover:bg-green-700">
                            Add Field
                        </x-button.primary>
                    </a>
                @endif

                {{-- Save Mapping --}}
                @unless($mappingSaved)
                    <x-button.primary wire:click="submit" class="bg-green-600 hover:bg-green-700" :disabled="$isFinal">
                        Save Mapping
                    </x-button.primary>
                @endunless

                {{-- Preview --}}
                <x-button.primary wire:click="openPreview" class="bg-blue-600 hover:bg-blue-700">
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
    <div class="fixed inset-0 z-50 bg-black/75 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-4xl max-h-[90vh] flex flex-col overflow-hidden">
            
            {{-- HEADER --}}
            <div class="flex items-center justify-between px-6 py-5 border-b">
                <h3 class="text-xl font-semibold text-gray-800">configured Form Preview</h3>
                <button 
                    wire:click="closePreview"
                    class="text-gray-400 hover:text-gray-600 transition-colors text-xl"
                >
                    ×
                </button>
            </div>

            {{-- CONTENT --}}
            <div class="flex-1 overflow-y-auto p-6">
                <div class="space-y-6">
                    <livewire:dynamic-form 
                        :scheme-id="$selectedSchemeId" 
                        :ram="1" 
                        :wire:key="'dynamic-form-'.$selectedSchemeId" 
                    />
                </div>
            </div>

            {{-- FOOTER --}}
            <div class="px-6 py-4 border-t bg-gray-50">
                <div class="flex justify-end">
                    <x-button.primary 
                        wire:click="closePreview"
                        class="px-5 py-2.5"
                    >
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