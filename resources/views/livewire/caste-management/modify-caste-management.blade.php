<div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
    <div>
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                    <h2 class="text-sm font-semibold text-gray-700 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Search Beneficiary
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
                        <thead class="bg-gradient-to-r from-indigo-600 to-indigo-700">
                            <tr>
                                <th class="px-5 py-4 text-center text-xs font-semibold text-white uppercase tracking-wider border-r border-indigo-400">Application ID</th>
                                <th class="px-5 py-4 text-center text-xs font-semibold text-white uppercase tracking-wider border-r border-indigo-400">Applicant Name</th>
                                <th class="px-5 py-4 text-center text-xs font-semibold text-white uppercase tracking-wider border-r border-indigo-400">Current Caste</th>
                                <th class="px-5 py-4 text-center text-xs font-semibold text-white uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @foreach($items as $index => $row)
                            <tr class="transition-all hover:bg-indigo-50">
                                <td class="px-5 py-4 text-center">
                                    <span class="text-sm font-medium text-indigo-700">{{ $row['application_id'] }}</span>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span class="text-sm font-medium text-gray-900">{{ $row['applicant_name'] }}</span>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span class="text-sm text-gray-700 font-semibold">{{ $row['caste_name'] }}</span>
                                    <div class="text-xs text-gray-500 font-mono">{{ $row['caste_no'] }}</div>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <button
                                        type="button"
                                        wire:click="selectBeneficiary({{ $row['application_id'] }})"
                                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
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

            @if($beneficiary)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden animate-fadeIn">
                <div class="p-5 space-y-6">
                    {{-- Added Tab-wise Application View from original blade --}}
                    <div class="mb-4">
                        @livewire('application-details.tab-wise-application-view', [
                        'id' => $beneficiary->application_id,
                        'schemeId' => $moduleSchemeId,
                        'allowedTabCodes' => [101]
                        ])
                    </div>

                    <div class="pb-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">{{ $beneficiary->beneficiary_name }}</h3>
                        <p class="text-sm text-gray-500">
                            Application ID: {{ $beneficiary->application_id }} |
                            Current Caste: {{ \App\Helpers\FormOptionHelper::label('Caste', $beneficiary->caste) }}
                        </p>
                    </div>

                    <div class="space-y-6">
                        <div class="rounded-xl border border-gray-200 p-4">
                            <h4 class="text-sm font-semibold text-gray-800 mb-4">Modify Caste Details</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Original Caste</label>
                                        <input type="text" value="{{ \App\Helpers\FormOptionHelper::label('Caste', $oldData['caste']) ?? 'N/A' }}" class="block w-full px-3 py-2 text-sm bg-gray-100 border border-gray-200 rounded-md text-gray-500" readonly>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Original Certificate No</label>
                                        <input type="text" value="{{ $oldData['caste_certificate_no'] ?? 'N/A' }}" class="block w-full px-3 py-2 text-sm bg-gray-100 border border-gray-200 rounded-md text-gray-500" readonly>
                                    </div>
                                </div>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">New Caste</label>
                                        <select wire:model.live="newData.caste" class="block w-full px-3 py-2 text-sm border border-indigo-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="">Select New Caste</option>
                                            @foreach($casteOptions as $id => $name)
                                                @if($id != $oldData['caste'])
                                                <option value="{{ $id }}">{{ $name }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                        @error('newData.caste') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                    @if($this->isSCST)
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">New Certificate No</label>
                                        <input type="text" wire:model.live="newData.caste_certificate_no" class="block w-full px-3 py-2 text-sm border border-indigo-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter new certificate number">
                                        @error('newData.caste_certificate_no') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @if($this->isSCST)
                        <div class="rounded-xl border border-gray-200 p-4 bg-gray-50" wire:key="enclosure-section-{{ $beneficiary->application_id }}">
                            <h4 class="text-sm font-semibold text-gray-800 mb-4">Upload Supporting Document</h4>
                            @livewire('enclosure-list', [
                            'application_id' => $beneficiary->application_id,
                            'doc_type_id_array_list' => $doctype,
                            'scheme_id' => $moduleSchemeId,
                            'enclosureSource' => '5'
                            ])
                            @error('document_upload') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        @endif

                        <div class="flex justify-between items-center pt-4 border-t border-gray-200 mt-4">
                            <div class="flex items-center text-xs text-gray-500 italic">
                                <svg class="w-4 h-4 mr-1 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                                Make sure to upload documents before submitting for SC/ST.
                            </div>
                            <button wire:click="submitRequest" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                                </svg>
                                Submit Update Request
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>