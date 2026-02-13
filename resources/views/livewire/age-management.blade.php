<div class="bg-white shadow rounded-xl p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-xl font-bold text-indigo-700">
            Age Management Configuration
        </h1>
    </div>
    <form wire:submit.prevent="save" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 p-4 rounded-lg">
            <x-form.input label="General Min Age" wire:model.defer="minage" name="minage"
                x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0,3); $wire.set('minage', $el.value);" />

            <x-form.input label="General Max Age" wire:model.defer="maxage" name="maxage"
                x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0,3); $wire.set('maxage', $el.value);" />
        </div>
        <div class="p-2">
            <label class="font-semibold block mb-2 text-gray-700">Does this scheme have special cases?</label>
            <div class="flex gap-6">
                <x-form.radio name="isspecial" value="yes" label="Yes" wire:model.live="isspecial" />
                <x-form.radio name="isspecial" value="no" label="No" wire:model.live="isspecial" />
            </div>
        </div>
        @if ($isspecial === 'yes')
            <div class="space-y-4 pt-4">
                <div class="flex justify-between items-center">
                    <h3 class="font-bold text-gray-800">Special Age Rules</h3>
                    <button type="button" wire:click="addSpecialCase"
                        class="bg-indigo-600 text-white px-4 py-2 rounded shadow hover:bg-indigo-700 text-sm">
                        + Add More
                    </button>
                </div>
                @foreach ($selectedSpecialCases as $index => $case)
                    <div
                        class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end bg-indigo-50 p-4 rounded-lg border border-indigo-100 shadow-sm">
                        <div class="md:col-span-1">
                            <x-form.select label="Category" name="selectedSpecialCases.{{ $index }}.case_id"
                                wire:model.live="selectedSpecialCases.{{ $index }}.case_id">
                                <option value="">-- Choose --</option>
                                @foreach ($this->getAvailableOptions($index) as $option)
                                    <option value="{{ $option->id }}">{{ $option->name }}</option>
                                @endforeach
                            </x-form.select>
                        </div>
                        <div>
                            <x-form.input label="Min Age" name="selectedSpecialCases.{{ $index }}.min"
                                wire:model.defer="selectedSpecialCases.{{ $index }}.min"
                                x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0,3); $wire.set('selectedSpecialCases.{{ $index }}.min', $el.value);" />
                        </div>
                        <div>
                            <x-form.input label="Max Age" name="selectedSpecialCases.{{ $index }}.max"
                                wire:model.defer="selectedSpecialCases.{{ $index }}.max"
                                x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0,3); $wire.set('selectedSpecialCases.{{ $index }}.max', $el.value);" />
                        </div>
                        <div class="flex justify-center pb-2">
                            <button type="button" wire:click="removeSpecialCase({{ $index }})"
                                class="text-red-500 hover:text-red-700 font-bold">
                                Remove
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
        <div class="border-t">
            <x-button.loading-button action="save" text="Save Changes"
                class="bg-indigo-700 text-white py-3 rounded-xl font-bold mt-6" />
        </div>
    </form>
</div>