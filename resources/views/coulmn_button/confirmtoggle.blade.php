@props([
'itemId'=>null,
'action'=>null,
'state'=>false,
'title'=>'Toggle Status',
'message'=>'Are you sure?',
'tooltipOn'=>'Deactivate',
'tooltipOff'=>'Activate'
])

<div
    x-data="{
showTooltip:false,
showModal:false,
isOn:@js((bool)$state)
}"
    class="relative inline-block">

    <button
    
        type="button"
        @click="showModal=true"
        @mouseenter="showTooltip=true"
        @mouseleave="showTooltip=false"
        class="relative inline-flex items-center cursor-pointer">

        <span
            :class="isOn ? 'bg-green-600':'bg-gray-300'"
            class="w-11 h-6 flex items-center rounded-full p-1 transition">

            <span
                :class="isOn ? 'translate-x-5':'translate-x-0'"
                class="w-5 h-5 bg-white rounded-full shadow transform transition">
            </span>

        </span>

    </button>

    <div
        x-show="showTooltip"
        x-cloak
        class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1 px-2 py-1 text-xs text-white bg-gray-800 rounded">

        <span x-show="isOn">{{ $tooltipOn }}</span>
        <span x-show="!isOn">{{ $tooltipOff }}</span>

    </div>

    <div
        x-show="showModal"
        x-cloak
        class="fixed inset-0 flex items-center justify-center bg-black/40 z-50">

        <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-md">

            <h2 class="text-lg font-semibold mb-4">
                {{ $title }}
            </h2>

            <p class="text-sm text-gray-600 mb-6">
                {{ $message }}
            </p>

            <div class="flex justify-end gap-2">

                <button
                    @click="showModal=false"
                    class="px-4 py-2 bg-gray-200 rounded">
                    Cancel
                </button>

                <button
                    wire:click="{{ $action }}({{ $itemId }})"
                    @click="showModal=false; isOn=!isOn"
                    class="px-4 py-2 bg-green-600 text-white rounded">
                    Confirm
                </button>

            </div>

        </div>

    </div>

</div>