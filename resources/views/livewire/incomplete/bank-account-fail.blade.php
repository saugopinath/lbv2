<div class="mt-4">
    <div class="p-4 mb-4 border rounded-lg bg-gray-50 shadow-sm">

        {{-- Select Operation Type block (hide if bank_action === 1 or 2) --}}
        @if (!in_array($bank_action, ['1', '2']))
            <div class="p-4 mb-2 border rounded-lg bg-gray-50 shadow-sm">
                <h2>Select Operation Type</h2>
                <div class="flex gap-6 pl-4 pr-4 mt-2">
                    <label class="flex items-center space-x-2 ">
                        <input type="radio" class="form-radio text-blue-600" wire:model.live="bank_action" value="4"
                            @if ($dupAction === '2') disabled @endif />
                        <span>KEEP SAME</span>
                    </label>

                    <label class="flex items-center space-x-2">
                        <input type="radio" class="form-radio text-blue-600" wire:model.live="bank_action"
                            value="3" @if ($dupAction === '1' || $dupAction === '2') disabled @endif />
                        <span>CHANGE</span>
                    </label>
                </div>
            </div>
        @endif

        {{-- Existing Bank Details (readonly) --}}
        @if ($bank_action === '' || $bank_action === '4')
            <div class="grid gap-6 mb-4 md:grid-cols-3 pl-4 pr-4">
                <x-form.input name="ifscode" label="IFSC Code" wire:model.defer="ifscode" disabled />
                <x-form.input name="bankname" label="Bank Name" wire:model.defer="bankname" disabled />
                <x-form.input name="bankbranchname" label="Branch Name" wire:model.defer="bankbranchname" disabled />
                <x-form.input name="bank_account_number" label="Existing Bank Account Number"
                    wire:model.defer="bank_account_number" disabled />
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
            <div class="grid gap-6 mb-4 md:grid-cols-3 pl-4 pr-4">
                <x-form.input name="ifscode" label="IFSC Code" wire:model.lazy="ifscode"
                    x-on:input="if ($el.value.length > 11) $el.value = $el.value.slice(0, 11)" />

                <div class="relative">
                    <x-form.input name="bankname" label="Bank Name" wire:model="bankname" disabled />
                    <x-loading-spinner wire:target="ifscode" />
                </div>

                <div class="relative">
                    <x-form.input name="bankbranchname" label="Branch Name" wire:model="bankbranchname" disabled />
                    <x-loading-spinner wire:target="ifscode" />
                </div>

                <x-form.masked-input name="bank_account_number" label="New Bank Account Number" required
                    wire:model.live="bank_account_number" />

                <x-form.input name="confirmbankaccountnumber" label="Confirm Bank Account Number" required
                    wire:model.live="confirmbankaccountnumber"
                    x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '')" />



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

    {{-- Global Error Message --}}
    @if (session()->has('error'))
        <div class="p-3 mb-3 text-red-700 bg-red-100 rounded">
            {{ session('error') }}
        </div>
    @endif
</div>
