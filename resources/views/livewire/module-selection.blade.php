<div>
    <div class="bg-white rounded-xl border border-gray-200 p-6 hover:shadow-lg transition-shadow">
        <div class="flex items-center gap-4 mb-4">
            <div class="w-14 h-14 bg-emerald-50 rounded-xl flex items-center justify-center">
                <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                    </path>
                </svg>
            </div>
            <div>
                <h5 class="text-lg font-bold text-gray-900">Module Configuration</h5>
                <p class="text-sm text-gray-500">Select existing or create new module</p>
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Choose from Master
                Modules</label>
            <select @if ($isNewModule) disabled @endif class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all" name="moduleId" wire:model.live="moduleId">
                <option value="">-- Select Existing Module --</option>
                @foreach ($moduleList as $mod)
                    <option value="{{ $mod->id }}">{{ $mod->module_name }}
                        ({{ $mod->module_code }})
                    </option>
                @endforeach
            </select>
            @error('moduleId')
                <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror
        </div>

        <label class="flex items-center gap-3 mb-4 p-3 bg-gray-50 rounded-xl cursor-pointer">
            <input class="w-5 h-5 text-indigo-600 rounded focus:ring-indigo-500" name="isNewModule" type="checkbox" value="1" wire:model.live="isNewModule">
            <span class="text-sm font-medium text-gray-700">Create a new module instead</span>
        </label>

        @if ($isNewModule)
            <div class="bg-gray-50 rounded-xl p-4">
                <h6 class="font-medium text-gray-900 mb-3">New Module Details</h6>
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Module
                            Name</label>
                        <input class="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 @error('newModuleName') border-red-500 @enderror" placeholder="e.g., Caste Correction" type="text" wire:model="newModuleName">
                        @error('newModuleName')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Module
                            Code</label>
                        <input class="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 uppercase @error('newModuleCode') border-red-500 @enderror" placeholder="e.g., CASTE_CORR" type="text" wire:model="newModuleCode">
                        @error('newModuleCode')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="flex justify-end pt-6 border-t border-gray-200">
        <button class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-xl transition-colors shadow-lg hover:shadow-xl" type="button" wire:click="saveModule">
            Save Module and Continue
            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M13 7l5 5m0 0l-5 5m5-5H6" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
            </svg>
        </button>
    </div>
</div>
