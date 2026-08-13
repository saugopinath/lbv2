<div
    x-data="{ open: false }"
    x-show="open"
    x-cloak
    x-init="$watch('open', value => {
        if (value === false) {
            $wire.resetForm();
        }
    })"
    x-on:show-modal.window="open = true"
    @hide-modal.window="open = false"
    @keydown.escape.window="open = false"
    wire:ignore.self
    class="fixed inset-0 z-[60] flex items-center justify-center overflow-y-auto p-4 sm:p-6"
    role="dialog"
    aria-modal="true"
>
    <!-- Backdrop with blur -->
    <div 
        x-show="open"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity"
        @click="open = false"
    ></div>

    <!-- Modal Content -->
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        class="relative w-full max-w-lg transform overflow-hidden rounded-2xl bg-white shadow-2xl transition-all"
    >
        <form wire:submit.prevent="saveDsMark">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-white">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-blue-50 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 leading-none">DS Mark Confirmation</h2>
                        <p class="text-xs text-gray-500 mt-1">Please provide the Duare Sarkar details below.</p>
                    </div>
                </div>
                <button type="button" @click="open = false" class="p-2 rounded-full text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Body -->
            <div class="px-6 py-6 space-y-5">
                <div class="grid grid-cols-1 gap-6">
                    <div class="group">
                        <x-form.input
                            name="ds_registration_no"
                            label="Duare Sakar Registration Number"
                            placeholder="Enter Registration Number"
                            required 
                            wire:model="ds_registration_no" 
                        />
                    </div>
                    <div class="group">
                        <x-form.input 
                            type="date" 
                            name="duaresarkarDate" 
                            id="duaresarkarDate" 
                            label="Duare Sakar Date" 
                            required 
                            wire:model="duaresarkarDate" 
                            :max="$currentDate" 
                            :min="$previouesDate" 
                        />
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                <button 
                    type="button"
                    @click="open = false" 
                    class="inline-flex justify-center items-center px-5 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-200 transition-all"
                >
                    Cancel
                </button>
                <button 
                    type="submit"
                    class="inline-flex justify-center items-center px-6 py-2.5 text-sm font-semibold text-white bg-blue-600 rounded-xl hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-lg shadow-blue-200 transition-all"
                >
                    <span>Confirm Mark</span>
                    <svg class="ml-2 -mr-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </button>
            </div>
        </form>
    </div>
</div>