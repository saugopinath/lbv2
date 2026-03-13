<div class="w-full space-y-6">
    @if (!$schemeData)
        <livewire:scheme-dropdown-new />
    @endif
    @if ($schemeData)
        <livewire:define-workflow :scheme-id="$schemeId" :wire:key="'define-workflow-'.$schemeId" />
    @endif
</div>