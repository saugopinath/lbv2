<x-layouts.app>
    <div class="bg-white dark:bg-gray-800 shadow-md rounded p-4 space-y-4">

        <div class="flex justify-between items-center ">
            <h1 class="text-xl font-bold">Assigned User's Permissions Details</h1>
             <x-button.primary href="{{ route('assign-users-permissions') }}">Assign New Permission</x-button.primary>

        </div>
    </div>
    <div class="bg-white dark:bg-gray-800 shadow-md rounded p-4 space-y-4">
        <livewire:user-permission-filter.filter-user-permission />
        <livewire:user-permission-details-table />
        <livewire:user-permission.user-permission-edit-modal />
        <livewire:user-permission.bulk-user-permission-modal />

    </div>
</x-layouts.app>
