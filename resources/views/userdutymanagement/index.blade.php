{{--  <x-layouts.app>
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl p-4">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">
            User Management
        </h2>
        <livewire:role-mapping-level  />
        <livewire:filter-lgd-master  />
    </div>
    <div class="bg-white shadow-xl rounded-2xl ">
        <h2 class="text-xl font-semibold text-gray-700 mb-4 p-4">
             List of Users
        </h2>
        <div>
           <livewire:user-duty-management />
        </div>
    </div>
</x-layouts.app>  --}}
<x-layouts.app>
    <livewire:user-management-container />
</x-layouts.app>
