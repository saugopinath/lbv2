<div class="mt-4">
    <div class="p-4 mb-4 border rounded-lg bg-gray-50 shadow-sm">
        <div x-data="{
                    bank: @entangle('bank_account_number').live,
                    confirm: @entangle('confirmbankaccountnumber').live,
                    ifscode: @entangle('ifscode'),
                    showSuccess: false,
                    showError: false,
                    checkMatch() {
                        if (this.confirm && this.bank) {
                            if (this.bank === this.confirm) {
                                this.showError = false;
                                this.showSuccess = true;
                                setTimeout(() => this.showSuccess = false, 2000);
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
            <x-form.input name="ifscode" label="IFS Code" required x-model="ifscode" maxlength="11"
                wire:model.live="ifscode" x-on:input="
                    ifscode = $el.value.toUpperCase().slice(0, 11);
                    $el.value = ifscode;
                " />

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
            <x-form.input type="password" name="bank_account_number" label="New Bank Account Number" required
                wire:model="bank_account_number" x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '')" />

            {{-- Confirm Bank Account Number --}}
            <div class="col-span-1">
                <x-form.input name="confirmbankaccountnumber" label="Confirm Bank Account Number"
                    wire:model="confirmbankaccountnumber" required
                    x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '')" />

                {{-- Error Message --}}
                <p x-show="showError" x-transition.opacity class="text-red-500 text-sm mt-1">
                    ❌ Bank account numbers do not match
                </p>

                {{-- Success Message --}}
                <p x-show="showSuccess" x-transition.opacity class="text-green-600 text-sm mt-1">
                    ✅ Bank account numbers match
                </p>
            </div>
            
            <div>
                <h3 class="font-semibold mb-2">Upload Bank Passbook</h3>
                <livewire:enclosure-list :application_id="$application_id" :doc_type_id_array_list="[111]" />                
                @error('document_type')
                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
</div>