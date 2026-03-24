<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-auto mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Beneficiary Update Request</h1>
            <p class="text-sm text-gray-600 mt-1">Search and update beneficiary information with workflow approval</p>
            <div class="mt-3 flex flex-wrap gap-2 text-xs">
                <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 font-semibold text-slate-700">
                    Code: {{ $moduleCode }}
                </span>
            </div>
        </div>
        <div>
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                        <h2 class="text-sm font-semibold text-gray-700 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Beneficiary Lookup
                        </h2>
                    </div>
                    <div class="p-4">
                        @livewire('beneficiary-search', [
                        'isApproved' => true,
                        'isShownScheme' => true,
                        'selectedScheme' => $moduleSchemeId,
                        'excludeFields' => ['beneficiary_name','bank_account_number']
                        ])
                    </div>
                </div>
            </div>
            <div class="lg:col-span-2 space-y-6">
                @if(!empty($items))
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden animate-fadeIn" wire:key="search-results-table">
                    <div class="px-4 py-3 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                        <h2 class="text-sm font-semibold text-gray-700">Search Results</h2>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                            {{ count($items) }} match{{ count($items) > 1 ? 'es' : '' }}
                        </span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <!-- Table Header -->
                            <thead class="bg-gradient-to-r from-indigo-600 to-indigo-700">
                                <tr>
                                    <th class="px-5 py-4 text-center text-xs font-semibold text-white uppercase tracking-wider border-r border-indigo-400">Application ID</th>
                                    <th class="px-5 py-4 text-center text-xs font-semibold text-white uppercase tracking-wider border-r border-indigo-400">Beneficiary ID</th>
                                    <th class="px-5 py-4 text-center text-xs font-semibold text-white uppercase tracking-wider border-r border-indigo-400">Applicant Name</th>
                                    <th class="px-5 py-4 text-center text-xs font-semibold text-white uppercase tracking-wider border-r border-indigo-400">Mobile</th>
                                    <th class="px-5 py-4 text-center text-xs font-semibold text-white uppercase tracking-wider border-r border-indigo-400">Address</th>
                                    <th class="px-5 py-4 text-center text-xs font-semibold text-white uppercase tracking-wider border-r border-indigo-400">Bank Details</th>
                                    <th class="px-5 py-4 text-center text-xs font-semibold text-white uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>

                            <!-- Table Body -->
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @foreach($items as $index => $row)
                                @php($isSelected = $beneficiary && (string) $beneficiary->application_id === (string) $row['application_id'])
                                <tr class="transition-all duration-200 ease-in-out {{ $isSelected ? 'bg-indigo-50' : ($index % 2 == 0 ? 'bg-white hover:bg-indigo-50' : 'bg-gray-50/50 hover:bg-indigo-50') }}">
                                    <td class="px-5 py-4 text-center">
                                        <span class="text-sm font-medium text-indigo-700 bg-indigo-50 px-2 py-1 rounded-lg">{{ $row['application_id'] }}</span>
                                    </td>

                                    <td class="px-5 py-4 text-center">
                                        <span class="text-sm text-gray-700 font-mono">{{ $row['beneficiary_id'] }}</span>
                                    </td>

                                    <td class="px-5 py-4">
                                        <div class="flex items-center justify-center">
                                            <span class="text-sm font-medium text-gray-900">{{ $row['applicant_name'] }}</span>
                                        </div>
                                    </td>

                                    <td class="px-5 py-4 text-center">
                                        <div class="flex items-center justify-center text-sm text-gray-700">
                                            <svg class="w-4 h-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                            </svg>
                                            {{ $row['mobile_no'] }}
                                        </div>
                                    </td>

                                    <td class="px-5 py-4 max-w-xs">
                                        <div class="text-sm text-gray-700 bg-gray-50 p-2 rounded-lg border border-gray-100">
                                            {!! $row['address'] !!}
                                        </div>
                                    </td>

                                    <td class="px-5 py-4">
                                        <div class="bg-blue-50 rounded-lg p-2 border border-blue-100">
                                            <div class="text-xs text-gray-600 flex items-center mb-1">
                                                <span class="font-semibold">A/C:</span>
                                                <span class="font-mono text-indigo-600 p-1">{{ $row['bank_account'] }}</span>
                                            </div>
                                            <div class="text-xs text-gray-600">
                                                <span class="font-semibold">IFSC:</span>
                                                <span class="font-mono text-indigo-600 p-1">{{ $row['ifsc'] }}</span>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-5 py-4 text-center">
                                        <button
                                            type="button"
                                            wire:click="selectBeneficiary({{ $row['application_id'] }})"
                                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white {{ $isSelected ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-indigo-600 hover:bg-indigo-700' }} focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">

                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                </path>
                                            </svg>

                                            {{ $isSelected ? 'Selected' : 'Update' }}
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                <!-- Selected Beneficiary & Update Form -->
                @if($beneficiary)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden animate-fadeIn">
                    <!-- Beneficiary Header -->
                    @if($showFields)
                    <div class="p-5 space-y-6">
                        <div class="pb-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">{{ $beneficiary->beneficiary_name }}</h3>
                            <p class="text-sm text-gray-500">
                                Application ID: {{ $beneficiary->application_id }} |
                                Beneficiary ID: {{ $beneficiary->beneficiary_id }}
                            </p>
                        </div>

                        <div class="mb-6">
                            <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-3">
                                Select fields to update
                            </h4>

                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                @foreach($fieldOptions as $fieldKey => $label)
                                <label wire:key="field-{{ $fieldKey }}"
                                    class="relative flex items-center p-3 rounded-lg border cursor-pointer transition-all
                {{ in_array($fieldKey, $selectedFields) ? 'bg-indigo-50 border-indigo-300' : 'bg-white border-gray-200 hover:border-indigo-200' }}">

                                    <input type="checkbox"
                                        wire:model.live="selectedFields"
                                        value="{{ $fieldKey }}"
                                        class="w-4 h-4 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500">

                                    <span class="ml-2 text-xs font-medium text-gray-700">
                                        {{ $label }}
                                    </span>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- DEBUG (temporary, পরে remove করো) --}}

                        @if(count($selectedFields) > 0)
                        <div class="space-y-5">
                            @if(in_array('beneficiary_name', $selectedFields, true))
                            <div class="rounded-xl border border-gray-200 p-4">
                                <h4 class="text-sm font-semibold text-gray-800 mb-4">Name Update</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Old Name</label>
                                        <input type="text" value="{{ $oldData['beneficiary_name'] ?? '' }}" class="block w-full px-3 py-2 text-sm bg-gray-50 border border-gray-200 rounded-md text-gray-500" readonly>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">New Name</label>
                                        <input type="text" wire:model.live="newData.beneficiary_name" class="block w-full px-3 py-2 text-sm border border-indigo-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter new beneficiary name">
                                        @error('newData.beneficiary_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if(in_array('dob_age', $selectedFields, true))
                            <div class="rounded-xl border border-gray-200 p-4">
                                <h4 class="text-sm font-semibold text-gray-800 mb-4">Date of Birth / Age Update</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Old Date of Birth</label>
                                        <input type="date" value="{{ $oldData['dob'] ?? '' }}" class="block w-full px-3 py-2 text-sm bg-gray-50 border border-gray-200 rounded-md text-gray-500" readonly>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">New Date of Birth</label>
                                        <input type="date" wire:model.live="newData.dob" class="block w-full px-3 py-2 text-sm border border-indigo-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                                        @error('newData.dob') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Old Age</label>
                                        <input type="text" value="{{ $oldData['age'] ?? '' }}" class="block w-full px-3 py-2 text-sm bg-gray-50 border border-gray-200 rounded-md text-gray-500" readonly>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Calculated Age</label>
                                        <input type="text" wire:model="newData.age" class="block w-full px-3 py-2 text-sm bg-indigo-50 border border-indigo-200 rounded-md text-indigo-700" readonly>
                                        @error('newData.age') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if(in_array('mobile_no', $selectedFields, true))
                            <div class="rounded-xl border border-gray-200 p-4">
                                <h4 class="text-sm font-semibold text-gray-800 mb-4">Mobile Update</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Old Mobile</label>
                                        <input type="text" value="{{ $oldData['mobile_no'] ?? '' }}" class="block w-full px-3 py-2 text-sm bg-gray-50 border border-gray-200 rounded-md text-gray-500" readonly>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">New Mobile</label>
                                        <input type="text" wire:model.live="newData.mobile_no" maxlength="10" class="block w-full px-3 py-2 text-sm border border-indigo-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter new mobile number">
                                        @error('newData.mobile_no') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if(in_array('bank_details', $selectedFields, true))
                            <div class="rounded-xl border border-gray-200 p-4">
                                <h4 class="text-sm font-semibold text-gray-800 mb-4">Bank Update</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Old IFSC</label>
                                        <input type="text" value="{{ $oldData['bank_ifsc'] ?? '' }}" class="block w-full px-3 py-2 text-sm bg-gray-50 border border-gray-200 rounded-md text-gray-500" readonly>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">New IFSC</label>
                                        <input type="text" wire:model.live="newData.bank_ifsc" maxlength="11" class="block w-full px-3 py-2 text-sm border border-indigo-300 rounded-md uppercase focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter IFSC">
                                        @error('newData.bank_ifsc') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Old Bank Name</label>
                                        <input type="text" value="{{ $oldData['bank_name'] ?? '' }}" class="block w-full px-3 py-2 text-sm bg-gray-50 border border-gray-200 rounded-md text-gray-500" readonly>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">New Bank Name</label>
                                        <input type="text" wire:model="newData.bank_name" class="block w-full px-3 py-2 text-sm bg-indigo-50 border border-indigo-200 rounded-md text-indigo-700" readonly>
                                        @error('newData.bank_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Old Branch</label>
                                        <input type="text" value="{{ $oldData['bank_branch_name'] ?? '' }}" class="block w-full px-3 py-2 text-sm bg-gray-50 border border-gray-200 rounded-md text-gray-500" readonly>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">New Branch</label>
                                        <input type="text" wire:model="newData.bank_branch_name" class="block w-full px-3 py-2 text-sm bg-indigo-50 border border-indigo-200 rounded-md text-indigo-700" readonly>
                                        @error('newData.bank_branch_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Old Account Number</label>
                                        <input type="text" value="{{ $oldData['bank_account_number'] ?? '' }}" class="block w-full px-3 py-2 text-sm bg-gray-50 border border-gray-200 rounded-md text-gray-500" readonly>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">New Account Number</label>
                                        <input type="text" wire:model.live="newData.bank_account_number" class="block w-full px-3 py-2 text-sm border border-indigo-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter new account number">
                                        @error('newData.bank_account_number') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Confirm Account Number</label>
                                        <input type="text" wire:model.live="newData.confirm_bank_account_number" class="block w-full px-3 py-2 text-sm border border-indigo-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500" placeholder="Confirm account number">
                                        @error('newData.confirm_bank_account_number') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>
                            @endif

                            <div class="flex justify-end pt-4 border-t border-gray-200 mt-4">
                                <button wire:click="submitRequest" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                                    </svg>
                                    Submit Update Request
                                </button>
                            </div>
                        </div>
                        @endif
                    </div>
                    @else
                    <div class="p-8 text-center">
                        <div class="w-16 h-16 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-3">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <p class="text-sm text-gray-500">Click "Start Editing" to begin modification</p>
                    </div>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Animation Styles -->
    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fadeIn {
            animation: fadeIn 0.3s ease-out;
        }
    </style>
</div>