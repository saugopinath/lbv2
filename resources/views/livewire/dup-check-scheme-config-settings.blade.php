@php
    $isDisabled = $already && !$isEdit;
@endphp
<div class="bg-white shadow rounded-xl p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-xl font-bold text-indigo-700">Dup Check Scheme Config</h1>
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
    <form wire:submit.prevent="save" class="space-y-4">
        @foreach ($dupcheckOptions as $key => $label)
            <div
                class="p-4 border rounded-lg {{ $config[$key]['selected'] ? 'bg-indigo-50 border-indigo-200' : 'bg-gray-50' }}">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center">

                    <div class="flex items-center">
                        <input type="checkbox" id="check_{{ $key }}"
                            wire:model.live="config.{{ $key }}.selected"
                            {{ $isDisabled ? 'disabled' : '' }}
                            class="w-5 h-5 text-indigo-600 rounded disabled:opacity-50 disabled:cursor-not-allowed">
                        <label for="check_{{ $key }}"
                            class="ml-3 font-bold text-gray-700 uppercase cursor-pointer">
                            {{ $label }}
                        </label>
                    </div>

                    @if ($config[$key]['selected'])
                        <div class="flex flex-col">
                            <span class="text-xs font-semibold text-gray-500 mb-1">Is Same Check?</span>
                            <div class="flex gap-4">
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" wire:model.live="config.{{ $key }}.issame"
                                        value="yes" {{ $isDisabled ? 'disabled' : '' }} class="mr-2 disabled:opacity-50 disabled:cursor-not-allowed"> Yes
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" wire:model.live="config.{{ $key }}.issame"
                                        value="no" {{ $isDisabled ? 'disabled' : '' }} class="mr-2 disabled:opacity-50 disabled:cursor-not-allowed"> No
                                </label>
                            </div>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-xs font-semibold text-gray-500 mb-1">Is Cross Check?</span>
                            <div class="flex gap-4">
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" wire:model.live="config.{{ $key }}.iscross"
                                        value="yes" {{ $isDisabled ? 'disabled' : '' }} class="mr-2 disabled:opacity-50 disabled:cursor-not-allowed"> Yes
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" wire:model.live="config.{{ $key }}.iscross"
                                        value="no" {{ $isDisabled ? 'disabled' : '' }} class="mr-2 disabled:opacity-50 disabled:cursor-not-allowed"> No
                                </label>
                            </div>
                        </div>

                        <div>
                            @if ($config[$key]['iscross'] === 'yes')
                                <x-form.multiselect label="Schemes for {{ $label }}"
                                    :disabled="$isDisabled"
                                    wire:model="config.{{ $key }}.schemes" :options="$schemeOptions" />
                            @else
                                <span class="text-sm text-gray-400 italic font-medium">Checks only within same
                                    scheme.</span>
                            @endif
                        </div>
                    @else
                        <div class="md:col-span-2 text-sm text-gray-400 italic">
                            Select this field to configure validation rules.
                        </div>
                    @endif
                </div>
            </div>
        @endforeach

        <div class="mt-8 pt-6 border-t flex justify-end">
            @if (!$isDisabled)
                <x-button.loading-button action="save" text="Save Configuration"
                    class="w-full md:w-64 px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow-lg" />
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