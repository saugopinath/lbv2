<div class="w-full space-y-6">
    @if (!$schemeData)
        <livewire:scheme-dropdown-new />
    @endif
    @if ($schemeData)
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl p-4 space-y-2 flex items-center justify-between">
            <h1 class="text-xl font-bold text-indigo-800 dark:text-white mt-2 pl-4">
                Assigned Role Permissions Details ({{ $schemeName }})
            </h1>
            <button
                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 cursor-pointer"
                x-data
                @click="$dispatch('open-modal')">
                Create Role
            </button>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-md rounded p-4 space-y-4">
            <livewire:role-permission-management-details-table :schemeId="$schemeId"/>
            <livewire:role-permission-management.role-permission-management-edit-modal :schemeId="$schemeId"/>
            <livewire:role-permission-management.role-create-modal :schemeId="$schemeId"/>
        </div>
    @endif
</div>
