<div class="p-6 bg-white rounded shadow">
    {{-- Page Header --}}
    <h1 class="text-xl font-bold mb-4">
        @if ($stage === 'verifier')
            Update Incomplete
        @elseif ($stage === 'approver')
            Approver Incomplete
        @elseif ($stage === 'revert')
            Revert Incomplete
        @else
            Update Incomplete
        @endif
    </h1>

    {{-- Applicant Info --}}
    @if ($applicantInfo)
        <div class="mb-6 p-4 border rounded-lg bg-gray-100 shadow-sm">
            <div class="flex flex-wrap gap-6 text-sm">
                <p><strong>Application ID:</strong> {{ $id }}</p>
                <p><strong>Name:</strong> {{ optional($applicantInfo->beneficiaryPersonal)->full_name ?? 'N/A' }}</p>
                <p><strong>Father Name:</strong>
                    {{ $applicantInfo->beneficiaryPersonal?->father?->first()?->full_name ?? 'N/A' }}</p>
                <p><strong>Address:</strong>
                    @if ($applicantInfo->panchayat)
                        {{ $applicantInfo->panchayat->name }}
                    @elseif ($applicantInfo->ward)
                        {{ $applicantInfo->ward->name }}
                    @else
                        N/A
                    @endif
                </p>
            </div>
        </div>
    @endif

    @if ($errors->has('duplicate_check'))
        <div class="mt-2 mb-0 p-3 border border-red-400 bg-red-100 text-red-700 rounded-md shadow-sm">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->get('duplicate_check') as $message)
                    <li>{{ $message }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if (session('error'))
        <div class="mb-4 p-3 rounded bg-red-100 border border-red-400 text-red-700">
            <strong>Error:</strong> {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('incomplete-full-deatils-update', ['id' => encrypt($id)]) }}">
        @csrf
        @csrf

        @if (!empty($aadhaarIssues))
            <x-incomplete.aadhar-modification :aadhaar-issues="$aadhaarIssues" />
        @endif

        {{-- Mobile Issues --}}
        @if (!empty($mobileIssues))
            <x-incomplete.mobile-issues :mobile-issues="$mobileIssues" />
        @endif

        {{-- Bank Issues --}}
        @if (!empty($sortedBankIssues))
            @foreach ($sortedBankIssues as $item)
                <div class="p-4 mb-4 border rounded-lg bg-gray-50 shadow-sm">
                    <h2 class="font-semibold text-lg text-blue-700 mb-2">{{ $item->incompletType->name }}</h2>

                    @if ($item->incomplet_type == '1411')
                        <livewire:incomplete.dup-bank :item="$item" :dupAction="$item->dupAction"
                            :wire:key="'dup-'.$item->id" />
                    @elseif ($item->incomplet_type == '145')
                        <livewire:incomplete.bank-name-fail :item="$item" :dupAction="$item->dupAction"
                            :wire:key="'name-'.$item->id" />
                    @elseif ($item->incomplet_type == '146')
                        <livewire:incomplete.bank-account-fail :item="$item" :dupAction="$item->dupAction"
                            :wire:key="'account-'.$item->id" />
                    @elseif ($item->incomplet_type == '1412')
                        <livewire:incomplete.mismatch-low :item="$item" :dupAction="$item->dupAction"
                            :wire:key="'mismatch-low-'.$item->id" />
                    @elseif ($item->incomplet_type == '1413')
                        <livewire:incomplete.mismatch-high :item="$item" :dupAction="$item->dupAction"
                            :wire:key="'mismatch-high-'.$item->id" />
                    @endif
                </div>
            @endforeach

        @endif

        <div class="flex justify-end mt-4 space-x-2">
            @if ($stage === 'verifier')
                <x-button.primary type="submit" class="bg-blue-500 text-white whitespace-nowrap"
                    x-on:click="if(confirm('Are you sure you want to submit this request?'))">
                    Request Send to Approver
                </x-button.primary>
            @elseif ($stage === 'approver')
                <div class="flex justify-center w-full space-x-4">
                    <x-button.primary type="submit"
                        x-on:click="if(confirm('Are you sure you want to approve this request?')) { $wire.approve() }">
                        Approve
                    </x-button.primary>
                    <!-- Revert Button -->
                    <x-button.danger x-on:click="$dispatch('open-revert-modal')">
                        Revert
                    </x-button.danger>

                    <!-- Revert Modal -->
                    <div x-data="{ open: false }" x-on:open-revert-modal.window="open = true" x-show="open"
                        class="fixed inset-0 flex items-center justify-center text-gray-800 bg-black/60 z-50"
                        style="display:none">

                        <div class="bg-white rounded-lg shadow-lg p-6 w-96 border-gray-800">
                            <h2 class="text-lg font-semibold mb-4">Revert Application</h2>

                            {{-- Dropdown --}}
                            <div class="mb-4">
                                <x-form.select name="revert_reason_cause_id" id="revert_reason_cause_id"
                                    label="Revert Reason" wire:model.live="revert_reason_cause_id" required>
                                    <option value="">-- Select Reason --</option>
                                    @foreach ($revertReasons as $reason)
                                        <option value="{{ $reason->id }}">{{ $reason->name }}</option>
                                    @endforeach
                                </x-form.select>
                            </div>

                            {{-- Remarks --}}
                            <div class="mb-4">
                                <x-form.textarea id="revert_reason_remarks" name="revert_reason_remarks" label="Remarks"
                                    wire:model="revert_reason_remarks" required />
                            </div>

                            {{-- Buttons --}}
                            <div class="flex justify-end space-x-2">
                                <x-button.primary x-on:click="open = false">Cancel</x-button.primary>

                                <x-button.primary x-on:click="$wire.validateRevert()">
                                    Submit
                                </x-button.primary>
                            </div>
                        </div>
                    </div>

                    {{-- Confirm Alert --}}
                    <script>
                        document.addEventListener('livewire:init', () => {
                            Livewire.on('confirm-revert', () => {
                                if (confirm('Are you sure you want to revert this request?')) {
                                    Livewire.dispatch('do-revert');
                                }
                            });
                        });
                    </script>
                </div>
            @elseif ($stage === 'revert')
                <x-button.primary
                    x-on:click="if(confirm('Are you sure you want to send revert request to approver?')) { $wire.revertVerify() }">
                    Revert Request Send to Approver
                </x-button.primary>
            @endif

        </div>
    </form>
</div>
