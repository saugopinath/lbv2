<div> {{-- ✅ ROOT TAG (VERY IMPORTANT) --}}

    <div
        x-data="{ open: @entangle('show') }"
        x-show="open"
        x-transition
        class="fixed inset-0 flex items-center justify-center bg-black/50 z-50">

        {{-- Modal Box --}}
        <div
            @click.away="$wire.close()"
            class="bg-white rounded-2xl shadow-xl max-w-5xl w-full max-h-[80vh] overflow-y-auto p-6
">

            <h2 class="text-xl font-semibold mb-4">
                Final Review Before Submit
            </h2>

            {{-- TAB DATA --}}
            @foreach($tabsData ?? [] as $tabName => $fields)

            <div class="border rounded-xl p-3 mb-3">

                <h3 class="font-semibold text-indigo-600 mb-2">
                    {{ $tabName }}
                </h3>

                @foreach($fields as $label => $value)

                <div class="flex justify-between text-sm border-b py-1">
                    <span class="text-gray-600">
                        {{ $label }}
                    </span>

                    <span class="font-medium">
                        {{ $value ?: '-' }}
                    </span>
                </div>

                @endforeach

            </div>

            @endforeach


            {{-- BUTTONS --}}
            <div class="flex justify-end gap-3 mt-5">

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