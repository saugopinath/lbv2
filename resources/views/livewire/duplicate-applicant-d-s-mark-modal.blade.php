<div
    x-data="{ open: false }"
    x-show="open"
    x-cloak
    {{-- CRITICAL FIX: Use 'x-on:' because '@show' is a reserved Blade command --}}
    x-on:show-modal.window="open = true" 
    @hide-modal.window="open = false"
    @keydown.escape.window="open = false"
    wire:ignore.self
    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
>
    <div 
        class="bg-white rounded-lg p-6 w-1/2 shadow-xl transform transition-all"
        @click.away="open = false"
    >
        <div class="flex justify-between items-center mb-4 border-b pb-2">
            <h2 class="text-xl font-bold text-gray-800">DS Mark Confirmation</h2>
            <button @click="open = false" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
        </div>

        <div class="space-y-4">
            <p class="text-gray-600">
                Are you sure you want to mark this applicant as Duplicate?
            </p>

            <div class="bg-violet-50 p-4 rounded text-center border border-violet-200">
                <span class="font-semibold text-gray-700">Applicant ID:</span> 
                <span class="text-violet-800 font-bold text-xl ml-2">
                    {{ $applicantId ?? 'Loading...' }}
                </span>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <button 
                @click="open = false" 
                class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition"
            >
                Cancel
            </button>

            <button 
                wire:click="saveDsMark" 
                wire:loading.attr="disabled"
                class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition flex items-center"
            >
                <span wire:loading.remove wire:target="saveDsMark">Confirm Mark</span>
                <span wire:loading wire:target="saveDsMark">Processing...</span>
            </button>
        </div>
    </div>
</div>