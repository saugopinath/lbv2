    <div class="p-4 mb-4 border rounded-lg bg-gray-50 shadow-sm">
        <h2 class="font-semibold text-lg text-blue-700 mb-2">
            Aadhaar Related Issues
        </h2>

        <ul class="list-disc list-inside text-sm text-gray-700 mb-2">
            @foreach ($aadhaarIssues as $issueItem)
                <li>{{ $issueItem->incompletType->name }}</li>
            @endforeach
        </ul>



        <p class="grid gap-6 mb-2 md:grid-cols-3 pl-4 pr-4">
            Old Aadhaar: {{ $issueItem->old_value['aadhaar_no'] ?? 'N/A' }}
        </p>

        <div>
            {{--  @dump($stage);  --}}
            @if (!empty($stage) && in_array($stage, ['verifier', 'revert']))
                <div class="grid gap-6 mb-2 md:grid-cols-3 pl-4 pr-4">
                    <x-form.input id="aadhar_modification_{{ $aadhaarIssues[0]->application_id }}"
                        name="aadhar_modification" label="Aadhaar Number" required placeholder="Enter New Aadhaar Number"
                        wire:model.defer="formData.aadhar_modification.{{ $aadhaarIssues[0]->application_id }}"
                        x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0,12)" />
                </div>
            @elseif (!empty($stage) && $stage === 'approver')
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

        <div class="grid gap-6 mb-2 md:grid-cols-3 pl-4 pr-4">
            {{-- Previous Approved Document --}}
            {{--  <div class="w-1/3">
                <h3 class="font-semibold mb-2">Previous Approved Document</h3>
                <livewire:enclosure-list :application_id="$aadhaarIssues[0]->application_id" :doc_type_id_array_list="[108]" :is_page="1" :key="'previous-' . $aadhaarIssues[0]->application_id" />
            </div>  --}}
            <div class="w-1/3">
                <h3 class="font-semibold mb-2">Newly Temp Document</h3>
                <livewire:enclosure-list :application_id="$aadhaarIssues[0]->application_id" :doc_type_id_array_list="[107]" enclosureSource="5" :key="'new-' . $aadhaarIssues[0]->application_id" />

                {{-- Document error --}}
                @error('document_upload')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>
