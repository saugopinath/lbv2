<x-layouts.app>
    <!-- Page Header -->
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-4 mb-4">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-bold text-indigo-800 dark:text-white">
                {{ $header }}
            </h1>
            <x-form.back-button :url="route('master-tab')" />
        </div>
    </div>
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-4 mb-4">
        <livewire:master-tab-create />
    </div>
</x-layouts.app>