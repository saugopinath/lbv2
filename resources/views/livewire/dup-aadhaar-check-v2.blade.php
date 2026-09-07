<div x-data="{
    aadhaar: '',
    errorMessage: '',
    successMessage: '',
    disableCheckBtn: false,
    dsMark: false,
    dsData: false,
    async validateAndSubmit() {
        this.errorMessage = '';
        this.successMessage = '';

        let val = this.aadhaar.replace(/\s+/g, '');

        // Check 12-digit format
        if (!/^\d{12}$/.test(val)) {
            this.errorMessage = 'Please enter a valid 12-digit Aadhaar number.';
            return;
        }

        // Verhoeff checksum validation
        {{-- Commented for Dev --}}
        {{-- if (!verhoeffValidate(val)) {
            this.errorMessage = 'Invalid Aadhaar number';
            return;
        } --}}
        Livewire.dispatch('showLoader');
        // Send to Livewire
        $wire.aadhaar = val;
        let result = await $wire.checkDuplicate();

        if (result.status === 'error' || result.status === 'duplicate') {
            this.errorMessage = result.message;
            this.disableCheckBtn = false;
        } else if (result.status === 'success') {
            this.successMessage = result.message;
            this.disableCheckBtn = true;
        }
        if (result.status === 'duplicate') {
            this.disableCheckBtn = true;
            this.dsMark = result.ds_entry;
        }
    },
    async DsMark() {
        let val = this.aadhaar.replace(/\s+/g, '');
        $wire.aadhaar = val;
        let result = await $wire.DsMark();
        this.dsData = result.status;
    }
}">
    <div class="grid gap-6 md:grid-cols-3 mb-6 p-4 border-b border-gray-200 dark:border-gray-700">

        <!-- Aadhaar Input -->
        <div>
            <x-form.input id="check_aadhar" label="Aadhaar Number" name="aadhar_number" placeholder="Enter Aadhaar Number" required x-on:input="
        let clean = $event.target.value.replace(/[^0-9]/g, '').slice(0, 12);
        $event.target.value = clean;
        aadhaar = clean;

        // If the button was previously disabled (meaning a check was completed),
        // notify Livewire ONCE that the verified state is now reset.
        if (disableCheckBtn) {
            Livewire.dispatch('aadhaarCheckedReset');
        }

        // Local Alpine resets
        errorMessage = '';
        successMessage = '';
        disableCheckBtn = false;
        dsMark = false;
        dsData = false;
                " />
        </div>

        <!-- Button -->
        <div class="flex items-end">
            <x-button.gradient-button @click="validateAndSubmit()" type="button" wire:loading.attr="disabled" wire:target="checkDuplicate" x-bind:disabled="disableCheckBtn">
                <span wire:loading.remove wire:target="checkDuplicate">Check Availability V2</span>
                <span wire:loading wire:target="checkDuplicate">Checking…</span>
            </x-button.gradient-button>
        </div>

        <!-- Error -->
        <template x-if="errorMessage">
            <div class="mt-8 text-red-600 text-sm" x-text="errorMessage"></div>
        </template>

        <!-- Success -->
        <template x-if="successMessage">
            <div class="mt-8 text-green-600 text-sm" x-text="successMessage"></div>
        </template>
        <template x-if="dsMark">
            <x-button.gradient-button @click="DsMark()" type="button">
                <span>Ds Mark</span>
            </x-button.gradient-button>
        </template>
    </div>
    @if ($showDsTable)
        <div x-cloak x-show="dsData" x-transition>
            <livewire:duplicate-applicant-data-table />
            <livewire:duplicate-applicant-d-s-mark-modal />
        </div>
    @endif
</div>
