<div>
    <form wire:submit.prevent="save" x-data="{ passbookName: @entangle('passbook_name'), error: '' }">
        <div class="grid gap-6 md:grid-cols-2 mb-2 pl-4 pr-4">
            <div>
<x-form.input
    id="passbook_name"
    name="passbook_name"
    label="Name as in Bank Passbook"
    placeholder="Enter Name as in Bank Passbook"
    x-model="passbookName"
    x-on:input="
        $el.value = $el.value.replace(/[^A-Za-z\s]/g, '');
        error = '';
        $wire.set('score', null);
    "
    x-on:keydown="
        if ($event.key === 'Backspace' || $event.key === 'Delete') {
            $wire.set('score', null);
        }
    "
    required />
            </div>
            <div class="flex items-center space-x-3 mt-6">
                <x-button.gradient-button
                    type="button"
                    x-on:click="
                        if (!passbookName || passbookName.trim() === '') {
                            error = 'Applicant Name is required.';
                        } else {
                            error = '';
                            $wire.checkScore();
                        }
                    "
                    wire:loading.attr="disabled"
                    wire:target="checkScore">
                    <span wire:loading.remove wire:target="checkScore">Check Score</span>
                    <span wire:loading wire:target="checkScore">Checking…</span>
                </x-button.gradient-button>
                <template x-if="error">
                    <span class="text-red-500 text-sm" x-text="error"></span>
                </template>
                @if ($score !== null)
                <span wire:model="score"
                    class="font-semibold text-sm {{$scoreColor}} ">
                    Matching Score: {{ $score }}%
                </span>
                @endif
            </div>
        </div>
        <div class="grid gap-6 mb-4 md:grid-cols-2 pl-4 pr-4">
            <div>
                <x-form.input
                    name="ifscode"
                    label="IFS Code"
                    required
                    wire:model.lazy="ifscode"
                    x-on:input="if ($el.value.length > 11) $el.value = $el.value.slice(0, 11)" />
            </div>
            <div class="relative">
                <x-form.input
                    name="bankname"
                    label="Bank Name"
                    required
                    wire:model.defer="bankname"
                    disabled />
                <x-loading-spinner wire:target="ifscode" />
            </div>
        </div>
        <div class="grid gap-6 mb-4 md:grid-cols-2 pl-4 pr-4">
            <div class="relative">
                <x-form.input
                    name="bank_branch_name"
                    label="Bank Branch Name"
                    required
                    wire:model.defer="bankbranchname"
                    disabled />
                <x-loading-spinner wire:target="ifscode" />
            </div>
            <div>
                <x-form.masked-input
                    name="bankaccountnumber"
                    label="Bank Account Number"
                    required
                    wire:model.defer="bankaccountnumber" />
            </div>
        </div>
        <div class="grid gap-6 mb-4 md:grid-cols-2 pl-4 pr-4">
            <div>
                <x-form.input
                    name="confirmbankaccountnumber"
                    label="Confirm Bank Account Number"
                    required
                    wire:model.defer="confirmbankaccountnumber"
                    x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '')" />
            </div>
        </div>
        <div class="flex justify-between mt-4 pl-6 pr-6">
            @if ($mode != '0')
            <x-button.danger wire:click="$dispatch('goPrevious')">Previous</x-button.danger>
            @endif
            <x-button.primary :disabled="$score === null" type="submit">
                {{ $mode == '0' ? 'Save' : 'Save & Next' }}
            </x-button.primary>
        </div>
    </form>
</div>