<div class="mt-4">
    <div class="p-4 mb-4 border rounded-lg bg-gray-50 shadow-sm">

        {{-- Select Operation Type block (hide if bank_action === 1 or 2) --}}
        @if (!in_array($bank_action, ['1', '2']))
            <div class="p-4 mb-2 border rounded-lg bg-gray-50 shadow-sm">
                <h2>Select Operation Type</h2>
                <div class="flex gap-6 pl-4 pr-4 mt-2">
                    <label class="flex items-center space-x-2 ">
                        <input type="radio" class="form-radio text-blue-600" name="bank_action"
                            wire:model.lazy="bank_action" value="4"
                            @if ($dupAction === '2') disabled @endif />
                        <span>KEEP SAME</span>
                    </label>

                    <label class="flex items-center space-x-2">
                        <input type="radio" class="form-radio text-blue-600" name="bank_action"
                            wire:model.lazy="bank_action" value="3"
                            @if ($dupAction === '1' || $dupAction === '2') disabled @endif
                            {{ old('bank_action', $bank_action) == '3' ? 'checked' : '' }} x-on:change="Livewire.dispatch('showLoader')" />
                        <span>CHANGE</span>
                    </label>
                </div>
                @if ($errors->has('bank_action'))
                    <span class="text-red-800 text-sm">
                        <li>{{ $errors->first('bank_action') }}</li>
                @endif
            </div>
        @endif

        {{-- Existing Bank Details (readonly) --}}
        @if ($bank_action === '' || $bank_action === '4')
            <div class="grid gap-6 mb-4 md:grid-cols-3 pl-4 pr-4">
                <x-form.input name="ifscode" label="IFSC Code" wire:model.defer="ifscode" readonly />
                <x-form.input name="bankname" label="Bank Name" wire:model.defer="bankname" readonly />
                <x-form.input name="bankbranchname" label="Branch Name" wire:model.defer="bankbranchname" readonly />
                <x-form.input name="bank_account_number" label="Existing Bank Account Number"
                    wire:model.defer="bank_account_number" readonly />
            </div>
        @endif

        {{-- Message for bank_action = 1 --}}
        @if ($bank_action === '1')
            <div class="grid gap-6 mb-4 md:grid-cols-3 pl-4 pr-4">
                <div class="col-span-3">
                    <div class="p-4 rounded-md bg-blue-100 border border-blue-300 text-blue-800 text-sm">
                        ℹ️ As keep same has been accepted in duplicate bank, the modification in this portion will not
                        be required further.
                    </div>
                </div>
            </div>
        @endif

        {{-- Message for bank_action = 2 --}}
        @if ($bank_action === '2')
            <div class="grid gap-6 mb-4 md:grid-cols-3 pl-4 pr-4">
                <div class="col-span-3">
                    <div class="p-4 rounded-md bg-yellow-100 border border-yellow-300 text-yellow-800 text-sm">
                        ⚠️ As Duplicate Bank Account has been changed, the modification in this portion will not be
                        required further.
                    </div>
                </div>
            </div>
        @endif

        {{-- Form for bank_action = 3 --}}
        @if ($bank_action === '3')
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
                <x-form.input name="ifscode" label="IFSC Code" wire:model.live="ifscode"
                    x-on:input="if ($el.value.length > 11) $el.value = $el.value.slice(0, 11)" />

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
                {{--  <input type="hidden" name="bank_account_number" :value="$wire.bank_account_number">  --}}
                {{-- Confirm Bank Account Number --}}
                <div class="col-span-1">
                    <x-form.input name="confirmbankaccountnumber" label="Confirm Bank Account Number" required
                        wire:model="confirmbankaccountnumber"
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

                 <div class="flex gap-6">
                    <div class="w-1/2">
                        <h3 class="font-semibold mb-2">Newly Temp Document</h3>

                        <livewire:enclosure-list :application_id="$item->application_id" :doc_type_id_array_list="[112]" enclosureSource="5" />

                        {{-- Error --}}
                        @error('document_upload')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

            </div>
        @endif
    </div>
</div>
