@if (!empty($aadhaarIssues))
    <div class="p-4 mb-4 border rounded-lg bg-gray-50 shadow-sm">
        <h2 class="font-semibold text-lg text-blue-700 mb-2">
            Aadhaar Related Issues
        </h2>

        <ul class="list-disc list-inside text-sm text-gray-700 mb-2">
            @foreach ($aadhaarIssues as $issueItem)
                <li>{{ $issueItem->incompletType->name }}</li>
            @endforeach
        </ul>

        <x-form.input
            id="aadhar_modification_{{ $aadhaarIssues[0]->application_id }}"
            name="aadhar_modification[{{ $aadhaarIssues[0]->application_id }}]"
            label="Aadhaar Number"
            required
            wire:model="formData.aadhar_modification.{{ $aadhaarIssues[0]->application_id }}"
            placeholder="Enter New Aadhaar Number"
            x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0,12)"
        />

        <livewire:enclosure-list
            :application_id="$aadhaarIssues[0]->application_id"
            :doc_type_id_array_list="[108]"
            enclosureSource="5"
        />

        @error("formData.aadhar_modification.{$aadhaarIssues[0]->application_id}")
            <span class="text-red-600 text-sm">{{ $message }}</span>
        @enderror
    </div>
@endif
