<x-layouts.app>
    
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-4 space-y-4">
        <div class="flex justify-between items-center text-center">
            <h1 class="text-xl font-bold text-indigo-800 dark:text-white">{{$header}}</h1>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-4 space-y-4">
        <livewire:caste-modification-filters wire:key="filters-component" />
    </div>

    <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-4 space-y-4">
        <livewire:caste-modification-list-table
            :applicantStatus="$applicantStatus ?? ''"
            :casteId="$casteId ?? ''"
            :key="($applicantStatus ?? '').'-'.($casteId ?? '')" />
    </div>
</x-layouts.app>
