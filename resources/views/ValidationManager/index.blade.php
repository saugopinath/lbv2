<x-layouts.app>
    <div class="bg-white dark:bg-gray-800 shadow-md rounded p-4 space-y-4">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
                {{ $header ?? 'Manage Defult Field Validations' }}
            </h1>
            @if(isset($schemeName) && isset($tabName))
            <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Scheme:</span>
                    <span class="font-medium text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30 px-3 py-1 rounded-md">
                        {{ $schemeName }}
                    </span>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Tab:</span>
                    <span class="font-medium text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/30 px-3 py-1 rounded-md">
                        {{ $tabName }}
                    </span>
                </div>
            </div>
            <x-form.back-button :url="url()->previous()" />

            @endif
        </div>
        
    </div>
    <div class="bg-white dark:bg-gray-800 shadow-md rounded p-4 space-y-4">
        <livewire:scheme-field-validation-table
            :scheme-id="$schemeId"
            :tab-code="$tabCode" />
    </div>
</x-layouts.app>