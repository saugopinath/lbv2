<x-layouts.app>
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl p-4">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">
            Incomplete Details Mis Report
        </h2>

    </div>
    {{-- <livewire:incomplete-type :wire:key="'incomplete-type'" /> --}}



    <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-4 space-y-4">
        <form action="{{ route('incomplete-details-mis-report') }}" method="POST" id="lgdForm">
            @csrf

            <livewire:incomplete-type :button_show="0" />
            <livewire:filter-lgd-master :button_show="0" />

            <div class="flex items-center justify-center gap-2 mt-2">
                <button type="submit"
                    class="px-6 py-2 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105">
                    <i class="fas fa-search mr-2"></i>Search
                </button>
            </div>
        </form>
    </div>


    <div class="bg-white shadow-xl rounded-2xl ">
        <div class="bg-white dark:bg-gray-800 shadow-md rounded p-4 space-y-4">


            <x-dynamic-table-view :header="$header" :helper="$helper ?? []" :columns="$columns" :data="$data" />
        </div>
    </div>
</x-layouts.app>
