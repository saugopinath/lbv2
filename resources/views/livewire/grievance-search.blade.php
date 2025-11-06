<div>
    <div
        x-data="{
            selectedOption: @entangle('selectedOption'),
            inputValue: @entangle('inputValue'),
            initValue: @js($initialMobile ?? ''),

            resetIfChanged(old) {
                if (this.selectedOption !== old) {
                    this.inputValue = '';
                }
            }
        }"
        x-init="
            if (initValue) inputValue = initValue;
            $watch('selectedOption', (val, old) => resetIfChanged(old))
        "
        class="bg-white p-4 rounded shadow">
        <div class="mb-3">
            <label class="block font-medium text-sm text-gray-700 mb-2">
                Please select which one do you want to Search?
            </label>

            <div class="flex flex-wrap gap-4">
                @foreach (['Application ID', 'Beneficiary Name', 'Mobile Number', 'Aadhaar Number', 'Bank Account Number'] as $option)
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input
                        type="radio"
                        value="{{ $option }}"
                        x-model="selectedOption"
                        class="text-indigo-600">
                    <span class="text-sm">{{ $option }}</span>
                </label>
                @endforeach
            </div>
        </div>

        <div class="mb-3">
            <label class="block text-sm font-medium text-gray-700">
                <span x-text="selectedOption ? selectedOption : 'Enter value'"></span>
                <span class="text-red-600">*</span>
            </label>

            <input
                type="text"
                x-model="inputValue"
                :name="selectedOption"
                :placeholder="selectedOption ? 'Enter ' + selectedOption : 'Enter value'"
                class="mt-2 block w-full p-2 border rounded">
        </div>

        <div class="flex justify-end gap-2">
            <button
                type="button"
                class="px-4 py-2 bg-blue-600 text-white rounded"
                x-on:click="$wire.dispatch('searchTriggered', [selectedOption, inputValue])">
                GO
            </button>
            <form action="{{ route('cmo-grievance-search') . '?id=' . $grievanceId }}" method="POST">
                @csrf
                <x-button.danger
                    type="submit"
                    name="action_type" value="send_to_operator" class="bg-blue-500 text-white whitespace-nowrap cursor-pointer">
                    Send To Operator For New Entry
                </x-button.danger>
            </form>


        </div>
    </div>
</div>