<x-layouts.app>
    <div class="max-w-auto mx-auto bg-white rounded-xl shadow-sm p-6">
    <livewire:dynamic-form :applicationId="$app_id" :beneficiaryId="$ben_id" :schemeId="20" :schemeName="'LB'" />
    </div>
    @push('scripts')
        <script src="{{ asset('js/master-data/master-data-v2.js') }}"></script>
    @endpush
</x-layouts.app>