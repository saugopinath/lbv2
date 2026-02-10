<div class="bg-white shadow rounded-xl p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-xl font-bold text-indigo-700">Dup Check Scheme Config (Scheme ID: {{ $schemeId }})</h1>
    </div>
    <form wire:submit.prevent="save" class="space-y-4">
        @foreach ($dupcheckOptions as $key => $label)
            <div
                class="p-4 border rounded-lg {{ $config[$key]['selected'] ? 'bg-indigo-50 border-indigo-200' : 'bg-gray-50' }}">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center">

                    <div class="flex items-center">
                        <input type="checkbox" id="check_{{ $key }}"
                            wire:model.live="config.{{ $key }}.selected"
                            class="w-5 h-5 text-indigo-600 rounded">
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
                                        value="yes" class="mr-2"> Yes
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" wire:model.live="config.{{ $key }}.issame"
                                        value="no" class="mr-2"> No
                                </label>
                            </div>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-xs font-semibold text-gray-500 mb-1">Is Cross Check?</span>
                            <div class="flex gap-4">
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" wire:model.live="config.{{ $key }}.iscross"
                                        value="yes" class="mr-2"> Yes
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" wire:model.live="config.{{ $key }}.iscross"
                                        value="no" class="mr-2"> No
                                </label>
                            </div>
                        </div>

                        <div>
                            @if ($config[$key]['iscross'] === 'yes')
                                <x-form.multiselect label="Schemes for {{ $label }}"
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
            <x-button.loading-button action="save" text="Save Configuration"
                class="w-full md:w-64 px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow-lg" />
        </div>
    </form>
</div>
