<x-layouts.app>
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-2 space-y-4">
        <div class="flex justify-between items-center text-center">
            <h1 class="text-xl font-bold text-indigo-800 dark:text-white">{{$header}}</h1>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-2 space-y-4">
        <livewire:caste-modification-filters wire:key="filters-component" />
    </div>

    {{-- Table --}}
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-2 space-y-4">
        {{-- Re-mount table on filter change using key --}}
        <livewire:caste-modification-list-table 
            :applicantStatus="$applicantStatus ?? ''" 
            :casteId="$casteId ?? ''" 
            :key="($applicantStatus ?? '').'-'.($casteId ?? '')" />
    </div>
</x-layouts.app>
