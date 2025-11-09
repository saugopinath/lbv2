<x-layouts.app>
    <div class="bg-white dark:bg-gray-800 shadow-md rounded p-4 space-y-4">

        <div class="flex justify-between items-center ">
            <h1 class="text-xl font-bold">Assigned User's Permissions Details</h1>
<<<<<<< HEAD
            <!-- <x-button.primary href="{{ route('assign-users-permissions') }}">Assign New Permission</x-button.primary> -->
=======
             {{--  <x-button.primary href="{{ route('assign-users-permissions') }}">Assign New Permission</x-button.primary>  --}}
>>>>>>> d726694e2ff4cbf8a12d9642a72f953c3c34c7b5

        </div>
    </div>
    <div class="bg-white dark:bg-gray-800 shadow-md rounded p-4 space-y-4">
        <livewire:user-permission-filter.filter-user-permission />
        <livewire:user-permission-details-table />
        <livewire:user-permission.user-permission-edit-modal />
        <livewire:user-permission.bulk-user-permission-modal />
<<<<<<< HEAD
        
    </div>
</x-layouts.app>
=======

    </div>
</x-layouts.app>
>>>>>>> d726694e2ff4cbf8a12d9642a72f953c3c34c7b5
