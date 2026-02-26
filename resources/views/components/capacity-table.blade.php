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
])

<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    {{ $type === 'scheme' ? 'Scheme' : ucfirst(str_replace('_', ' ', $locationLevel ?? 'Location')) }}
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Entry Type</th>
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
                        <option value="0">Any</option>
                        <option value="1">Normal Only</option>
                        <option value="2">DS Only</option>
                        <option value="both">Both (Normal & DS)</option>
                    </select>

                </td>
                <td class="px-6 py-4 whitespace-nowrap">

                    <input type="number"
                        wire:model="{{ $data }}.{{ $index }}.total_capacity"
                        x-bind:disabled="$wire.{{ $data }}[{{ $index }}].entry_type === 'both' || $wire.{{ $data }}[{{ $index }}].entry_type === '1' || $wire.{{ $data }}[{{ $index }}].entry_type === '2'"
                        class="border border-gray-300 hover:border-blue-500 focus:border-cyan-500 focus:ring-cyan-500 outline-none text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 dark:hover:border-blue-400 dark:focus:border-green-400 dark:focus:ring-green-400 disabled:cursor-not-allowed"
                        placeholder="Total Capacity">
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <input type="number"
                        wire:model="{{ $data }}.{{ $index }}.normal_capacity"
                        x-bind:disabled="$wire.{{ $data }}[{{ $index }}].entry_type !== 'both' && $wire.{{ $data }}[{{ $index }}].entry_type !== '1'"
                        class="border border-gray-300 hover:border-blue-500 focus:border-cyan-500 focus:ring-cyan-500 outline-none text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 dark:hover:border-blue-400 dark:focus:border-green-400 dark:focus:ring-green-400 disabled:cursor-not-allowed"
                        placeholder="Normal Capacity">
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <input type="number"
                        wire:model="{{ $data }}.{{ $index }}.ds_capacity"
                        x-bind:disabled="$wire.{{ $data }}[{{ $index }}].entry_type !== 'both' && $wire.{{ $data }}[{{ $index }}].entry_type !== '2'"
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
                <td class="px-6 py-4 whitespace-nowrap">
                    <button
                        wire:click="{{ $saveMethod }}({{ $item->id }}, {{ $index }})"
                        @disabled($disabled)
                        class="px-3 py-1 text-sm rounded-md transition-colors
                            {{ $disabled 
                                ? 'bg-gray-300 text-gray-500 cursor-not-allowed' 
                                : 'bg-blue-600 hover:bg-blue-700 text-white' }}">
                        Save
                    </button>
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