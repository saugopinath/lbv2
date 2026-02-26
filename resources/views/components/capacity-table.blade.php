@props([
    'items',           // Collection of items (schemes or locations)
    'type',            // 'scheme' or 'location'
    'data',            // The data array (schemes_data or locations_data)
    'saveMethod',      // Method name to call on save
    'titleField' => 'name', // Field to display as title
    'subtitleField' => 'id', // Field to display as subtitle
    'subtitlePrefix' => 'ID: ', // Prefix for subtitle
    'disabled' => false, // Whether save buttons are disabled
])

<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    {{ $type === 'scheme' ? 'Scheme' : ucfirst(str_replace('_', ' ', $location_level ?? 'Location')) }}
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Entry Type</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Capacity</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Normal Capacity</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DS Capacity</th>
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
                        class="text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
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
                        class="w-32 text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 disabled:bg-gray-100 disabled:cursor-not-allowed"
                        placeholder="Total">
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <input type="number"
                        wire:model="{{ $data }}.{{ $index }}.normal_capacity"
                        x-bind:disabled="$wire.{{ $data }}[{{ $index }}].entry_type !== 'both' && $wire.{{ $data }}[{{ $index }}].entry_type !== '1'"
                        class="w-32 text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 disabled:bg-gray-100 disabled:cursor-not-allowed"
                        placeholder="Normal">
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <input type="number"
                        wire:model="{{ $data }}.{{ $index }}.ds_capacity"
                        x-bind:disabled="$wire.{{ $data }}[{{ $index }}].entry_type !== 'both' && $wire.{{ $data }}[{{ $index }}].entry_type !== '2'"
                        class="w-32 text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 disabled:bg-gray-100 disabled:cursor-not-allowed"
                        placeholder="DS">
                </td>
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