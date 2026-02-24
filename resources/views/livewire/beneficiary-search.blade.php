<div>
    <div x-data="{
        selectedOption: @entangle('selectedOption'),
        inputValue: @entangle('inputValue'),
        fields: @js($fields),
        resetInput() {
            this.inputValue = ''
        },
        filterInput(val) {
            if (this.fields[this.selectedOption].type === 'text') {
                this.inputValue = val.replace(/[0-9]/g, '')
            } else {
                this.inputValue = val.replace(/[^0-9]/g, '')
            }
        }
    }" x-init="$watch('selectedOption', () => resetInput())" class="bg-white p-4 rounded shadow border border-gray-200">
        <div class="mb-4">
            <label class="block font-bold text-sm text-gray-700 mb-3">
                Please select search criteria:
            </label>
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
            @error('selectedOption')
                <span class="text-red-600 text-xs mt-2 font-semibold block">
                    {{ $message }}
                </span>
            @enderror
        </div>
        @if ($selectedOption)
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">
                    <span x-text="fields[selectedOption].label"></span>
                    <span class="text-red-600">*</span>
                </label>
                <input type="text" x-model="inputValue" :maxlength="fields[selectedOption].max"
                    :placeholder="'Enter ' + fields[selectedOption].label"
                    :inputmode="fields[selectedOption].type === 'text' ? 'text' : 'numeric'"
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
                <button type="button" wire:click="search"
                    class="px-6 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500">
                    GO
                </button>
            </div>
        @endif
    </div>
</div>
