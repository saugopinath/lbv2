<div class="w-full space-y-6">
    @if (!$schemeData)
        <livewire:new-scheme-modal />
        <div class="max-w-5xl mx-auto bg-white border border-gray-200 rounded-xl shadow-sm p-6">
            <div class="flex items-center gap-2 mb-2 ml-4 border-b border-gray-200">
                <span class="text-md font-semibold text-gray-700 uppercase tracking-wide text-blue-600">Configure the Workflow</span>
            </div>
            <livewire:scheme-dropdown-new />
        </div>
    @endif
    @if ($schemeData && !$moduleData)
        <div class="max-w-5xl mx-auto bg-white border border-gray-200 rounded-xl shadow-sm p-6">
            <livewire:module-selection :scheme-data="$schemeData" :wire:key="'module-selection-'.$schemeId" />
        </div>
    @endif
    @if ($schemeData && $moduleData)
        <div class="max-w-5xl mx-auto bg-white border border-gray-200 rounded-xl shadow-sm p-6">
            <livewire:define-workflow :module-data="$moduleData" :scheme-data="$schemeData" :wire:key="'define-workflow-'.$schemeId.'-'.$moduleId" />
        </div>
    @endif
</div>
