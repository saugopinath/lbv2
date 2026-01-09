<x-layouts.app>
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-4 space-y-4">
        <div class="flex justify-between items-center text-center">
            <h1 class="text-xl font-bold text-indigo-800 dark:text-white">{{$header}}</h1>
        </div>
    </div>
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-4 space-y-4">
        <form action="{{ route('beneficiary-reportlist') }}" method="POST" id="lgdForm">
            @csrf
            {{-- Livewire component renders inputs (no <form> inside) --}}
            <livewire:filter-lgd-master :button_show="0" />
            <div class="flex items-center justify-center gap-2 mt-2">
                <button type="submit" class="px-6 py-2 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105">
                    <i class="fas fa-search mr-2"></i>Search
                </button>
            </div>
        </form>
    </div>
    <x-dynamic-table-view
        :header="$header"
        :helper="$helper ?? []"
        :columns="$columns"
        :data="$data"
        :export-url="$exportUrl"
        filename="$filename"
        />

</x-layouts.app>