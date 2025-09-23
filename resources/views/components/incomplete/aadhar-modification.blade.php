    {{--  <div class="p-4 mb-4 border rounded-lg bg-gray-50 shadow-sm">
        <h2 class="font-semibold text-lg text-blue-700 mb-2">
            Aadhaar Related Issues
        </h2>

        <ul class="list-disc list-inside text-sm text-gray-700 mb-2">
            @foreach ($aadhaarIssues as $issueItem)
                <li>{{ $issueItem->incompletType->name }}</li>
            @endforeach
        </ul>
        <p class="text-sm text-gray-600">Old Aadhaar: {{ $issueItem->old_value['aadhaar_no'] ?? 'N/A' }}
        </p>
        <div>  --}}

    {{--  <x-form.input id="aadhar_modification_{{ $aadhaarIssues[0]->application_id }}" name="aadhar_modification"
                label="Aadhaar Number" required placeholder="Enter New Aadhaar Number"
                wire:model="formData.aadhar_modification.{{ $aadhaarIssues[0]->application_id }}"
                x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0,12)" />  --}}

    {{--  <x-form.input id="aadhar_modification_{{ $aadhaarIssues[0]->application_id }}" name="aadhar_modification"
                label="Aadhaar Number" required placeholder="Enter New Aadhaar Number"
                value="{{ old('aadhar_modification', $issueItem->new_value['aadhaar_no'] ?? '') }}"
                x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0,12)" />


            @if ($errors->has('aadhaar'))
                <span class="text-red-600 text-sm">
                    <li>{{ $errors->first('aadhaar') }}</li>
            @endif
        </div>

        <div class="flex gap-6">
            <div class="w-1/2">
                <h3 class="font-semibold mb-2">Newly Temp Document</h3>

                <livewire:enclosure-list :application_id="$aadhaarIssues[0]->application_id" :doc_type_id_array_list="[108]" enclosureSource="5" :key="'new-' . $aadhaarIssues[0]->application_id" />

                {{-- Error --}}
    {{--  @error('document_upload')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>  --}}
    {{--  </div>  --}}


    {{--
        @error('duplicate_check')
            <span class="text-red-600 text-sm">{{ $message }}</span>
        @enderror  --}}
    {{--  </div>  --}}
    <div class="p-4 mb-4 border rounded-lg bg-gray-50 shadow-sm">
        <h2 class="font-semibold text-lg text-blue-700 mb-2">
            Aadhaar Related Issues
        </h2>

        <ul class="list-disc list-inside text-sm text-gray-700 mb-2">
            @foreach ($aadhaarIssues as $issueItem)
                <li>{{ $issueItem->incompletType->name }}</li>
            @endforeach
        </ul>

        <p class="text-sm text-gray-600">
            <strong>Old Aadhaar:</strong> {{ $issueItem->old_value['aadhaar_no'] ?? 'N/A' }}
        </p>

        <div>
            @if (request()->has('stage') && in_array(decrypt(request()->get('stage')), ['verifier', 'revert']))
                <x-form.input id="aadhar_modification_{{ $aadhaarIssues[0]->application_id }}" name="aadhar_modification"
                    label="Aadhaar Number" required placeholder="Enter New Aadhaar Number"
                    wire:model.defer="formData.aadhar_modification.{{ $aadhaarIssues[0]->application_id }}"
                    {{--  value="{{ old('aadhar_modification', $issueItem->new_value['aadhaar_no'] ?? '') }}"  --}}
                    x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0,12)" />

            @elseif (request()->has('stage') && decrypt(request()->get('stage')) === 'approver')
                <p class="mt-2 text-gray-700">
                    <strong>New Aadhaar:</strong> {{ $issueItem->new_value['aadhaar_no'] ?? 'N/A' }}
                </p>
            @endif

            {{-- Error Message --}}
            @if ($errors->has('aadhaar'))
                <span class="text-red-600 text-sm">
                    <li>{{ $errors->first('aadhaar') }}</li>
                </span>
            @endif
        </div>

        <div class="flex gap-6 mt-4">
            <div class="w-1/2">
                <h3 class="font-semibold mb-2">Newly Temp Document</h3>
                <livewire:enclosure-list :application_id="$aadhaarIssues[0]->application_id" :doc_type_id_array_list="[108]" enclosureSource="5" :key="'new-' . $aadhaarIssues[0]->application_id" />

                {{-- Document error --}}
                @error('document_upload')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>
