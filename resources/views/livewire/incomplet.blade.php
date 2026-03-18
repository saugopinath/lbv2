<div class="w-full space-y-6">
    @if (!$schemeData)
        <livewire:scheme-dropdown-new />
    @endif
    @if ($schemeData)
        <livewire:incomplete-search-wrapper :schemeId="$schemeId" :stage="$stage ?? null"
            :wire:key="'incomplete.types-'.$schemeId" />
    @endif
</div>
