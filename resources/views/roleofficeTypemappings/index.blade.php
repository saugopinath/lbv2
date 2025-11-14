<x-layouts.app>

    <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl p-4">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-700">
                Role Office Type Mappings
            </h2>
            @can('ccreate role mappings')
                <a href="{{ route('role-office-type-mappings.create') }}"
                    class="bg-blue-500 text-white px-4 py-2 rounded-2xl shadow-md hover:bg-blue-600 whitespace-nowrap cursor-pointer">
                    New role office type mapping
                </a>
            @endcan
        </div>
    </div>
    <div class="bg-white shadow-xl rounded-2xl">
        <div>
            <livewire:role-office-type-mappings-table />
        </div>
    </div>
</x-layouts.app>
