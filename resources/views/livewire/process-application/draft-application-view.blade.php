<div>
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-4 mb-4 flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <h1 class="text-2xl font-bold text-indigo-800 dark:text-white px-2 py-2">
                Process Application
            </h1>
            <span class="px-4 py-1.5 rounded-full text-sm font-semibold 
             bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400 
             border border-blue-200 dark:border-blue-800 shadow-sm
             flex items-center gap-2">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"
                        clip-rule="evenodd" />
                </svg>
                {{ $schemeName }}
            </span>
            <span class="px-4 py-1.5 rounded-full text-sm font-semibold 
             bg-gray-50 text-gray-700 dark:bg-gray-800 dark:text-gray-400 
             border border-gray-200 dark:border-gray-700 shadow-sm
             flex items-center gap-2">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                        clip-rule="evenodd" />
                </svg>
                Application ID: {{ $applicationId }}
            </span>
            
        </div>
        <x-form.back-button :url="route('lb-application-list')" />
    </div>
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-8 space-y-4">
        <livewire:application-view :id="$applicationId" :schemeId="$schemeId" />
        <div class="flex justify-center space-x-2">
            <x-button.primary wire:click="openActionModal" wire-target="openActionModal"
                x-on:click="Livewire.dispatch('showLoader')">
                Action
            </x-button.primary>
            @livewire('process-application.bulk-action-modal')

        </div>
    </div>
</div>