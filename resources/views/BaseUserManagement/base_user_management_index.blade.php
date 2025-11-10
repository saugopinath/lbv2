<x-layouts.app>
    <div class="bg-white dark:bg-gray-800 shadow-md rounded p-4 space-y-4">

        <div class="flex justify-between items-center ">
            <h1 class="text-xl font-bold">Assigned User's Permissions Details</h1>
           
        </div>
    </div>
    <div class="bg-white dark:bg-gray-800 shadow-md rounded p-4 space-y-4">
        <livewire:user-permission-filter.filter-user-permission />
        <livewire:base-management-details-table />
        <livewire:user-permission.user-permission-edit-modal />
        <livewire:base-user-management.sync-users-with-baseuser />
        
    </div>
</x-layouts.app>