<x-layouts.app>
    <div class="bg-white dark:bg-gray-800 shadow-md rounded p-4 space-y-4">

        <livewire:filter-lgd-master :button_show="$button_show" />
    </div>

    <div class="bg-white dark:bg-gray-800 shadow-md rounded p-4 space-y-4">



        <livewire:application-process-details-data-table />

        <livewire:revert-reject-modal />
    </div>
</x-layouts.app>