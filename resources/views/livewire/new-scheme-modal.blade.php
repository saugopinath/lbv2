    <div @scheme-created.window="openModal=false" class="max-w-5xl mx-auto bg-white border border-gray-200 rounded-xl shadow-sm p-4 mb-4" x-data="{ openModal: false }">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-800">Define Workflow</h2>
            <x-button.primary @click="openModal=true">Add New Scheme</x-button.primary>
        </div>

        <div @keydown.escape.window="openModal = false" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" x-cloak x-show="openModal" x-transition:enter-end="opacity-100" x-transition:enter-start="opacity-0" x-transition:enter="transition ease-out duration-200" x-transition:leave-end="opacity-0" x-transition:leave-start="opacity-100" x-transition:leave="transition ease-in duration-150">
            <div @click.away="openModal=false" class="w-full max-w-lg bg-white rounded-xl shadow-xl p-6 space-y-4">
                <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                    <h3 class="text-xl font-bold text-gray-800">Add New Scheme</h3>
                    <button @click="openModal = false" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
                </div>
                <form class="space-y-4" wire:submit="saveScheme">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Scheme Name</label>
                        <input class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('schemeName') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror" placeholder="Eg: Textile Pension" type="text" wire:model="schemeName">
                        @error('schemeName')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Scheme Short-Name</label>
                        <input class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('schemeShortName') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror" placeholder="Eg: weavers" type="text" wire:model="schemeShortName">
                        @error('schemeShortName')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Scheme Description</label>
                        <input class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('schemeDescription') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror" placeholder="Eg: Textile Pension" type="text" wire:model="schemeDescription">
                        @error('schemeDescription')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Scheme Department</label>
                        <select class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('schemeDepartment') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror" wire:model="schemeDepartment">
                            <option value="">-- Select --</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}">{{ $department->name . ' (' . $department->short_name . ')' }}</option>
                            @endforeach
                        </select>
                        @error('schemeDepartment')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                        <button @click="openModal = false" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800" type="button">Cancel</button>
                        <x-button.primary type="submit">Save Scheme</x-button.primary>
                    </div>
                </form>
            </div>
        </div>

        <div>
            @if (session()->has('message'))
                <div class="flex items-center justify-between p-2 mt-4 text-sm text-green-800 bg-green-50 border border-green-200 rounded-xl" role="alert" x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-transition>
                    <div class="flex items-center gap-2">
                        <!-- Success Icon (Optional) -->
                        <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path clip-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" fill-rule="evenodd" />
                        </svg>
                        <span class="font-medium">{{ session('message') }}</span>
                    </div>
                    <button @click="show=false" class="text-green-600 hover:text-green-800 text-xl font-bold leading-none p-1" type="button">&times;</button>
                </div>
            @endif
        </div>
    </div>
