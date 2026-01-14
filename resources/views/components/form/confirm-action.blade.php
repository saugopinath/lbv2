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
            class="flex items-center gap-2 px-4 py-2 rounded
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
            x-cloak
            class="fixed inset-0 z-50 bg-black/60 flex items-center justify-center">
            <div class="bg-white rounded-lg w-96 p-6 shadow-xl">

                <h3 class="text-lg font-semibold mb-3 flex items-center justify-center gap-2">
                    {!! $titleIcon ?? $defaultTitleIcon !!}
                    <span>{{ $title }}</span>
                </h3>

                <p class="text-sm text-gray-600 text-center mb-6">
                    {{ $message }}
                </p>

                <div class="flex justify-end gap-2">
                    <button
                        @click="showModal = false"
                        :disabled="loading"
                        class="px-4 py-2 rounded bg-gray-500 text-white hover:bg-gray-600 disabled:opacity-50">
                        {{ $cancelLabel }}
                    </button>

                    <button
                        @click="submit"
                        :disabled="loading"
                        class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700
                           flex items-center gap-2 disabled:opacity-50">
                        <svg
                            x-show="loading"
                            class="animate-spin h-4 w-4 text-white"
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