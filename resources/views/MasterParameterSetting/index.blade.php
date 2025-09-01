<x-layouts.app>
<!-- <div class="max-w-full"> -->
    <div class="bg-white dark:bg-gray-800 shadow-md rounded p-4 space-y-4">
    <h1>{{ $header }}</h1>

    <livewire:master-parameter-setting.index/>

    </div>
    <div class="bg-white dark:bg-gray-800 shadow-md rounded p-4 space-y-4">
        <livewire:master-parameter-data-table />
    </div>

</x-layouts.app>