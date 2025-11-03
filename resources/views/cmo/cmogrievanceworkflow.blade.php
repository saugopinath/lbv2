<x-layouts.app>
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-2 space-y-4">
        <div class="flex justify-between items-center text-center">
            <h1 class="text-xl font-bold text-indigo-800 dark:text-white">{{$header}}</h1>
        </div>
    </div>
    <livewire:cmo-grievance-workflow-dropdown />
    <div class="bg-white shadow-xl rounded-2xl ">
        <h2 class="text-xl font-semibold text-gray-700 mb-4 p-4">
            Grievance List
        </h2>
        <div class="bg-white dark:bg-gray-800 shadow-md rounded p-4 space-y-4">
            <livewire:cmo-work-flow-data-table />
        </div>
    </div>
</x-layouts.app>