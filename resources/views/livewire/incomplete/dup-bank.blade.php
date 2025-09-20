<div class="mt-4">
    <div class="p-4 mb-4 border rounded-lg bg-gray-50 shadow-sm">
        <div class="p-4 mb-2 border rounded-lg bg-gray-50 shadow-sm">
            <h2>Select Opertion Type</h2>
            <div class="flex gap-6 pl-4 pr-4 mt-2">
                <label class="flex items-center space-x-2">
                    <input type="radio" class="form-radio text-blue-600" name="bank_action" wire:model.live="bank_action"
                        value="1" />
                    <span>KEEP SAME</span>
                </label>
                <label class="flex items-center space-x-2">
                    <input type="radio" class="form-radio text-blue-600" name="bank_action"
                        wire:model.live="bank_action" value="2" />
                    <span>CHANGE</span>
                </label>
            </div>
            @if ($errors->has('bank_action'))
                <span class="text-red-800 text-sm">
                    <li>{{ $errors->first('bank_action') }}</li>
            @endif
        </div>

        @if ($bank_action === '' || $bank_action === '1')
            <div class="grid gap-6 mb-4 md:grid-cols-3 pl-4 pr-4">
                <x-form.input name="ifscode" label="IFSC Code" wire:model.defer="ifscode" readonly />
                <x-form.input name="bankname" label="Bank Name" wire:model.defer="bankname" readonly />
                <x-form.input name="bankbranchname" label="Branch Name" wire:model.defer="bankbranchname" readonly />
                <x-form.input name="bank_account_number" label="Existing Bank Account Number"
                    wire:model.defer="bank_account_number" readonly />
            </div>
        @endif

        @if ($bank_action === '2')
            <div x-data="{
                bank: @entangle('bank_account_number').live,
                confirm: @entangle('confirmbankaccountnumber').live,
                showSuccess: false,
                showError: false,
                checkMatch() {
                    if (this.confirm && this.bank) {
                        if (this.bank === this.confirm) {
                            this.showError = false;
                            this.showSuccess = true;
                            setTimeout(() => this.showSuccess = false, 2000); // 2 sec পরে success msg উড়ে যাবে
                        } else {
                            this.showSuccess = false;
                            this.showError = true;
                        }
                    } else {
                        this.showSuccess = false;
                        this.showError = false;
                    }
                }
            }" x-effect="checkMatch()" class="grid gap-6 mb-4 md:grid-cols-3 pl-4 pr-4">

                {{-- IFSC Code --}}
                <x-form.input name="ifscode" label="IFSC Code" wire:model.lazy="ifscode"
                    x-on:input="if ($el.value.length > 11) $el.value = $el.value.slice(0, 11)" />
                {{--  @if ($errors->has('ifscode'))
                    <span class="text-red-800 text-sm">
                        <li>{{ $errors->first('ifscode') }}</li>
                @endif  --}}

                {{-- Bank Name --}}
                <div class="relative">
                    <x-form.input name="bankname" label="Bank Name" wire:model="bankname" disabled />
                    <x-loading-spinner wire:target="ifscode" />
                </div>

                {{-- Branch Name --}}
                <div class="relative">
                    <x-form.input name="bankbranchname" label="Branch Name" wire:model="bankbranchname" disabled />
                    <x-loading-spinner wire:target="ifscode" />
                </div>

                {{-- New Bank Account Number --}}
                <x-form.masked-input name="bank_account_number" label="New Bank Account Number" required
                    wire:model.live="bank_account_number" x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '')" />
                {{--  @if ($errors->has('bank_account_number'))
                    <span class="text-red-800 text-sm">
                        <li>{{ $errors->first('bank_account_number') }}</li>
                @endif  --}}
                {{-- Confirm Bank Account Number --}}
                <div class="col-span-1">
                    <x-form.input name="confirmbankaccountnumber" label="Confirm Bank Account Number" required
                        wire:model.live="confirmbankaccountnumber"
                        x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '')" />
                    {{--  @if ($errors->has('confirmbankaccountnumber'))
                        <span class="text-red-800 text-sm">
                            <li>{{ $errors->first('confirmbankaccountnumber') }}</li>
                    @endif  --}}
                    {{-- Error Message --}}
                    <p x-show="showError" x-transition.opacity class="text-red-500 text-sm mt-1">
                        ❌ Bank account numbers do not match
                    </p>

                    {{-- Success Message --}}
                    <p x-show="showSuccess" x-transition.opacity class="text-green-600 text-sm mt-1">
                        ✅ Bank account numbers match
                    </p>
                </div>


                <div class="flex gap-6">
                    {{-- Newly Temp Document --}}
                    <div class="w-1/2">
                        <h3 class="font-semibold mb-2">Newly Temp Document</h3>
                        <livewire:enclosure-list :application_id="$item->application_id" :doc_type_id_array_list="[112]" enclosureSource="5" />
                    </div>
                </div>

            </div>
        @endif
    </div>





    @error('duplicate_check')
        <span class="text-red-600 text-sm">{{ $message }}</span>
    @enderror
</div>
