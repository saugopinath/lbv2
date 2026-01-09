<div>
        <div 
            x-data="{ openModal: @entangle('openModal') }" 
            x-show="openModal" 
            x-cloak
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-90"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-90"
            class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50 p-4"
        >
            <div class="bg-white w-full max-w-3xl rounded-lg shadow-lg overflow-hidden">
                
                <!-- Header -->
                <div class="bg-green-100 px-6 py-4 text-center text-lg font-semibold text-green-800">
                    Confirm Submit
                </div>

                <!-- Body -->
                <div class="p-6 space-y-6 text-sm text-gray-700 overflow-y-auto max-h-[80vh]">
                    <div class="grid grid-cols-2">
                        <div class="text-center">
                            <img src="https://c.animaapp.com/mdn4r47eB5hzlO/img/biswo-2.png" 
                                 alt="Logo" 
                                 class="w-24 h-24 mx-auto">
                        </div>
                        <div class="text-left p-4 text-gray-800">
                            <h2 class="text-xl font-bold">Government of West Bengal</h2>
                            <h3 class="text-md text-gray-600">Lakshmir Bhandar Scheme</h3>
                        </div>
                    </div>

                    <!-- Laravel Blade Component inside Livewire -->
                    <x-apllicant-modal.personal-details :id="$application_id" />
                    <x-apllicant-modal.contact-details :id="$application_id" />
                    <x-apllicant-modal.bank-account-details :id="$application_id" />
                    <x-apllicant-modal.self-declarations :id="$application_id" />
                </div>

                <!-- Footer -->
                <div class="flex justify-end p-4 border-t border-gray-200 space-x-4">
                    <button wire:click="close" 
                        class="px-6 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                        Cancel
                    </button>
                    <button 
                        class="px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700 font-semibold tracking-wide">
                        Submit
                    </button>
                </div>
            </div>
        </div>
</div>
