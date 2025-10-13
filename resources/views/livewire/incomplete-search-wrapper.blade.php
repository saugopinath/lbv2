<div class="space-y-6">
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl p-6">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Applicant Incomplete Details Search</h2>

        <livewire:filter-lgd-master :button_show="0" :wire:key="'filter-lgd'" />
        <livewire:incomplete-type :button_show="0" :wire:key="'incomplete-type'" />

        <div class="flex items-center gap-6">
            {{-- Is Revert Radio --}}
           @if($stage == 'verifier' || $stage == 'revert')
            <div class="flex items-center gap-4">
                <span class="text-gray-700 font-medium">Is Revert :</span>
                <div class="flex items-center gap-4">
                    <label class="inline-flex items-center">
                        <input type="radio" wire:model.live="revert" value="yes" class="form-radio text-blue-600">
                        <span class="ml-2 text-gray-700">Yes</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" wire:model.live="revert" value="no" class="form-radio text-blue-600">
                        <span class="ml-2 text-gray-700">No</span>
                    </label>
                </div>
            </div>
            @endif

            {{-- Search / Reset --}}
            <div class="flex gap-3">
                <x-button.primary wire:click="search"
                 {{--  x-on:click="
        Livewire.dispatch('showLoader');
        $wire.search();    "  --}}
                    class="bg-blue-500 text-white whitespace-nowrap cursor-pointer">Search</x-button.primary>
                <x-button.primary wire:click="resetAll"
                    class="bg-green-500 text-white whitespace-nowrap cursor-pointer">Reset</x-button.primary>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white shadow-xl rounded-2xl p-4 mt-4">
        <livewire:incomplet-type-table :stage="$stage" :wire:key="'table-'.$stage" />
    </div>
</div>
