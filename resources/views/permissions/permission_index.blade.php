<x-layouts.app>
    <div class="bg-white dark:bg-gray-800 shadow-md rounded p-4 space-y-4">

        <div class="flex justify-between items-center mb-4">
            <h1 class="text-xl font-bold">Permissions Details</h1>
            <button
                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 cursor-pointer"
                x-data
                @click="$dispatch('open-modal')">
                Create Permission
            </button>
        </div>
    </div>
    <div class="bg-white dark:bg-gray-800 shadow-md rounded p-4 space-y-4">
        <livewire:permission-list-table-new />
    </div>

    <!-- Modal -->
     <livewire:permission.create-permission-form />


</x-layouts.app>
