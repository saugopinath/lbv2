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
        }" x-init="$watch('selectedOption', () => resetInput())">
        @if ($isShownScheme)
        <div>
            <x-form.select name="selectedScheme" label="Select Scheme" wire:model.live="selectedScheme"
                required>
                <option value="">-- Select --</option>
                @foreach ($schemes as $scheme)
                <option value="{{ $scheme->id }}">
                    {{ $scheme->name }}
                </option>
                @endforeach
            </x-form.select>
        </div>
        @endif
        <div>
            <div>
                <label>
                    Please select search criteria:
                    <span class="text-red-600">*</span>
                </label>
                @if ($displayType === 'radio')
                <div class="flex flex-wrap gap-4">
                    @foreach ($fields as $key => $field)
                    <label class="flex items-center space-x-2 cursor-pointer group">
                        <input type="radio" value="{{ $key }}" x-model="selectedOption"
                            class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                        <span class="text-sm text-gray-600 group-hover:text-blue-600">
                            {{ $field['label'] }}
                        </span>
                    </label>
                    @endforeach
                </div>
            </div>

            @else
            <div>
                <select x-model="selectedOption"
                    class="block w-full p-2.5 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <option value="">-- Select Search Criteria --</option>
                    @foreach ($fields as $key => $field)
                    <option value="{{ $key }}">{{ $field['label'] }}</option>
                    @endforeach
                </select>
                @endif

                @error('selectedOption')
                <span class="text-red-600 text-xs mt-2 font-semibold block">
                    {{ $message }}
                </span>
                @enderror
            </div>

        </div>

        <div x-show="selectedOption && fields[selectedOption]" x-transition class="mt-4">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">
                    <span x-text="fields[selectedOption] ? fields[selectedOption].label + ': ' : ''"></span>
                    <span class="text-red-600">*</span>
                </label>
                <input :type="fields[selectedOption]?.input_type || 'text'" x-model="inputValue"
                    :maxlength="fields[selectedOption]?.max"
                    :placeholder="'Enter ' + (fields[selectedOption]?.label || '')"
                    :inputmode="fields[selectedOption]?.type === 'text' ? 'text' : 'numeric'"
                    x-on:input="filterInput($event.target.value)"
                    class="mt-2 block w-full p-2.5 border rounded-lg focus:ring-blue-500 focus:border-blue-500
                        @error('inputValue') border-red-500 bg-red-50 @enderror">

                @error('inputValue')
                <span class="text-red-600 text-xs mt-1 font-semibold block">
                    {{ $message }}
                </span>
                @enderror
            </div>

            <div class="flex justify-end">
                <x-button.loading-button action="search" text="Search"></x-button.loading-button>
                {{-- <button type="button" wire:click="search"
                        class="px-6 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500">
                        GO
                    </button> --}}
            </div>
        </div>
    </div>
</div>