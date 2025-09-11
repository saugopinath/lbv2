{{--  @if (!empty($aadhaarIssues))
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
@endif  --}}
{{--  @if (!empty($aadhaarIssues))
    <div class="p-4 mb-4 border rounded-lg bg-gray-50 shadow-sm">
        <h2 class="font-semibold text-lg text-blue-700 mb-2">Aadhaar Related Issues</h2>
        <ul class="list-disc list-inside text-sm text-gray-700 mb-2">
            @foreach ($aadhaarIssues as $issueItem)
                <li>{{ $issueItem->incompletType->name }}</li>
            @endforeach
        </ul>
        <p class="text-sm text-gray-600">Old Aadhaar: {{ $aadhaarIssues[0]->old_value ?? 'N/A' }}</p>
        <x-form.input
            id="aadhar_{{ $aadhaarIssues[0]->id }}"
            name="aadhar[{{ $aadhaarIssues[0]->id }}]"
            label="New Aadhaar Number"
            required
            wire:model="formData.aadhar.{{ $aadhaarIssues[0]->id }}"
            placeholder="Enter New Aadhaar Number"
            x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0,12)"
        />
        <livewire:enclosure-list
            :application_id="$aadhaarIssues[0]->application_id"
            :doc_type_id_array_list="[108]"
            enclosureSource="5"
        />
        @error("formData.aadhar.{$aadhaarIssues[0]->id}")
            <span class="text-red-600 text-sm">{{ $message }}</span>
        @enderror
    </div>
@endif  --}}
<div class="p-4 mb-4 border rounded-lg bg-gray-50 shadow-sm">
    <h2 class="font-semibold text-lg text-blue-700 mb-2">Aadhaar Related Issues</h2>
    @foreach ($aadhaarIssues as $issueItem)
        <div class="mb-4">
            <h3 class="font-medium text-gray-700">{{ $issueItem->incompletType->name }}</h3>
            <p class="text-sm text-gray-600 mb-2">
                Old Aadhaar: {{ $issueItem->old_value ? Crypt::decryptString($issueItem->old_value) : 'N/A' }}
            </p>
            <x-form.input
                id="{{ $issueItem->incompletType->name === 'PDS MISMATCH' ? 'pds' : ($issueItem->incompletType->name === 'NO AADHAR NUMBER' ? 'aadhar' : 'new_aadhar') }}_{{ $issueItem->id }}"
                name="{{ $issueItem->incompletType->name === 'PDS MISMATCH' ? 'pds' : ($issueItem->incompletType->name === 'NO AADHAR NUMBER' ? 'aadhar' : 'new_aadhar') }}[{{ $issueItem->id }}]"
                label="New Aadhaar Number"
                required
                wire:model="formData.{{ $issueItem->incompletType->name === 'PDS MISMATCH' ? 'pds' : ($issueItem->incompletType->name === 'NO AADHAR NUMBER' ? 'aadhar' : 'new_aadhar') }}.{{ $issueItem->id }}"
                placeholder="Enter New Aadhaar Number"
                x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0,12)"
            />
            @error("formData.{{ $issueItem->incompletType->name === 'PDS MISMATCH' ? 'pds' : ($issueItem->incompletType->name === 'NO AADHAR NUMBER' ? 'aadhar' : 'new_aadhar') }}.{$issueItem->id}")
                <span class="text-red-600 text-sm">{{ $message }}</span>
            @enderror
            <livewire:enclosure-list
                :application_id="$issueItem->application_id"
                :doc_type_id_array_list="[108]"
                enclosureSource="5"
                :wire:key="'enclosure-'.$issueItem->id"
            />
        </div>
    @endforeach
</div>
