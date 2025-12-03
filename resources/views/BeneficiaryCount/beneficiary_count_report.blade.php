<x-layouts.app>
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-4 space-y-4">
        <div class="flex justify-between items-center text-center">
            <h1 class="text-xl font-bold text-indigo-800 dark:text-white">{{$header}}</h1>
        </div>
    </div>
    <livewire:filter-lgd-master :button_show="1"/>

    <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-4 space-y-4">
        <livewire:application-mis-report-lgd/>
    </div>

</x-layouts.app>
