@php
    $isDisabled = $already && !$isEdit;
@endphp
<div class="bg-white shadow rounded-xl p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-xl font-bold text-indigo-700">
            Age Management Configuration
        </h1>
        @if ($isEdit)
            <span class="px-3 py-1 bg-amber-100 text-amber-800 text-xs font-semibold rounded-full flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                </svg>
                Edit Mode
            </span>
        @elseif ($already)
            <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                View Mode
            </span>
        @endif
    </div>
    <form wire:submit.prevent="save" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 p-4 rounded-lg">
            <x-form.input label="General Min Age" wire:model.defer="minage" name="minage" :disabled="$isDisabled"
                x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0,3); $wire.set('minage', $el.value);" />

            <x-form.input label="General Max Age" wire:model.defer="maxage" name="maxage" :disabled="$isDisabled"
                x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0,3); $wire.set('maxage', $el.value);" />
        </div>
        <div class="p-2">
            <label class="font-semibold block mb-2 text-gray-700">Does this scheme have special cases?</label>
            <div class="flex gap-6">
                <x-form.radio name="isspecial" value="yes" label="Yes" wire:model.live="isspecial" :disabled="$isDisabled" />
                <x-form.radio name="isspecial" value="no" label="No" wire:model.live="isspecial" :disabled="$isDisabled" />
            </div>
        </div>
        @if ($isspecial === 'yes')
            <div class="space-y-4 pt-4">
                <div class="flex justify-between items-center">
                    <h3 class="font-bold text-gray-800">Special Age Rules</h3>
                    @if (!$isDisabled)
                        <button type="button" wire:click="addSpecialCase"
                            class="bg-indigo-600 text-white px-4 py-2 rounded shadow hover:bg-indigo-700 text-sm">
                            + Add More
                        </button>
                    @endif
                </div>
                @foreach ($selectedSpecialCases as $index => $case)
                    <div
                        class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end bg-indigo-50 p-4 rounded-lg border border-indigo-100 shadow-sm">
                        <div class="md:col-span-1">
                            <x-form.select label="Category" name="selectedSpecialCases.{{ $index }}.case_id" :disabled="$isDisabled"
                                wire:model.live="selectedSpecialCases.{{ $index }}.case_id">
                                <option value="">-- Choose --</option>
                                @foreach ($this->getAvailableOptions($index) as $option)
                                    <option value="{{ $option->id }}">{{ $option->name }}</option>
                                @endforeach
                            </x-form.select>
                        </div>
                        <div>
                            <x-form.input label="Min Age" name="selectedSpecialCases.{{ $index }}.min" :disabled="$isDisabled"
                                wire:model.defer="selectedSpecialCases.{{ $index }}.min"
                                x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0,3); $wire.set('selectedSpecialCases.{{ $index }}.min', $el.value);" />
                        </div>
                        <div>
                            <x-form.input label="Max Age" name="selectedSpecialCases.{{ $index }}.max" :disabled="$isDisabled"
                                wire:model.defer="selectedSpecialCases.{{ $index }}.max"
                                x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0,3); $wire.set('selectedSpecialCases.{{ $index }}.max', $el.value);" />
                        </div>
                        <div class="flex justify-center pb-2">
                            @if (!$isDisabled)
                                <button type="button" wire:click="removeSpecialCase({{ $index }})"
                                    class="text-red-500 hover:text-red-700 font-bold">
                                    Remove
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
        <div class="border-t pt-4 flex justify-end">
            @if (!$isDisabled)
                <x-button.loading-button action="save" text="Save Changes"
                    class="bg-indigo-700 text-white py-3 px-8 rounded-xl font-bold mt-2" />
            @else
                <div class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-green-800 bg-green-50 border border-green-200 rounded-lg">
                    <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path clip-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" fill-rule="evenodd" />
                    </svg>
                    Already Configured (View Mode)
                </div>
            @endif
        </div>
    </form>
</div>