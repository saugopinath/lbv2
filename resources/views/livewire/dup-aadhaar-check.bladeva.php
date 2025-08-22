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
            
            // Check 12-digit format
            if (!/^\d{12}$/.test(val)) {
                this.errorMessage = 'Please enter a valid 12-digit Aadhaar number.';
                return;
            }

            // Verhoeff checksum validation
            if(!verhoeffValidate(val)) {
                this.errorMessage = 'Invalid Aadhaar number cs';
                return;
            }

            // Send to Livewire
            $wire.aadhaar = val;
            let result = await $wire.checkDuplicate();

            if(result.status === 'error' || result.status === 'duplicate') {
                this.errorMessage = result.message;
                this.disableCheckBtn = false;
            } else if(result.status === 'success') {
                this.successMessage = result.message;
                this.disableCheckBtn = true;
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
            label="Aadhaar Number"
            placeholder="Enter Aadhaar Number"
            required
            x-model="aadhaar" x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0,12)" />
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

<!-- Include Verhoeff JS only on this page -->
@push('scripts')
<script src="{{ asset('js/adhar-verhoeff.js') }}"></script>
@endpush
