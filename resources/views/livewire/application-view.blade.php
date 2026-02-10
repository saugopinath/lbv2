<div> {{-- ✅ ONE ROOT ONLY --}}

    <div class="max-w-6xl mx-auto p-6 space-y-4"
        x-data="{ open: null }">

        @foreach($tabs as $i => $tab)

        <div class="border rounded bg-white shadow">

            <!-- TAB HEADER -->
            <button
                class="w-full px-6 py-4 flex justify-between font-semibold bg-gray-100"
                @click="open === {{ $i }} ? open = null : open = {{ $i }}">

                {{ $tab['tab_name'] }}

                <span x-text="open === {{ $i }} ? '-' : '+'"></span>
            </button>

            <!-- TAB BODY -->
            <div x-show="open === {{ $i }}" x-collapse class="p-6">

                {{-- ✅ COMPONENT TAB --}}
                @if($tab['type'] === 'component')

                <livewire:enclosure-list
                    :application_id="$applicationId"
                    :scheme_id="$schemeId"
                    :is_page="1"
                    wire:key="doc-{{ $applicationId }}" />

                {{-- ✅ FIELD TAB --}}
                @else

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    @foreach($tab['data'] as $field)

                    <div class="bg-gray-50 p-3 rounded">

                        <div class="text-xs text-gray-500">
                            {{ $field['label'] }}
                        </div>

                        <div class="font-semibold">
                            {{ $field['value'] }}
                        </div>

                    </div>

                    @endforeach

                </div>

                @endif

            </div>
        </div>

        @endforeach

    </div>

</div>