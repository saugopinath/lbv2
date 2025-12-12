<x-layouts.app>
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl p-4">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">
            Incomplete Details Mis Report
        </h2>

    </div>
    {{--  <livewire:incomplete-type :wire:key="'incomplete-type'" />

    <livewire:filter-lgd-master :button_show="1" :wire:key="'filter-lgd'" />  --}}

    <div class="bg-white shadow-xl rounded-2xl ">
        <div class="bg-white dark:bg-gray-800 shadow-md rounded p-4 space-y-4">


            <x-dynamic-table-view :header="$header" :helper="$helper ?? []" :columns="$columns" :data="$data"
                :export-url="$exportUrl" filename="$filename" />
        </div>
    </div>
</x-layouts.app>
