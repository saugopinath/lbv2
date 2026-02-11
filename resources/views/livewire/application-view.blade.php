<div>
    <div class="space-y-3" x-data="{ open: 0 }">
        @foreach($tabs as $i => $tab)
        @php
        // Color mapping array with all static classes
        $colorMap = [
        'component' => [
        'bg' => 'bg-blue-500',
        'border' => 'border-blue-500',
        'text' => 'text-blue-500',
        'bg_light' => 'bg-blue-100',
        'text_dark' => 'text-blue-700',
        'border_light' => 'border-blue-200',
        ],
        0 => [
        'bg' => 'bg-green-500',
        'border' => 'border-green-500',
        'text' => 'text-green-500',
        'bg_light' => 'bg-green-100',
        'text_dark' => 'text-green-700',
        'border_light' => 'border-green-200',
        ],
        1 => [
        'bg' => 'bg-purple-500',
        'border' => 'border-purple-500',
        'text' => 'text-purple-500',
        'bg_light' => 'bg-purple-100',
        'text_dark' => 'text-purple-700',
        'border_light' => 'border-purple-200',
        ],
        2 => [
        'bg' => 'bg-amber-500',
        'border' => 'border-amber-500',
        'text' => 'text-amber-500',
        'bg_light' => 'bg-amber-100',
        'text_dark' => 'text-amber-700',
        'border_light' => 'border-amber-200',
        ],
        ];

        // Determine which color set to use
        if($tab['type'] === 'component') {
        $colors = $colorMap['component'];
        } else {
        $index = $i % 3;
        $colors = $colorMap[$index];
        }
        @endphp

        <div class="rounded-xl overflow-hidden border border-gray-200 bg-white shadow-sm">
            <!-- Header Button -->
            <button
                class="w-full flex justify-between items-center text-left p-5 bg-white hover:bg-gray-50 font-semibold transition-colors duration-150 border-b border-gray-100"
                @click="open === {{ $i }} ? open = null : open = {{ $i }}"
                :aria-expanded="open === {{ $i }} ? 'true' : 'false'">

                <!-- Left side with colored indicator and title -->
                <div class="flex items-center space-x-4">
                    <span class="h-8 w-1.5 {{ $colors['bg'] }} rounded-full"></span>
                    <div>
                        <span class="text-lg font-semibold text-gray-900">{{ $tab['tab_name'] }}</span>
                    </div>
                </div>

                <!-- Right side with animated toggle icons -->
                <div class="flex items-center space-x-3">
                    <div class="relative w-6 h-6">
                        <svg x-show="open !== {{ $i }}" class="w-6 h-6 text-indigo-500 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M10.271 5.575C8.967 4.501 7 5.43 7 7.12v9.762c0 1.69 1.967 2.618 3.271 1.544l5.927-4.881a2 2 0 0 0 0-3.088l-5.927-4.88Z" clip-rule="evenodd" />
                        </svg>


                        <svg x-show="open === {{ $i }}" class="w-6 h-6 text-indigo-500 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M18.425 10.271C19.499 8.967 18.57 7 16.88 7H7.12c-1.69 0-2.618 1.967-1.544 3.271l4.881 5.927a2 2 0 0 0 3.088 0l4.88-5.927Z" clip-rule="evenodd" />
                        </svg>

                        </svg>
                    </div>
                </div>
            </button>

            <!-- Content Area -->
            <div x-show="open === {{ $i }}"
                x-transition.opacity
                x-collapse
                class="transition-all duration-300 bg-gray-50">
                <div class="p-6 border-l-4 {{ $colors['border'] }} bg-white">
                    @if($tab['type'] === 'component')
                    <div class="mb-6">

                        <div class="border border-gray-200 rounded-lg {{ $colors['bg_light'] }} p-2">
                            <livewire:enclosure-list
                                :application_id="$applicationId"
                                :scheme_id="$schemeId"
                                :is_page="1"
                                :tabCode="$tab['tab_code']"
                                wire:key="doc-{{ $applicationId }}-{{ $tab['tab_code'] }}" />
                        </div>
                    </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($tab['data'] ?? [] as $field)
                        <div class="bg-blue-50 border border-blue-100 rounded-lg p-4 transition-all duration-300 
            hover:shadow-md hover:-translate-y-0.5 hover:border-blue-200">
                            <div class="text-sm font-medium text-gray-600 mb-2 tracking-wide">
                                {{ $field['label'] }} :
                            </div>
                            <div class="text-base font-semibold text-gray-900 pl-6">
                                {{ $field['value'] }}
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
        @endforeach
    </div>
    
</div>