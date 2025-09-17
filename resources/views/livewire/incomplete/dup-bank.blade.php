<div class="mt-4">
    <div class="p-4 mb-4 border rounded-lg bg-gray-50 shadow-sm">
        <div class="p-4 mb-2 border rounded-lg bg-gray-50 shadow-sm">
            <h2>Select Opertion Type</h2>
            <div class="flex gap-6 pl-4 pr-4 mt-2">
                <label class="flex items-center space-x-2">
                    <input type="radio" class="form-radio text-blue-600" wire:model.live="bank_action" value="1" />
                    <span>KEEP SAME</span>
                </label>
                <label class="flex items-center space-x-2">
                    <input type="radio" class="form-radio text-blue-600" wire:model.live="bank_action"
                        value="2" />
                    <span>CHANGE</span>
                </label>
            </div>
        </div>

        @if ($bank_action === '' || $bank_action === '1')
            <div class="grid gap-6 mb-4 md:grid-cols-3 pl-4 pr-4">
                <x-form.input name="ifscode" label="IFSC Code" wire:model.defer="ifscode" disabled />
                <x-form.input name="bankname" label="Bank Name" wire:model.defer="bankname" disabled />
                <x-form.input name="bankbranchname" label="Branch Name" wire:model.defer="bankbranchname" disabled />
                <x-form.input name="bank_account_number" label="Existing Bank Account Number"
                    wire:model.defer="bank_account_number" disabled />
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

                {{-- Confirm Bank Account Number --}}
                <div class="col-span-1">
                    <x-form.input name="confirmbankaccountnumber" label="Confirm Bank Account Number" required
                        wire:model.live="confirmbankaccountnumber"
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
                    {{-- Previous Approved Document --}}
                    <div class="w-1/2">
                        <h3 class="font-semibold mb-2">Previous Approved Document</h3>
                       {{--  <livewire:enclosure-list :application_id="$item->application_id" :doc_type_id_array_list="[112]" :is_page="1" />  --}}
                    </div>

                    {{-- Newly Temp Document --}}
                    <div class="w-1/2">
                        <h3 class="font-semibold mb-2">Newly Temp Document</h3>
                        <livewire:enclosure-list :application_id="$item->application_id" :doc_type_id_array_list="[112]" enclosureSource="5" />
                    </div>
                </div>

            </div>
        @endif


    </div>
    @if (session()->has('error'))
        <div class="p-3 mb-3 text-red-700 bg-red-100 rounded">
            {{ session('error') }}
        </div>
    @endif
</div>
