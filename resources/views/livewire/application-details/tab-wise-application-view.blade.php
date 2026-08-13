<div>
    <div class="space-y-3" x-data="{ open: null }">

        @foreach($tabs as $i => $tab)
        @php
        // Color mapping
        $colorMap = [
        'component' => [
        'bg' => 'bg-blue-500',
        'border' => 'border-blue-500',
        'bg_light' => 'bg-blue-100',
        ],
        0 => [
        'bg' => 'bg-green-500',
        'border' => 'border-green-500',
        'bg_light' => 'bg-green-100',
        ],
        1 => [
        'bg' => 'bg-purple-500',
        'border' => 'border-purple-500',
        'bg_light' => 'bg-purple-100',
        ],
        2 => [
        'bg' => 'bg-amber-500',
        'border' => 'border-amber-500',
        'bg_light' => 'bg-amber-100',
        ],
        ];

        if ($tab['type'] === 'component') {
        $colors = $colorMap['component'];
        } else {
        $colors = $colorMap[$i % 3];
        }
        @endphp

        <div class="rounded-xl overflow-hidden border border-gray-200 bg-white shadow-sm">

            <!-- HEADER -->
            <button
                class="w-full flex justify-between items-center text-left p-5 bg-white hover:bg-gray-50 font-semibold border-b border-gray-100"
                @click="
                    open === {{ $i }} ? open = null : open = {{ $i }};
                    $wire.loadTab({{ $i }});
                ">
                <div class="flex items-center space-x-4">
                    <span class="h-8 w-1.5 {{ $colors['bg'] }} rounded-full"></span>
                    <span class="text-lg font-semibold text-gray-900">
                        {{ $tab['tab_name'] }}
                    </span>
                </div>

                <svg class="w-5 h-5 transition-transform"
                    :class="open === {{ $i }} ? 'rotate-180' : ''"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <!-- BODY -->
            <div x-show="open === {{ $i }}"
                x-collapse
                x-transition.opacity
                class="bg-gray-50">

                <div class="p-6 border-l-4 {{ $colors['border'] }} bg-white">

                    {{-- COMPONENT TAB --}}
                    @if($tab['type'] === 'component')
                    @if($tab['loaded'])
                    <div class="mb-6 border border-gray-200 rounded-lg {{ $colors['bg_light'] }} p-2">
                        <livewire:enclosure-list
                            :application_id="$applicationId"
                            :scheme_id="$schemeId"
                            :is_page="1"
                            :tabCode="$tab['tab_code']"
                            wire:key="doc-{{ $applicationId }}-{{ $tab['tab_code'] }}" />
                    </div>
                    @else
                    <div class="text-gray-500">Loading...</div>
                    @endif

                    {{-- FIELD TAB --}}
                    @else
                    @if($tab['loaded'])
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @forelse($tab['data'] as $field)
                        <div class="bg-blue-50 border border-blue-100 rounded-lg p-4
                                                transition-all duration-300 hover:shadow-md">
                            <div class="text-sm font-medium text-gray-600 mb-2">
                                {{ $field['label'] }}
                            </div>
                            <div class="text-base font-semibold text-gray-900 pl-4">
                                {{ $field['value'] }}
                            </div>
                        </div>
                        @empty
                        <div class="text-gray-500 col-span-full">
                            No data available
                        </div>
                        @endforelse
                    </div>
                    @else
                    <div class="text-gray-500">Loading...</div>
                    @endif
                    @endif

                </div>
            </div>
        </div>
        @endforeach

    </div>
</div>