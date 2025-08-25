<div
    x-data="{
        aadhaar: '',
        errorMessage: '',
        successMessage: '',
        disableCheckBtn: false,

        async validateAndSubmit() {
            this.errorMessage = '';
            this.successMessage = '';

            let val = this.aadhaar.replace(/\s+/g, '');
            if (!/^\d{12}$/.test(val)) {
                this.errorMessage = 'Please enter a valid 12-digit Aadhaar number.';
                return;
            }

            $wire.aadhaar = val; // send clean value to Livewire
            let result = await $wire.checkDuplicate();

            if (result.status === 'error' || result.status === 'duplicate') {
                this.errorMessage = result.message;
                this.disableCheckBtn = false; // allow retry
            } else if (result.status === 'success') {
                this.successMessage = result.message;
                this.disableCheckBtn = true; // disable after success
            }
        }
    }"
    x-init="$watch('aadhaar', value => { disableCheckBtn = false; })"
    class="grid gap-6 md:grid-cols-3 mb-6 p-4 border-b border-gray-200 dark:border-gray-700">

    <!-- Aadhaar Input -->
    <div>
        <x-form.input
            id="check_aadhar"
            name="aadhar_number"
            label="Aadhar Number"
            placeholder="Enter Aadhar Number"
            required
            x-model="aadhaar" />
    </div>

    <!-- Button -->
    <div class="flex items-end">
        <x-button.gradient-button
            type="button"
            @click="validateAndSubmit()"
            wire:loading.attr="disabled"
            wire:target="checkDuplicate"
            x-bind:disabled="disableCheckBtn">
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
</div>
