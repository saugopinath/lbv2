<div class="space-y-5">
    {{-- Select Operation Type (Verifier & Revert queues) --}}
    @if (!empty($stage) && in_array($stage, ['verifier', 'revert']))
        <div class="p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/70 dark:bg-gray-900/30">
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-2.5">
                Select Operation Type <span class="text-rose-500">*</span>
            </label>
            <div class="flex flex-wrap gap-4">
                {{-- KEEP SAME (value 1) --}}
                <label class="relative flex items-center gap-3 px-4 py-2.5 rounded-lg border cursor-pointer transition {{ $bank_action == '1' ? 'border-violet-600 bg-violet-50/60 dark:bg-violet-950/40 text-violet-900 dark:text-violet-200 shadow-xs' : 'border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:border-gray-400' }}">
                    <input type="radio"
                           class="w-4 h-4 text-violet-600 focus:ring-violet-500 border-gray-300"
                           name="bank_action"
                           wire:model.lazy="bank_action"
                           value="1"
                           @if (!empty($stage) && $stage === 'approver') disabled @endif />
                    <span class="text-xs font-bold uppercase tracking-wide">Keep Same Account</span>
                </label>

                {{-- CHANGE (value 2) --}}
                <label class="relative flex items-center gap-3 px-4 py-2.5 rounded-lg border cursor-pointer transition {{ $bank_action == '2' ? 'border-violet-600 bg-violet-50/60 dark:bg-violet-950/40 text-violet-900 dark:text-violet-200 shadow-xs' : 'border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:border-gray-400' }}">
                    <input type="radio"
                           class="w-4 h-4 text-violet-600 focus:ring-violet-500 border-gray-300"
                           name="bank_action"
                           wire:model.lazy="bank_action"
                           value="2"
                           {{ old('bank_action', $bank_action) == '2' ? 'checked' : '' }}
                           x-on:change="Livewire.dispatch('showLoader')"
                           @if (!empty($stage) && $stage === 'approver') disabled @endif />
                    <span class="text-xs font-bold uppercase tracking-wide">Change Bank Details</span>
                </label>
            </div>

            @if ($errors->has('bank_action'))
                <span class="text-rose-600 text-xs mt-2 block">{{ $errors->first('bank_action') }}</span>
            @endif
        </div>
    @endif

    {{-- Approver Status Badge --}}
    @if (!empty($stage) && $stage === 'approver')
        <div class="flex items-center gap-2.5 p-3 rounded-lg bg-gray-50 dark:bg-gray-900/40 border border-gray-200 dark:border-gray-700">
            <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Action Selected:</span>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $bank_action == '1' || $bank_action == '4' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300' : 'bg-violet-100 text-violet-800 dark:bg-violet-900/40 dark:text-violet-300' }}">
                {{ $bank_action == '1' || $bank_action == '4' ? 'KEEP SAME' : 'CHANGE DETAILS' }}
            </span>
        </div>
    @endif

    {{-- Existing Bank Details (When keeping same or initial state) --}}
    @if (!empty($stage) && in_array($stage, ['verifier', 'revert']))
        @if ($bank_action === '' || $bank_action === '1')
            <div>
                <h4 class="text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-400 mb-3 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <span>Current Bank Details on File</span>
                </h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-900/30 border border-gray-200 dark:border-gray-700">
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 block mb-0.5">IFS Code</span>
                        <span class="text-xs font-mono font-bold text-gray-800 dark:text-white">{{ $ifscode ?? 'N/A' }}</span>
                    </div>
                    <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-900/30 border border-gray-200 dark:border-gray-700">
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 block mb-0.5">Bank Name</span>
                        <span class="text-xs font-semibold text-gray-800 dark:text-white">{{ $bankname ?? 'N/A' }}</span>
                    </div>
                    <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-900/30 border border-gray-200 dark:border-gray-700">
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 block mb-0.5">Branch Name</span>
                        <span class="text-xs font-semibold text-gray-800 dark:text-white">{{ $bankbranchname ?? 'N/A' }}</span>
                    </div>
                    <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-900/30 border border-gray-200 dark:border-gray-700">
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 block mb-0.5">Account Number</span>
                        <span class="text-xs font-mono font-bold text-gray-800 dark:text-white">{{ $bank_account_number ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        @endif

        {{-- Form for Bank Action = 2 (Change) --}}
        @if ($bank_action === '2')
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
                            setTimeout(() => this.showSuccess = false, 2500);
                        } else {
                            this.showSuccess = false;
                            this.showError = true;
                        }
                    } else {
                        this.showSuccess = false;
                        this.showError = false;
                    }
                }
            }" x-effect="checkMatch()" class="space-y-5">

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    {{-- IFSC Code --}}
                    <div>
                        <x-form.input name="ifscode" label="IFS Code" required x-model="ifscode" maxlength="11"
                            wire:model.live="ifscode"
                            x-on:input="
                                ifscode = $el.value.toUpperCase().slice(0, 11);
                                $el.value = ifscode;
                            " />
                    </div>

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
                    <div>
                        <x-form.input type="password" name="bank_account_number" label="New Bank Account Number" required
                            wire:model="bank_account_number" x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '')" />
                    </div>

                    {{-- Confirm Bank Account Number --}}
                    <div>
                        <x-form.input name="confirmbankaccountnumber" label="Confirm Bank Account Number" required
                            wire:model="confirmbankaccountnumber"
                            x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '')" />

                        <p x-show="showError" x-transition.opacity class="text-rose-600 text-xs font-semibold mt-1">
                            ❌ Bank account numbers do not match
                        </p>
                        <p x-show="showSuccess" x-transition.opacity class="text-emerald-600 text-xs font-semibold mt-1">
                            ✅ Bank account numbers match
                        </p>
                    </div>
                </div>

                {{-- Bank Enclosures Grid --}}
                <div class="pt-4 border-t border-gray-100 dark:border-gray-700">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-400 mb-3 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                        </svg>
                        <span>Bank Passbook Enclosures</span>
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Previous Approved Document --}}
                        <div class="p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/60 dark:bg-gray-900/20">
                            <div class="flex items-center justify-between pb-2 mb-3 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                    Previous Approved Passbook Copy
                                </span>
                                <span class="text-xs px-2.5 py-0.5 rounded-md bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300 font-medium">
                                    On File
                                </span>
                            </div>
                            <livewire:enclosure-list
                                :application_id="$item->application_id"
                                :scheme_id="$schemeId"
                                :doc_type_id_array_list="[111]"
                                :is_page="1"
                                :key="'bank-old-'.$item->application_id" />
                        </div>

                        {{-- Newly Uploaded Document --}}
                        <div class="p-4 rounded-xl border border-violet-200 dark:border-violet-800 bg-violet-50/30 dark:bg-violet-950/10">
                            <div class="flex items-center justify-between pb-2 mb-3 border-b border-violet-100 dark:border-violet-800/60">
                                <span class="text-xs font-bold text-violet-900 dark:text-violet-200 uppercase tracking-wider">
                                    New Uploaded Passbook Copy
                                </span>
                                <span class="text-xs px-2.5 py-0.5 rounded-md bg-violet-100 dark:bg-violet-900/50 text-violet-800 dark:text-violet-300 font-medium">
                                    Verification Copy
                                </span>
                            </div>
                            <livewire:enclosure-list
                                :application_id="$item->application_id"
                                :scheme_id="$schemeId"
                                :doc_type_id_array_list="[111]"
                                :enclosureSource="5"
                                :wire:key="'bank-new-'.$item->application_id" />

                            @error('document_upload')
                                <span class="text-rose-600 text-xs mt-2 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif

    {{-- Approver View --}}
    @if (!empty($stage) && $stage === 'approver')
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-900/30 border border-gray-200 dark:border-gray-700">
                <span class="text-xs font-medium text-gray-500 dark:text-gray-400 block mb-0.5">IFS Code</span>
                <span class="text-xs font-mono font-bold text-gray-800 dark:text-white">{{ $ifscode ?? 'N/A' }}</span>
            </div>
            <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-900/30 border border-gray-200 dark:border-gray-700">
                <span class="text-xs font-medium text-gray-500 dark:text-gray-400 block mb-0.5">Bank Name</span>
                <span class="text-xs font-semibold text-gray-800 dark:text-white">{{ $bankname ?? 'N/A' }}</span>
            </div>
            <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-900/30 border border-gray-200 dark:border-gray-700">
                <span class="text-xs font-medium text-gray-500 dark:text-gray-400 block mb-0.5">Branch Name</span>
                <span class="text-xs font-semibold text-gray-800 dark:text-white">{{ $bankbranchname ?? 'N/A' }}</span>
            </div>
            <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-900/30 border border-gray-200 dark:border-gray-700">
                <span class="text-xs font-medium text-gray-500 dark:text-gray-400 block mb-0.5">Account Number</span>
                <span class="text-xs font-mono font-bold text-gray-800 dark:text-white">{{ $bank_account_number ?? 'N/A' }}</span>
            </div>
        </div>

        <div class="mt-4 p-4 rounded-xl border border-blue-200 dark:border-blue-800 bg-blue-50/30 dark:bg-blue-950/20">
            <h4 class="text-xs font-bold uppercase tracking-wider text-blue-900 dark:text-blue-200 mb-3">
                Submitted Passbook Enclosure
            </h4>
            <livewire:enclosure-list :application_id="$item->application_id" :scheme_id="$schemeId" :doc_type_id_array_list="[111]" :is_page="1" enclosureSource="5" />
        </div>
    @endif
</div>
