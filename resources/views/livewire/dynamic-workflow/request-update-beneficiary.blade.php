<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Beneficiary Update Request</h1>
            <p class="text-sm text-gray-600 mt-1">Search and update beneficiary information with workflow approval</p>
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
                                <tr class="hover:bg-indigo-50 transition-all duration-200 ease-in-out {{ $index % 2 == 0 ? 'bg-white' : 'bg-gray-50/50' }}">
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
                                        <button wire:click="selectBeneficiary('{{ $row['application_id'] }}')" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                            Update
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
                    <div class="p-5">
                        <!-- Field Selection -->
                        <div class="mb-6">
                            <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-3">Select fields to update</h4>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                @foreach($fieldOptions as $fieldKey => $label)
                                <label wire:key="field-{{ $fieldKey }}" class="relative flex items-center p-3 rounded-lg border cursor-pointer transition-all {{ in_array($fieldKey, $selectedFields) ? 'bg-indigo-50 border-indigo-300' : 'bg-white border-gray-200 hover:border-indigo-200' }}">
                                    <input type="checkbox" wire:model.live="selectedFields" value="{{ $fieldKey }}" class="w-4 h-4 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500">
                                    <span class="ml-2 text-xs font-medium text-gray-700">{{ $label }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Update Form -->
                        @if(!empty($selectedFields))
                        <div class="space-y-4">
                            <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-3">Enter new values</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($selectedFields as $fld)
                                <div class="space-y-1.5">
                                    <label class="block text-xs font-medium text-gray-700 capitalize">{{ str_replace('_', ' ', $fld) }}</label>
                                    <div class="flex items-center space-x-2">
                                        <div class="flex-1">
                                            <input type="text"
                                                value="{{ $oldData[$fld] }}"
                                                class="block w-full px-3 py-2 text-xs bg-gray-50 border border-gray-200 rounded-md text-gray-500"
                                                readonly>
                                        </div>
                                        <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                        </svg>
                                        <div class="flex-1">
                                            <input type="text"
                                                wire:model="newData.{{ $fld }}"
                                                class="block w-full px-3 py-2 text-xs border border-indigo-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                                                placeholder="New value">
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <!-- Submit Button -->
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