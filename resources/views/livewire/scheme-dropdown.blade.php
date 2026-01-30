<div class="w-full space-y-6">

    {{-- ================= SCHEME SELECT (SHOW ONLY IF NOT SELECTED) ================= --}}
    @if(!$schemeId)
        <div class="max-w-3xl mx-auto bg-white border border-gray-200 rounded-xl shadow-sm p-6">

            <x-form.select
                name="scheme_id"
                label="Scheme"
                wire:model.live="schemeId"
                class="border rounded px-3 py-2 w-full">

                <option value="">-- Select Scheme --</option>

                @foreach($schemes as $scheme)
                    <option value="{{ $scheme->id }}">
                        {{ $scheme->name }}
                    </option>
                @endforeach

            </x-form.select>

        </div>
    @endif

    {{-- ================= DYNAMIC FORM (FULL WIDTH) ================= --}}
    @if($schemeId)
        <div class="max-w-auto mx-auto bg-white rounded-xl shadow-sm p-6">

            <livewire:dynamic-form
                :scheme-id="$schemeId"
                :wire:key="'dynamic-form-'.$schemeId"
            />

        </div>
    @endif

</div>

@push('scripts')
    <script src="{{ asset('js/master-data/master-data-v2.js') }}"></script>
@endpush
