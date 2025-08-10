<div
    x-data="{
        errorMessage: '',
        async validateAndSubmit() {
            this.errorMessage = '';
            let val = $wire.aadhaar?.replace(/\s+/g, '');
            if (!/^\d{12}$/.test(val)) {
                this.errorMessage = 'Please enter a valid 12-digit Aadhaar number.';
                return;
            }
            await $wire.checkDuplicate();
        }
    }"
    class="grid gap-6 md:grid-cols-2 mb-6 p-4 border-b border-gray-200 dark:border-gray-700">
    <div>
        <x-form.input
            id="check_aadhar"
            name="aadhar_number"
            label="Aadhar Number"
            placeholder="Enter Aadhar Number"
            required
            wire:model.defer="aadhaar" />
    </div>
    <div class="flex items-end">
        <x-button.gradient-button
            type="button"
            @click="validateAndSubmit()"
            wire:loading.attr="disabled">
            <span wire:loading.remove>Check Availability</span>
            <span wire:loading>Checking…</span>
        </x-button.gradient-button>
    </div>
    <template x-if="errorMessage">
        <div class="mt-2 text-red-600 text-sm" x-text="errorMessage"></div>
    </template>
    @if ($error)
    <div class="mt-2 text-red-600 text-sm">{{ $error }}</div>
    @elseif ($valid)
    <div class="mt-2 text-green-600 text-sm">✅ Aadhaar is valid and not duplicate.</div>
    @endif
</div>