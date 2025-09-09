<div x-data="{ open: false, action: '' }"
    x-on:open-bulk-revert-modal.window="open = true; action = $event.detail.action"
    x-on:close-bulk-revert-modal.window="open = false">
    <div x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 backdrop-blur-sm flex items-center justify-center z-50">
        <div x-show="open"
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="opacity-0 scale-90"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-90"
            class="bg-white p-6 rounded-lg shadow-lg max-w-md w-full">
            <h2 class="text-lg font-bold mb-2">Confirm Bulk <span x-text="action.charAt(0).toUpperCase() + action.slice(1)"></span></h2>
            <p class="mb-4">Are you sure you want to <span x-text="action"></span> the selected records?</p>
            <div class="mb-2">
                <x-form.input type="textarea"
                    id="reason"
                    name="reason"
                    label="Reason" required />
            </div>
            <div class="flex space-x-2">
                <button class="bg-red-500 text-white px-4 py-2 rounded"
                    @click="$dispatch('confirm-bulk-revert')">
                    Yes, <span x-text="action.charAt(0).toUpperCase() + action.slice(1)"></span>
                </button>
                <button class="bg-gray-300 px-4 py-2 rounded"
                    @click="open = false">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>