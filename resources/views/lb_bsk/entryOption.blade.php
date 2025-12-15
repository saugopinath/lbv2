{{--  <x-layouts.app1>
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl p-4">
                <livewire:dup-aadhaar-check />
                <livewire:entrytab />
            </div>
</x-layouts.app1>  --}}
<x-layouts.app1>
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl p-4">

        {{-- USER INFO BAR --}}
        <div class="mb-4 p-3 rounded-lg bg-gray-100 dark:bg-gray-700 text-sm">
            <span class="font-semibold text-gray-700 dark:text-gray-200">
                User ID:
            </span>
            <span class="mr-6 text-gray-900 dark:text-white">
                {{ $userId }}
            </span>

            <span class="font-semibold text-gray-700 dark:text-gray-200">
                Ticket No:
            </span>
            <span class="text-gray-900 dark:text-white">
                {{ $ticketNo }}
            </span>
        </div>

        <livewire:dup-aadhaar-check />
        <livewire:entrytab />

    </div>
</x-layouts.app1>
