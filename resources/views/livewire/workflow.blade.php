<div class="w-full space-y-6">
    <livewire:new-scheme-modal />
    @if (!$schemeData)
        <div class="max-w-5xl mx-auto bg-white border border-gray-200 rounded-xl shadow-sm p-6">
            <div class="flex items-center gap-2 mb-2 ml-4 border-b border-gray-200">
                <span class="text-md font-semibold text-gray-700 uppercase tracking-wide text-blue-600">Configure the Workflow</span>
            </div>
            <livewire:scheme-dropdown-new />
        </div>
    @endif
    @if ($schemeData)
        <div class="max-w-5xl mx-auto bg-white border border-gray-200 rounded-xl shadow-sm p-6">
            <livewire:define-workflow :scheme-id="$schemeId" :wire:key="'define-workflow-'.$schemeId" />
        </div>
    @endif
</div>
