<div
    x-data="{ open: false }"
    x-show="open"
    x-cloak
    {{-- CRITICAL FIX: Use 'x-on:' because '@show' is a reserved Blade command --}}
    x-on:show-modal.window="open = true"
    @hide-modal.window="open = false"
    @keydown.escape.window="open = false"
    wire:ignore.self
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 bg-opacity-50">
    <div
        class="bg-white rounded-lg p-6 w-1/2 shadow-xl transform transition-all"
        @click.away="open = false">
        <div class="flex justify-between items-center mb-4 border-b pb-2">
            <h2 class="text-xl font-bold text-gray-800">DS Mark Confirmation</h2>
            <button @click="open = false" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
        </div>

        <div class="space-y-4">
            <div class="grid gap-6 md:grid-cols-2 mb-2 pl-4 pr-4">
                <div>
                    <x-form.input
                        name="reg_no"
                        label="Duare Sakar Registration Number"
                        placeholder="Enter Duare Sakar Registration Number"
                        required wire:model="reg_no" />
                </div>
                <div>
                    <x-form.input type="date" name="ds_date" id="ds_date" label="Duare Sakar Date" required wire:model="ds_date" :max="$cdate" :min="$pdate" />
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <button
                @click="open = false"
                class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition">
                Cancel
            </button>

            <button
                wire:click="saveDsMark"
                wire:loading.attr="disabled"
                class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition flex items-center">
                <span wire:loading.remove wire:target="saveDsMark">Confirm Mark</span>
                <span wire:loading wire:target="saveDsMark">Processing...</span>
            </button>
        </div>
    </div>
</div>