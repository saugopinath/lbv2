<div class="w-full space-y-6">

    {{-- ================= SCHEME SELECT (SHOW ONLY IF NOT SELECTED) ================= --}}
    @if (!$schemeId)
        <div class="max-w-3xl mx-auto bg-white border border-gray-200 rounded-xl shadow-sm p-6">

            <x-form.select name="scheme_id" label="Scheme" wire:model.live="schemeId"
                class="border rounded px-3 py-2 w-full" required>

                <option value="">-- Select Scheme --</option>

                @foreach ($schemes as $scheme)
                    <option value="{{ $scheme->id }}">
                        {{ $scheme->name }}
                    </option>
                @endforeach

            </x-form.select>

        </div>
    @endif

    {{-- ================= DYNAMIC FORM (FULL WIDTH) ================= --}}
    @if ($schemeId)
        @if ($option == 1)
            <div class="max-w-auto mx-auto bg-white rounded-xl shadow-sm p-6">
                <livewire:dynamic-form :scheme-id="$schemeId" :schemeName="$schemeName" :wire:key="'dynamic-form-'.$schemeId" />

            </div>
        @elseif($option == 2)
            <livewire:age-management :scheme-id="$schemeId" :wire:key="'age-management-'.$schemeId" />
        @elseif($option == 3)
            <div class="bg-white dark:bg-gray-800 shadow-md rounded p-4 space-y-4">
                <livewire:filter-lgd-master :button_show="$button_show" />
            </div>
            <div class="bg-white dark:bg-gray-800 shadow-md rounded p-4 space-y-4">

                <livewire:application-process-details-data-table :scheme-id="$schemeId"
                    :wire:key="'lb-application-list-'.$schemeId" />
                <livewire:revert-reject-modal />
            </div>
        @elseif($option == 4)

            <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl p-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-gray-700">
                        Role Office Type Mappings
                    </h2>

                    <a href="{{ route('role-office-type-mappings.create', [
                    'scheme_id' => Crypt::encryptString($schemeId)
                ]) }}"
                        class="bg-blue-500 text-white px-4 py-2 rounded-2xl shadow-md hover:bg-blue-600 whitespace-nowrap cursor-pointer">
                        New role office type mapping
                    </a>

                </div>
            </div>
            <div class="bg-white shadow-xl rounded-2xl">
                <div>
                    <livewire:role-office-type-mappings-table />
                </div>
            </div>
        @elseif($option == 5)
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl p-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-gray-700">
                        OfficeMasters
                    </h2>

                    <a href="{{ route('office-masters.create', [
                    'scheme_id' => Crypt::encryptString($schemeId)
                ]) }}"
                        class="bg-blue-500 text-white px-4 py-2 rounded-2xl shadow-md hover:bg-blue-600 whitespace-nowrap cursor-pointer">
                        New OfficeMaster
                    </a>

                </div>
            </div>
            <div class="bg-white shadow-xl rounded-2xl ">
                <div>
                    <livewire:office-masters />
                </div>
            </div>
        @elseif($option == 6)
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl p-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-gray-700">
                        Users
                    </h2>

                    <a href="{{ route('users.create', [
                    'scheme_id' => Crypt::encryptString($schemeId)
                ]) }}"
                        class="bg-blue-500 text-white px-4 py-2 rounded-2xl shadow-md hover:bg-blue-600 whitespace-nowrap cursor-pointer">
                        New Users
                    </a>

                </div>
            </div>
            <livewire:user-permission-filter.filter-user-permission />
            <div class="bg-white shadow-xl rounded-2xl ">
                <div>
                    <livewire:Users />
                </div>
            </div>
        @else
            <livewire:dup-check-scheme-config-settings :scheme-id="$schemeId" :wire:key="'duplicate-'.$schemeId" />
        @endif
    @endif

</div>
@if ($option == 1)
    @push('scripts')
        <script src="{{ asset('js/master-data/master-data-v2.js') }}"></script>
        <script src="{{ asset('js/adhar-verhoeff.js') }}"></script>
    @endpush
@endif