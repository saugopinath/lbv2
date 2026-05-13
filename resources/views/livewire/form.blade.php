<div class="w-full space-y-6">
    @if ($showSchemeDropdown && !$schemeData)
        <livewire:scheme-dropdown-new :isFinal="true" />
    @endif
    @if ($schemeData)
        <div class="max-w-auto mx-auto bg-white rounded-xl shadow-sm p-6">
            <livewire:dynamic-form :scheme-id="$schemeId" :schemeName="$schemeName"  :grievanceId="$grievanceId" :wire:key="'dynamic-form-'.$schemeId" />
        </div>
    @endif
    @push('scripts')
        <script src="{{ asset('js/master-data/master-data-v2.js') }}"></script>
        <script src="{{ asset('js/aadhaar-verhoeff.js') }}"></script>
    @endpush
</div>
