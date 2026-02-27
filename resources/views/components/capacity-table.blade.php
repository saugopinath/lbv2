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
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    {{ $type === 'scheme' ? 'Scheme' : ucfirst(str_replace('_', ' ', $locationLevel ?? 'Location')) }}
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Application Type</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Capacity</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Normal Capacity</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DS Capacity</th>
                @if($showExtraCondition)
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Extra Condition</th>
                @endif
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($items as $index => $item)
            <tr class="hover:bg-gray-50" wire:key="{{ $type }}-{{ $item->id }}">
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
                        :icon="'<svg class=\'w-4 h-4\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4\' /></svg>'"
                        class="px-3 py-1 text-sm rounded-md transition-colors {{ $disabled ? 'cursor-not-allowed' : 'cursor-pointer' }}" />


                    @if($deleteMethod)
                    @php
                    $rowData = $this->{$data}[$index] ?? [];
                    $hasNoData = empty($rowData['entry_type']) &&
                    empty($rowData['total_capacity']) &&
                    empty($rowData['normal_capacity']) &&
                    empty($rowData['ds_capacity']);
                    $isResetDisabled = $disabled || $hasNoData;
                    @endphp
                    <x-form.confirm-reset
                        :itemId="$item->id . ', ' . $index"
                        :disabled="$isResetDisabled"
                        :action="$deleteMethod"
                        title="Reset Capacity"
                        message="Are you sure to reset this capacity?"
                        confirm-label="Yes, Reset"
                        cancel-label="Cancel"
                        tooltip="Reset Capacity"
                        :icon="'<svg class=\'w-4 h-4\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16\' /></svg>'"
                        class="px-3 py-1 text-sm rounded-md transition-colors
        {{ $isResetDisabled ? 'cursor-not-allowed' : 'cursor-pointer' }}" />
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