<div>
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl p-4 mb-6">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">User Management</h2>


        <x-button.primary wire:click="toggleForm"
            class="whitespace-nowrap cursor-pointer {{ $showAddUserForm ? 'bg-red-500' : 'bg-blue-500' }}">
            {{ $showAddUserForm ? 'Close Add User and Assign Role' : 'Add User and Assign Role' }}
        </x-button.primary>


        @if ($showAddUserForm)
            <livewire:add-user-form />
        @endif

        <livewire:role-mapping-level />
        <livewire:filter-lgd-master />
    </div>

    <div class="bg-white shadow-xl rounded-2xl p-4">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">List of Users</h2>
        <livewire:user-duty-management />
    </div>
</div>
