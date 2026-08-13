<div class="w-full space-y-6">
    @if (!$schemeData)
        <div class="max-w-3xl mx-auto bg-white border border-gray-200 rounded-xl shadow-sm p-6">
            <livewire:scheme-dropdown-new :isFinal="true" :isAssigned="true" />
        </div>
    @endif
    @if ($schemeData)
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl p-4 space-y-2 flex items-center justify-between">
            <h1 class="text-xl font-bold text-indigo-800 dark:text-white mt-2 pl-4">
                Process Appliaction {{ $schemeName }}
            </h1>
            <x-form.back-button :url="route('application-lists')" />
        </div>
        <div class="bg-white dark:bg-gray-800 shadow-md rounded p-4 space-y-4">
            <livewire:filter-lgd-master :button_show="1" />
        </div>
        <div class="bg-white dark:bg-gray-800 shadow-md rounded p-4 space-y-4">
            <livewire:application-process-details-data-table :scheme-id="$schemeId"
                :wire:key="'application-lists-'.$schemeId" />
            <livewire:revert-reject-modal />
        </div>
    @endif
</div>