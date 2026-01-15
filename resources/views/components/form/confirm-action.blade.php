@props([
'itemId' => null,
'action' => null,
'title' => 'Confirm Action',
'titleIcon' => null,
'message' => 'Are you sure you want to continue?',
'tooltip' => null,
'icon' => null,
'confirmLabel' => 'Confirm',
'cancelLabel' => 'Cancel',
])

@php
$defaultTitleIcon = <<<SVG
    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-600"
    viewBox="0 0 24 24" fill="currentColor">
    <path d="M12 2a10 10 0 100 20 10 10 0 000-20zm1 5v6h-2V7h2zm0 8v2h-2v-2h2z" />
    </svg>
    SVG;
    @endphp

    @php
    $successIcon = <<<SVG
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
        SVG;
        @endphp


        <div
            x-data="{
        showModal: false,
        loading: false,
        submit() {
            if (this.loading) return;
            this.loading = true;
            $wire.call(
                '{{ $action }}'
                @if($itemId !== null)
                    , {{ $itemId }}
                @endif
            ).finally(() => {
                this.loading = false;
                this.showModal = false;
            });
        }
    }"
            class="relative inline-block">

            {{-- ACTION BUTTON --}}
            <button
                @click="showModal = true"
                class="flex items-center gap-2 px-4 py-2 rounded-xl
           bg-green-600 hover:bg-green-700 text-white
           transition">
                @if(trim($slot))
                {{ $slot }}
                @else
                {!! $icon ?? $defaultIcon !!}
                @endif
            </button>


            {{-- TOOLTIP --}}
            @if($tooltip)
            <div
                x-ref="tip"
                style="display:none"
                class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1
                   bg-gray-800 text-white text-xs px-2 py-1 rounded whitespace-nowrap z-10">
                {{ $tooltip }}
            </div>
            @endif

            {{-- MODAL --}}
            <div
                x-show="showModal"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                x-cloak
                class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div
                    x-show="showModal"
                    @click="!loading && (showModal = false)"
                    class="fixed inset-0 bg-black/40 backdrop-blur-sm transition-opacity"
                    aria-hidden="true"></div>

                <div
                    x-show="showModal"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="relative z-10 bg-white rounded-2xl w-full max-w-md overflow-hidden shadow-2xl">
                    <div class="h-1 bg-gradient-to-r from-red-500 to-red-600"></div>

                    <div class="p-8">
                        <div class="text-center mb-6">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gradient-to-br from-red-50 to-red-100 mb-4">
                                <div class="text-2xl">
                                    {!! $titleIcon ?? $successIcon !!}
                                </div>
                            </div>

                            <h3 class="text-2xl font-bold text-gray-900 mb-2">
                                {{ $title }}
                            </h3>

                            <p class="text-gray-600 leading-relaxed">
                                {{ $message }}
                            </p>
                        </div>

                        <!-- Action buttons -->
                        <div class="flex flex-col sm:flex-row gap-3 mt-8">
                            <button
                                @click="showModal = false"
                                :disabled="loading"
                                :class="loading ? 'cursor-not-allowed' : 'hover:bg-gray-100 active:bg-gray-200'"
                                class="flex-1 px-6 py-3 rounded-xl border border-gray-300 text-gray-700 font-medium
                           transition-all duration-200 disabled:opacity-50">
                                {{ $cancelLabel }}
                            </button>

                            <button
                                @click="submit"
                                :disabled="loading"
                                :class="loading ? 'cursor-not-allowed' : 'hover:bg-green-700 hover:shadow-lg active:bg-green-800 active:shadow-md'"
                                class="flex-1 px-6 py-3 rounded-xl bg-gradient-to-r from-green-600 to-green-500 
                           text-white font-medium shadow-md transition-all duration-200
                           disabled:opacity-50 flex items-center justify-center gap-2">

                                <svg
                                    x-show="loading"
                                    class="animate-spin h-5 w-5"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                </svg>
                                <span x-text="loading ? 'Processing...' : '{{ $confirmLabel }}'"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>