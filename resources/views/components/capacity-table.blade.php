@props([
'items', // Collection of items (schemes or locations)
'type', // 'scheme' or 'location'
'data', // The data array (schemes_data or locations_data)
'saveMethod', // Method name to call on save
'titleField' => 'name', // Field to display as title
'subtitleField' => 'id', // Field to display as subtitle
'subtitlePrefix' => 'ID: ', // Prefix for subtitle
'disabled' => false, // Whether save buttons are disabled
'showExtraCondition' => false, // Whether to show extra condition column
'locationLevel' => null, // Add this for location type
'deleteMethod' => null, // Method name to call on delete
])

<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Sl No.</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                    {{ $type === 'scheme' ? 'Scheme' : ucfirst(str_replace('_', ' ', $locationLevel ?? 'Location')) }}
                </th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Application Type</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total Capacity</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Normal Capacity</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">DS Capacity</th>
                @if($showExtraCondition)
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Extra Condition</th>
                @endif
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($items as $index => $item)
            <tr class="hover:bg-gray-50" wire:key="{{ $type }}-{{ $item->id }}">
                <td class="px-3 py-4 text-center whitespace-nowrap">{{ $index + 1 }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{{ $item->$titleField }}</div>
                    <div class="text-xs text-gray-500">{{ $subtitlePrefix }}{{ $item->$subtitleField }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <select wire:model="{{ $data }}.{{ $index }}.entry_type"
                        wire:change="resetRowCapacities('{{ $data }}', {{ $index }})"
                        class="border border-gray-300 hover:border-blue-500 focus:border-cyan-500 focus:ring-cyan-500 outline-none text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 dark:hover:border-blue-400 dark:focus:border-green-400 dark:focus:ring-green-400">
                        <option value="">Select Application Type</option>
                        <option value="0">Not Specified</option>
                        <option value="1">Normal Entry Only</option>
                        <option value="2">DS Entry Only</option>
                        <option value="both">Both (Normal & DS Entry)</option>
                    </select>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <input type="number"
                        wire:model="{{ $data }}.{{ $index }}.total_capacity"
                        x-bind:disabled="$wire.{{ $data }}[{{ $index }}].entry_type === 'both' || $wire.{{ $data }}[{{ $index }}].entry_type === '1' || $wire.{{ $data }}[{{ $index }}].entry_type === '2' || $wire.{{ $data }}[{{ $index }}].entry_type === ''"
                        class="border border-gray-300 hover:border-blue-500 focus:border-cyan-500 focus:ring-cyan-500 outline-none text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 dark:hover:border-blue-400 dark:focus:border-green-400 dark:focus:ring-green-400 disabled:cursor-not-allowed"
                        placeholder="Total Capacity">
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <input type="number"
                        wire:model="{{ $data }}.{{ $index }}.normal_capacity"
                        x-bind:disabled="$wire.{{ $data }}[{{ $index }}].entry_type !== 'both' && $wire.{{ $data }}[{{ $index }}].entry_type !== '1' || $wire.{{ $data }}[{{ $index }}].entry_type === ''"
                        class="border border-gray-300 hover:border-blue-500 focus:border-cyan-500 focus:ring-cyan-500 outline-none text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 dark:hover:border-blue-400 dark:focus:border-green-400 dark:focus:ring-green-400 disabled:cursor-not-allowed"
                        placeholder="Normal Capacity">
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <input type="number"
                        wire:model="{{ $data }}.{{ $index }}.ds_capacity"
                        x-bind:disabled="$wire.{{ $data }}[{{ $index }}].entry_type !== 'both' && $wire.{{ $data }}[{{ $index }}].entry_type !== '2' || $wire.{{ $data }}[{{ $index }}].entry_type === ''"
                        class="border border-gray-300 hover:border-blue-500 focus:border-cyan-500 focus:ring-cyan-500 outline-none text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 dark:hover:border-blue-400 dark:focus:border-green-400 dark:focus:ring-green-400 disabled:cursor-not-allowed"
                        placeholder="DS Capacity">
                </td>
                @if($showExtraCondition)
                <td class="px-6 py-4 whitespace-nowrap">
                    <input type="text"
                        wire:model="{{ $data }}.{{ $index }}.extra_condition"
                        class="border border-gray-300 hover:border-blue-500 focus:border-cyan-500 focus:ring-cyan-500 outline-none text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 dark:hover:border-blue-400 dark:focus:border-green-400 dark:focus:ring-green-400 disabled:cursor-not-allowed"
                        placeholder="Extra Condition">
                </td>
                @endif
                <td class="px-6 py-4 whitespace-nowrap flex items-center gap-2">
                    <x-form.confirm-action
                        :itemId="$item->id . ', ' . $index"
                        :action="$saveMethod"
                        :disabled="$disabled"
                        title="Confirm Save"
                        message="Are you sure to save the capacity?"
                        confirmLabel="Save"
                        tooltip="Save Capacity"
                        :icon="'<svg class=\'w-4 h-4\' fill=\'#fafbfc\' stroke=\'#fafbfc\' viewBox=\'0 0 640 640\'><path d=\'M320 576C178.6 576 64 461.4 64 320C64 178.6 178.6 64 320 64C461.4 64 576 178.6 576 320C576 461.4 461.4 576 320 576zM438 209.7C427.3 201.9 412.3 204.3 404.5 215L285.1 379.2L233 327.1C223.6 317.7 208.4 317.7 199.1 327.1C189.8 336.5 189.7 351.7 199.1 361L271.1 433C276.1 438 282.9 440.5 289.9 440C296.9 439.5 303.3 435.9 307.4 430.2L443.3 243.2C451.1 232.5 448.7 217.5 438 209.7z\' /></svg>'"
                        class="px-3 py-1 text-sm rounded-md transition-colors {{ $disabled ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-green-50 hover:bg-green-100 text-green-600 border border-green-200 cursor-pointer' }}" />
                    @if($deleteMethod)
                    <x-form.confirm-reset
                        :itemId="$item->id . ', ' . $index"
                        :disabled="$this->isResetDisabled($data, $index, $disabled)"
                        :action="$deleteMethod"
                        title="Reset Capacity"
                        message="Are you sure to reset this capacity?"
                        confirm-label="Yes, Reset"
                        cancel-label="Cancel"
                        tooltip="Reset Capacity"
                        :icon="'<svg class=\'w-5 h-4\' fill=\'#fafbfce4\' stroke=\'currentColor\' viewBox=\'0 0 640 640\'><path d=\'M129.9 292.5C143.2 199.5 223.3 128 320 128C373 128 421 149.5 455.8 184.2C456 184.4 456.2 184.6 456.4 184.8L464 192L416.1 192C398.4 192 384.1 206.3 384.1 224C384.1 241.7 398.4 256 416.1 256L544.1 256C561.8 256 576.1 241.7 576.1 224L576.1 96C576.1 78.3 561.8 64 544.1 64C526.4 64 512.1 78.3 512.1 96L512.1 149.4L500.8 138.7C454.5 92.6 390.5 64 320 64C191 64 84.3 159.4 66.6 283.5C64.1 301 76.2 317.2 93.7 319.7C111.2 322.2 127.4 310 129.9 292.6zM573.4 356.5C575.9 339 563.7 322.8 546.3 320.3C528.9 317.8 512.6 330 510.1 347.4C496.8 440.4 416.7 511.9 320 511.9C267 511.9 219 490.4 184.2 455.7C184 455.5 183.8 455.3 183.6 455.1L176 447.9L223.9 447.9C241.6 447.9 255.9 433.6 255.9 415.9C255.9 398.2 241.6 383.9 223.9 383.9L96 384C87.5 384 79.3 387.4 73.3 393.5C67.3 399.6 63.9 407.7 64 416.3L65 543.3C65.1 561 79.6 575.2 97.3 575C115 574.8 129.2 560.4 129 542.7L128.6 491.2L139.3 501.3C185.6 547.4 249.5 576 320 576C449 576 555.7 480.6 573.4 356.5z\' /></svg>'"
                        class="px-3 py-1 text-sm rounded-md transition-colors
        {{ $this->isResetDisabled($data, $index, $disabled) ? 'cursor-not-allowed' : 'cursor-pointer' }}" />
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                    No {{ $type === 'scheme' ? 'schemes' : 'locations' }} available
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>