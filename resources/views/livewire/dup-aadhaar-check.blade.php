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
        if (!verhoeffValidate(val)) {
            this.errorMessage = 'Invalid Aadhaar number cs';
            return;
        }
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
}" x-init="$watch('aadhaar', value => {
    errorMessage = '';
    successMessage = '';
    disableCheckBtn = false;
    dsMark = false;
    dsData = false;
    Livewire.dispatch('aadhaarCheckedReset');
})">
    <div class="grid gap-6 md:grid-cols-3 mb-6 p-4 border-b border-gray-200 dark:border-gray-700">

        <!-- Aadhaar Input -->
        <div>
            <x-form.input id="check_aadhaar" name="aadhaar_number" label="Aadhaar Number" placeholder="Enter Aadhaar Number"
                required x-model="aadhaar"
                x-on:input="aadhaar = $el.value.replace(/[^0-9]/g, '').slice(0,12);
        $el.value = aadhaar;" />
        </div>

        <!-- Button -->
        <div class="flex items-end">
            <x-button.gradient-button type="button" @click="validateAndSubmit()" wire:loading.attr="disabled"
                wire:target="checkDuplicate" x-bind:disabled="disableCheckBtn">
                <span wire:loading.remove wire:target="checkDuplicate">Check Availability</span>
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
            <x-button.gradient-button type="button" @click="DsMark()">
                <span>Ds Mark</span>
            </x-button.gradient-button>
        </template>
    </div>
    @if ($showDsTable)
        <div x-show="dsData" x-transition x-cloak>
            <livewire:duplicate-applicant-data-table />
            <livewire:duplicate-applicant-d-s-mark-modal />
        </div>
    @endif
</div>
