<div class="w-full space-y-6">
    @if (!$schemeData)
        <livewire:scheme-dropdown-new />
    @endif
    @if ($schemeData)
        <div class="bg-white dark:bg-gray-800 shadow-md rounded p-4 space-y-4">

            <div class="flex justify-between items-center ">
                <h1 class="text-xl font-bold">Assigned User's Permissions Details</h1>
                {{--  <x-button.primary href="{{ route('assign-users-permissions') }}">Assign New Permission</x-button.primary>  --}}

            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 shadow-md rounded p-4 space-y-4">
            <livewire:user-permission-filter.filter-user-permission />
            <livewire:user-permission-details-table :schemeId="$schemeId"/>
            <livewire:user-permission.user-permission-edit-modal :schemeId="$schemeId"/>
            <livewire:user-permission.bulk-user-permission-modal :schemeId="$schemeId"/>

        </div>
    @endif
</div>
