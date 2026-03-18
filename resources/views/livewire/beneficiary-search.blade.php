<div>
    <div x-data="{
        selectedOption: @entangle('selectedOption'),
        inputValue: @entangle('inputValue'),
        displayType: @js($displayType),
        fields: @js($fields),
        resetInput() {
            this.inputValue = ''
        },
        filterInput(val) {
            if (!this.selectedOption || !this.fields[this.selectedOption]) return;
            if (this.fields[this.selectedOption].type === 'text') {
                this.inputValue = val.replace(/[0-9]/g, '')
            } else {
                this.inputValue = val.replace(/[^0-9]/g, '')
            }
        }
    }" x-init="$watch('selectedOption', () => resetInput())" class="bg-white p-4 rounded-xl shadow border border-gray-200">


        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">

            @if ($isShownScheme)
            <x-form.select name="selectedScheme" label="Select Scheme" wire:model.live="selectedScheme"
                class="border border-gray-300 hover:border-blue-500 focus:border-cyan-500 focus:ring-cyan-500 outline-none text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 dark:hover:border-blue-400 dark:focus:border-green-400 dark:focus:ring-green-400" required>
                <option value="">-- Select --</option>
                @foreach ($schemes as $scheme)
                <option value="{{ $scheme->id }}">
                    {{ $scheme->name }}
                </option>
                @endforeach
            </x-form.select>
            @endif

            <div>
                <label class="block font-bold text-sm text-gray-700 mb-2">
                    Search Criteria:
                    <span class="text-red-600">*</span>
                </label>
                @if ($displayType === 'radio')
                <div class="flex flex-wrap gap-3">
                    @foreach ($fields as $key => $field)
                    <label class="border border-gray-300 hover:border-blue-500 focus:border-cyan-500 focus:ring-cyan-500 outline-none text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 dark:hover:border-blue-400 dark:focus:border-green-400 dark:focus:ring-green-400">
                        <input type="radio" value="{{ $key }}" x-model="selectedOption"
                            class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                        <span class="text-sm text-gray-600 group-hover:text-blue-600">
                            {{ $field['label'] }}
                        </span>
                    </label>
                    @endforeach
                </div>
                @else
                <select x-model="selectedOption"
                    class="border border-gray-300 hover:border-blue-500 focus:border-cyan-500 focus:ring-cyan-500 outline-none text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 dark:hover:border-blue-400 dark:focus:border-green-400 dark:focus:ring-green-400">
                    <option value="">-- Select --</option>
                    @foreach ($fields as $key => $field)
                    <option value="{{ $key }}">{{ $field['label'] }}</option>
                    @endforeach
                </select>
                @endif

                @error('selectedOption')
                <span class="text-red-600 text-xs mt-1 font-semibold block">
                    {{ $message }}
                </span>
                @enderror
            </div>

            <!-- Second Column: Input Field -->
            <div x-show="selectedOption && fields[selectedOption]" x-transition>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <span x-text="fields[selectedOption] ? fields[selectedOption].label + ':' : ''"></span>
                    <span class="text-red-600">*</span>
                </label>
                <input :type="fields[selectedOption]?.input_type || 'text'" x-model="inputValue"
                    :maxlength="fields[selectedOption]?.max"
                    :placeholder="'Enter ' + (fields[selectedOption]?.label || '')"
                    :inputmode="fields[selectedOption]?.type === 'text' ? 'text' : 'numeric'"
                    x-on:input="filterInput($event.target.value)"
                    class="border border-gray-300 hover:border-blue-500 focus:border-cyan-500 focus:ring-cyan-500 outline-none text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 dark:hover:border-blue-400 dark:focus:border-green-400 dark:focus:ring-green-400
                    @error('inputValue') border-red-500 bg-red-50 @enderror">

                @error('inputValue')
                <span class="text-red-600 text-xs mt-1 font-semibold block">
                    {{ $message }}
                </span>
                @enderror
            </div>

        </div>

        <!-- GO Button at the Bottom -->
        <div class="flex justify-center" x-show="selectedOption && fields[selectedOption]" x-transition>
            <button type="button" wire:click="search"
                class="px-8 py-2.5 bg-blue-600 backdrop-blur-md border border-white/30 text-white font-medium rounded-lg hover:bg-blue-600 focus:ring-2 focus:ring-blue-600 transition-all duration-300 shadow-lg flex items-center justify-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <span>Search</span>
            </button>
        </div>
    </div>
</div>