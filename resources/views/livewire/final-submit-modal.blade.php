<div> {{-- ROOT --}}

    {{-- Overlay --}}
    <div
        x-data="{ open: @entangle('show') }"
        x-show="open"
        x-transition
        x-cloak
        class="fixed inset-0 flex items-center justify-center bg-black/50 z-50">

        {{-- Modal --}}
        <div
            @click.away="$wire.close()"
            class="bg-white rounded-2xl shadow-xl max-w-5xl w-full
                   max-h-[80vh] overflow-y-auto p-6">

            {{-- Header --}}
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold">
                    Final Review Before Submit
                </h2>

                <button
                    wire:click="close"
                    class="text-gray-400 hover:text-gray-600 text-xl">
                    &times;
                </button>
            </div>

            {{-- Application ID --}}
            @if($applicationId)
                <p class="text-sm text-gray-500 mb-4">
                    Application ID:
                    <strong>{{ $applicationId }}</strong>
                </p>
            @endif

            {{-- ================= NORMAL TABS ================= --}}
            @foreach($tabsData as $tab)

                @if($tab['tab_code'] !== '104')

                    <div class="border rounded-xl p-4 mb-4">

                        <h3 class="font-semibold text-indigo-600 mb-2">
                            {{ $tab['tab_name'] }}
                        </h3>

                        @foreach($tab['fields'] as $label => $value)
                            <div class="flex justify-between text-sm border-b pb-1">
                                <span class="text-gray-600">{{ $label }}</span>
                                <span class="font-medium text-gray-800">
                                    {{ $value !== null && $value !== '' ? $value : '-' }}
                                </span>
                            </div>
                        @endforeach

                    </div>

                @endif

            @endforeach


            {{-- ================= DOCUMENT TAB (104) ================= --}}
            @php
                $documentTab = collect($tabsData)->firstWhere('tab_code', '104');
            @endphp

            @if($documentTab)

                <div class="border rounded-xl p-4 mb-4">

                    <h3 class="font-semibold text-indigo-600 mb-3">
                        {{ $documentTab['tab_name'] ?? 'Enclosure Details' }}
                    </h3>

                    <div class="border rounded-lg p-3 bg-gray-50">
                        <livewire:enclosure-list
                            :application_id="$applicationId"
                            :scheme_id="$schemeId"
                            :tabCode="104"
                            :is_page="1"
                            wire:key="enclosure-preview-{{ $applicationId }}-104"
                        />
                    </div>

                </div>

            @endif

            {{-- ================= ACTIONS ================= --}}
            <div class="flex justify-end gap-3 mt-6">

                <button
                    wire:click="close"
                    class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">
                    Cancel
                </button>

                <button
                    wire:click="confirmSubmit"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    Final Submit
                </button>

            </div>

        </div>
    </div>

</div>
