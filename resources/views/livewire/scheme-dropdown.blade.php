<div class="max-w-3xl mx-auto bg-white border border-gray-200 rounded-xl shadow-sm p-6 space-y-6">


    <x-form.select name="scheme_id" label="Scheme" wire:model.live="schemeId" class="border rounded px-3 py-2 w-full">
        <option value="">-- Select Scheme --</option>

        @foreach($schemes as $scheme)
            <option value="{{ $scheme->id }}">
                {{ $scheme->name }}
            </option>
        @endforeach
    </x-form.select>

    @if($schemeId)
        <livewire:dynamic-form :scheme-id="$schemeId" :wire:key="'dynamic-form-'.$schemeId" />
    @endif


</div>

@push('scripts')
    <script src="{{ asset('js/master-data/master-data-v2.js') }}"></script>
@endpush