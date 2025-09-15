{{--  <div class="mt-4">
    <div class="p-4 mb-4 border rounded-lg bg-gray-50 shadow-sm">

        <div class="p-4 mb-2 border rounded-lg bg-gray-50 shadow-sm">
            <h2>Select Opertion Type</h2>
            <div class="flex gap-6 pl-4 pr-4 mt-2">
                <label class="flex items-center space-x-2 ">
                    <input type="radio" class="form-radio text-blue-600" wire:model.live="bank_action" value="1"
                        @if ($dupAction === '2') disabled @endif />
                    <span>KEEP SAME</span>
                </label>

                <label class="flex items-center space-x-2">
                    <input type="radio" class="form-radio text-blue-600" wire:model.live="bank_action" value="3"
                        @if ($dupAction === '1' || $dupAction === '2') disabled @endif />
                    <span>CHANGE</span>
                </label>

            </div>
        </div>

       
        @if ($bank_action === '' || $bank_action === '1' || $bank_action === '2')
            <div class="grid gap-6 mb-4 md:grid-cols-3 pl-4 pr-4">
                <x-form.input name="ifscode" label="IFSC Code" wire:model.defer="ifscode" disabled />
                <x-form.input name="bankname" label="Bank Name" wire:model.defer="bankname" disabled />
                <x-form.input name="bankbranchname" label="Branch Name" wire:model.defer="bankbranchname" disabled />
                <x-form.input name="bank_account_number" label="Existing Bank Account Number"
                    wire:model.defer="bank_account_number" disabled />
            </div>
        @endif

        @if ($bank_action === '3')
            <div class="grid gap-6 mb-4 md:grid-cols-3 pl-4 pr-4">

                <x-form.input name="ifscode" label="IFSC Code" wire:model.lazy="ifscode"
                    x-on:input="if ($el.value.length > 11) $el.value = $el.value.slice(0, 11)" />

                <x-form.input name="bankname" label="Bank Name" wire:model.live="bankname" disabled />
                <x-form.input name="bankbranchname" label="Branch Name" wire:model.live="bankbranchname" disabled />

                <x-form.input name="bank_account_number" label="New Bank Account Number"
                    wire:model.live="bank_account_number" x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '')" x-on:copy.prevent/>
            </div>
        @endif
    </div>
</div>  --}}
<div class="mt-4">
    <div class="p-4 mb-4 border rounded-lg bg-gray-50 shadow-sm">

        {{-- Radio Selection --}}
        <div class="p-4 mb-2 border rounded-lg bg-gray-50 shadow-sm">
            <h2>Select Operation Type</h2>
            <div class="flex gap-6 pl-4 pr-4 mt-2">
                <label class="flex items-center space-x-2">
                    <input type="radio" class="form-radio text-blue-600" wire:model.live="bank_action" value="1"
                        @if ($dupAction === '2') disabled @endif />
                    <span>KEEP SAME</span>
                </label>

                <label class="flex items-center space-x-2">
                    <input type="radio" class="form-radio text-blue-600" wire:model.live="bank_action" value="3"
                        @if ($dupAction === '1' || $dupAction === '2') disabled @endif />
                    <span>CHANGE</span>
                </label>
            </div>
            @error('bank_action')
                <span class="text-red-600 text-sm">{{ $message }}</span>
            @enderror
        </div>

        {{-- KEEP SAME / VIEW --}}
        @if (in_array($bank_action, ['', '1', '2']))
            <div class="grid gap-6 mb-4 md:grid-cols-3 pl-4 pr-4">
                <x-form.input name="ifscode" label="IFSC Code" wire:model="ifscode" disabled />
                <x-form.input name="bankname" label="Bank Name" wire:model="bankname" disabled />
                <x-form.input name="bankbranchname" label="Branch Name" wire:model="bankbranchname" disabled />
                <x-form.input name="bank_account_number" label="Existing Bank Account Number"
                    wire:model="bank_account_number" disabled />
            </div>
        @endif

        {{-- CHANGE --}}
        @if ($bank_action === '3')
            <div class="grid gap-6 mb-4 md:grid-cols-3 pl-4 pr-4">
                <x-form.input name="ifscode" label="IFSC Code" wire:model.lazy="ifscode"
                    x-on:input="if ($el.value.length > 11) $el.value = $el.value.slice(0, 11)" />
                @error('ifscode')
                    <span class="text-sm text-red-600">{{ $message }}</span>
                @enderror
                <div class="relative">
                    <x-form.input name="bankname" label="Bank Name" wire:model="bankname" disabled />
                    <x-loading-spinner wire:target="ifscode" />
                </div>

                <div class="relative">
                    <x-form.input name="bankbranchname" label="Branch Name" wire:model="bankbranchname" disabled />
                    <x-loading-spinner wire:target="ifscode" />
                </div>

                {{--  <x-form.input name="bank_account_number" label="New Bank Account Number"
                    wire:model.live="bank_account_number" x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '')"
                    x-on:copy.prevent />  --}}
                <x-form.masked-input name="confirmbankaccountnumber" label="New Bank Account Number" required
                    wire:model.live="confirmbankaccountnumber" />
                @error('confirmbankaccountnumber')
                    <span class="text-sm text-red-600">{{ $message }}</span>
                @enderror

                <x-form.input name="bank_account_number" label="Confirm Bank Account Number" required
                    wire:model.live="bank_account_number" x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '')" />
                @error('bank_account_number')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror

            </div>
        @endif
    </div>
    @if (session()->has('error'))
        <div class="p-3 mb-3 text-red-700 bg-red-100 rounded">
            {{ session('error') }}
        </div>
    @endif
</div>
