<x-layouts.app>
<!-- <div class="max-w-full"> -->
    <div x-data="{ showForm: false }" 
     x-on:form-submitted.window="showForm = false" class="bg-white dark:bg-gray-800 shadow-md rounded space-y-4">
    <div class="flex justify-between items-center p-4">
    <h1 class="text-xl font-semibold text-gray-800 dark:text-gray-100 pr-4">
        {{ $header }}
    </h1>
    <button 
        @click="showForm = !showForm"
        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Insert 
    </button>
</div>

    <div x-show="showForm" x-cloak>
        <livewire:master-parameter-setting.index />
    </div>
</div>
    <div class="bg-white dark:bg-gray-800 shadow-md rounded p-4 space-y-4">
        <livewire:master-parameter-data-table />
    </div>

</x-layouts.app>