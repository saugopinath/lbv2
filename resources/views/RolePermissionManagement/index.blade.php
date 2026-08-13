<x-layouts.app>
    <div class="bg-white dark:bg-gray-800 shadow-md rounded p-4 space-y-4">

        <div class="flex justify-between items-center ">
            <h1 class="text-xl font-bold">Assigned Role Permissions Details</h1>
             <button
                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 cursor-pointer"
                x-data
                @click="$dispatch('open-modal')">
                Create Role
            </button>
           
        </div>
    </div>
    <div class="bg-white dark:bg-gray-800 shadow-md rounded p-4 space-y-4">
        <livewire:role-permission-management-details-table />
        <livewire:role-permission-management.role-permission-management-edit-modal/>
        <livewire:role-permission-management.role-create-modal />
        
    </div>
</x-layouts.app>
