<div class="w-full space-y-6">
    @if ($showSchemeDropdown && !$schemeData)
    <div class="max-w-3xl mx-auto bg-white border border-gray-200 rounded-xl shadow-sm p-6">
        <livewire:scheme-dropdown-new :isFinal="true" />
    </div>
    @endif
    @if ($schemeData)
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-4 space-y-4">
        <div class="flex justify-between items-center text-center">
            <h1 class="text-xl font-bold text-indigo-800 dark:text-white">{{$header}}</h1>
        </div>
    </div>
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-4 space-y-4">
        <livewire:dynamic-workflow.request-workflow-table :scheme-id="$schemeId" :moduleCode="$moduleCode" :wire:key="'request-workflow-table-'.$schemeId.'-'.$moduleCode" />
    </div>
    <!-- Modal Container -->
    <livewire:dynamic-workflow.process-workflow-modal />
    @endif

</div>