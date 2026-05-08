<div>
    @if($schemeId)
        @livewire('incomplete-search-wrapper', ['stage' => $stage ?? null])
        
    @endif
     @push('scripts')
        <script src="{{ asset('js/master-data/master-data-v2.js') }}"></script>
    @endpush
</div>