<div
    x-data="{
        errorMessage: '',
        async validateAndSubmit() {
            this.errorMessage = '';
            const val = $wire.aadhaar;
            if (!/^\d{12}$/.test(val)) {
                this.errorMessage = 'Please enter a valid 12‑digit Aadhaar number cs.';
                return;
            }
            await $wire.checkDuplicate();
        }
    }"
>
    <x-form.input
        name="aadhaar"
        label="Enter Aadhaar"
        x-model.defer="$wire.aadhaar"
        required
    />
    <x-button.danger
        @click="validateAndSubmit()"
        wire:loading.attr="disabled"
        class="mt-4"
    >
        <span wire:loading.remove>Check Aadhaar</span>
        <span wire:loading>Checking…</span>
    </x-button.danger>
    <template x-if="errorMessage">
        <div class="mt-2 text-red-600 text-sm" x-text="errorMessage"></div>
    </template>
    @if ($error)
        <div class="mt-2 text-red-600 text-sm">{{ $error }}</div>
    @elseif ($valid)
        <div class="mt-2 text-green-600 text-sm">✅ Aadhaar is valid and not duplicate.</div>
    @endif
</div>