<div>
    <div x-data="{
        selectedOption: @entangle('selectedOption'),
        inputValue: @entangle('inputValue'),
        resetIfChanged(old) {
            if (this.selectedOption !== old) this.inputValue = '';
        }
    }" x-init="$watch('selectedOption', (val, old) => resetIfChanged(old))" class="bg-white p-4 rounded shadow">
        <div class="mb-3">
            <label class="block font-medium text-sm text-gray-700 mb-2">
                Please select which one do you want to Search?
            </label>
            <div class="flex flex-wrap gap-4">
                @foreach ($searchOptions as $key => $label)
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="radio" value="{{ $label }}" name="{{ $key }}"
                            x-model="selectedOption" class="text-indigo-600">
                        <span class="text-sm">{{ $label }}</span>
                    </label>
                @endforeach
            </div>
        </div>
        <div class="mb-3">
            <label class="block text-sm font-medium text-gray-700">
                <span x-text="selectedOption ? selectedOption : 'Enter value'"></span>
                <span class="text-red-600">*</span>
            </label>
            <input type="text" x-model="inputValue" name="inputValue"
                :placeholder="selectedOption ? 'Enter ' + selectedOption : 'Enter value'"
                class="mt-2 block w-full p-2 border rounded">
            @error('inputValue')
                <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span>
            @enderror
        </div>
        <div class="flex justify-end gap-2">
            <button type="button" class="px-4 py-2 bg-blue-600 text-white rounded"
                x-on:click="let selected = document.querySelector('input[x-model=\'selectedOption\']:checked');
        let nameKey = selected ? selected.getAttribute('name') : null;
        let payload = { key: nameKey, value: inputValue };
                $wire.dispatch('searchTriggered', [payload])">
                GO
            </button>
        </div>
    </div>
</div>
