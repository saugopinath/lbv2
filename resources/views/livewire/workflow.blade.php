<div class="w-full space-y-6">
    @if (!$schemeData)
    <div class="max-w-3xl mx-auto bg-white border border-gray-200 rounded-xl shadow-sm p-6">
        <livewire:scheme-dropdown-new />
    </div>
    @endif
    @if ($schemeData)
    <livewire:define-workflow :scheme-id="$schemeId" :wire:key="'define-workflow-'.$schemeId" />
    @endif
</div>