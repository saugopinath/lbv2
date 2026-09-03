<div {{-- ✅ ROOT TAG (VERY IMPORTANT) --}}>
    <div x-data="{ open: @entangle('show') }" x-show="open" x-cloak
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-90"
        x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90"
        class="fixed inset-0 flex items-center justify-center z-50 bg-black/50 bg-opacity-50 p-4">
        <div @click.away="$wire.close()"
            class="bg-white w-full max-w-5xl rounded shadow-lg overflow-hidden max-h-[90vh] flex flex-col border border-blue-500">
            
            <!-- Modal Header -->
            <div class="bg-blue-600 px-6 py-3 flex justify-between items-center text-white">
                <h2 class="text-lg font-bold">Confirm Submit</h2>
                <button wire:click="close" class="text-white hover:text-gray-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Confirmation Prompt -->
            <div class="px-6 py-3 bg-gray-50 border-b border-gray-300 flex justify-center">
                <div class="border border-gray-300 bg-white px-8 py-3 w-full text-center text-md font-medium text-gray-800 rounded">
                    Are you sure you want to submit the following details?
                </div>
            </div>

            <div class="p-6 space-y-6 text-sm text-gray-800 overflow-y-auto flex-1 bg-white">
                
                <!-- Main Details Container -->
                <div class="border border-gray-300 rounded p-6 bg-white space-y-6 shadow-sm">
                    
                    <!-- Scheme Header -->
                    <div class="flex justify-between items-center pb-4">
                        <div class="flex items-center gap-5">
                            <img src="https://c.animaapp.com/mdn4r47eB5hzlO/img/biswo-2.png" alt="Logo" class="w-16 h-16 object-contain">
                            <div class="text-center md:text-left">
                                <h2 class="text-xl font-medium text-gray-800">
                                    Government of West Bengal (Old age Pension for Farmer)
                                </h2>
                                <h3 class="text-xl font-medium text-gray-800 mt-1">
                                    {{ $schemeName }} Scheme
                                </h3>
                            </div>
                        </div>
                        <div class="flex items-center flex-shrink-0">
                            @if($applicantPhoto)
                                <img src="{{ $applicantPhoto }}" alt="Applicant Photo" class="w-24 h-24 object-cover border border-gray-300 rounded shadow-sm">
                            @else
                                <div class="w-24 h-24 bg-green-600 rounded flex items-center justify-center shadow-sm">
                                    <svg class="w-12 h-12 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif
                        </div>
                    </div> 
                    
                    @if($applicationId)
                        <p class="text-center text-sm font-semibold text-gray-800">
                            Application Id {{ $applicationId }}
                        </p>
                    @endif

                    @foreach($tabsData as $tab)
                        @if($tab['tab_code'] == '104')
                            <div class="border border-gray-300 rounded overflow-hidden mb-4 shadow-sm">
                                <div class="bg-blue-600 px-4 py-2 text-white font-semibold">
                                    {{ $tab['tab_name'] ?? 'Enclosure Details' }}
                                </div>
                                <div class="p-3 bg-white">
                                    <livewire:enclosure-list :application_id="$applicationId" :scheme_id="$schemeId" :tabCode="104"
                                        :is_page="1" wire:key="enclosure-preview-{{ $applicationId }}-104" />
                                </div>
                            </div>
                        @else
                            <div class="border border-gray-300 rounded overflow-hidden mb-4 shadow-sm">
                                <div class="bg-blue-600 px-4 py-2 text-white font-semibold">
                                    {{ $tab['tab_name'] }}
                                </div>
                                <div class="p-4 bg-white">
                                    @php
                                        // Check if this tab has an array value that indicates tabular data
                                        $hasTableData = false;
                                        $tableData = [];
                                        foreach($tab['fields'] as $field) {
                                            if(is_array($field['value']) && count($field['value']) > 0 && is_array(reset($field['value']))) {
                                                $hasTableData = true;
                                                $tableData = $field['value'];
                                                break;
                                            }
                                        }
                                    @endphp

                                    @if($hasTableData)
                                        <div class="overflow-x-auto mb-4">
                                            <table class="w-full text-left border-collapse text-xs">
                                                <thead>
                                                    <tr>
                                                        @foreach(array_keys(reset($tableData)) as $th)
                                                            <th class="border-b font-semibold py-2 px-3 text-gray-800">{{ $th }}</th>
                                                        @endforeach
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($tableData as $row)
                                                        <tr>
                                                            @foreach($row as $cell)
                                                                <td class="border-b py-2 px-3 text-gray-600">{{ $cell }}</td>
                                                            @endforeach
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif

                                    <!-- Dynamic Sectioned Grid -->
                                    @php
                                        // Group fields by section name, EXCLUDING table arrays
                                        $groupedFields = collect($tab['fields'])->filter(function($field) {
                                            return !is_array($field['value']) || empty($field['value']) || !is_array(reset($field['value']));
                                        })->groupBy(function ($field) {
                                            return $field['section_name'] ?? '';
                                        });
                                    @endphp

                                    <div class="space-y-6">
                                        @foreach($groupedFields as $sectionName => $fields)
                                            <div class="{{ $sectionName ? 'bg-gray-50 p-4 border border-gray-200 rounded' : '' }}">
                                                @if($sectionName)
                                                    <div class="mt-2 mb-4 px-3 py-2 bg-indigo-50 border-l-4 border-indigo-600 rounded">
                                                        <span class="font-bold text-indigo-700">{{ $sectionName }}</span>
                                                    </div>
                                                @endif
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                                                    @foreach($fields as $field)
                                                        @php
                                                            $fieldName = $field['field_name'] ?? '';
                                                            $fieldType = $field['field_type'] ?? '';
                                                            $label = $field['label'] ?? '';
                                                            
                                                            $isInlineDeclaration = ($tab['tab_code'] == 105 && $fieldType === 'select' && $fieldName !== 'application_type')
                                                                || in_array($fieldName, ['aadhaar_consent', 'other_scheme_beneficiary', 'has_pucca_house'])
                                                                || str_starts_with(strtolower($label), 'consent to')
                                                                || str_starts_with(strtolower($label), 'a beneficiary')
                                                                || str_starts_with(strtolower($label), 'have pucca');
                                                        @endphp
                                                        @if($isInlineDeclaration)
                                                            <div class="col-span-full bg-indigo-50/50 p-3 rounded-lg border border-indigo-100 flex items-center flex-wrap gap-1.5 text-sm text-gray-800 font-medium">
                                                                <span>I</span>
                                                                <span class="font-bold text-indigo-700 bg-indigo-100 px-2.5 py-0.5 rounded text-xs uppercase">{{ ($field['value'] !== null && $field['value'] !== '' && $field['value'] !== '-') ? $field['value'] : '(Not Selected)' }}</span>
                                                                <span>{{ $label }}</span>
                                                            </div>
                                                        @else
                                                            <div class="flex flex-col">
                                                                @if(!empty(trim($field['label'])))
                                                                    <span class="font-semibold text-gray-800 text-sm">{{ $field['label'] }}:</span>
                                                                @endif
                                                                <span class="text-sm text-gray-600">{{ ($field['value'] !== null && $field['value'] !== '' && $field['value'] !== '-') ? $field['value'] : '(Blank)' }}</span>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <!-- Footer -->
            <div class="flex justify-center p-4 border-t border-gray-200 space-x-6 bg-white">
                <button wire:click="close"
                    class="px-6 py-2 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50 font-medium shadow-sm transition-colors cursor-pointer">
                    Cancel
                </button>
                <div class="px-6 py-2 bg-[#198754] text-white rounded hover:bg-green-700 font-medium transition-colors cursor-pointer">
                    <x-form.confirm-action action="confirmSubmit" title="Final Submit"
                        message="Are you sure to submit this application?" confirmLabel="Yes, Submit">
                        Submit
                    </x-form.confirm-action>
                </div>
            </div>

        </div>
    </div>
</div>