<x-layouts.app>
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl p-4">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">
            Applicant Incomplete Details Search
        </h2>
        <livewire:filter-lgd-master :button_show="1" />
    </div>
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl p-4">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">
            Incomplete Details Search
        </h2>
        <livewire:incomplete-type />
    </div>
    <div class="bg-white shadow-xl rounded-2xl ">

        <div>
            <livewire:incomplet-type-table />
        </div>
    </div>
</x-layouts.app>

