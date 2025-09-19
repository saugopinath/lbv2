<div class="p-4 mb-4 border rounded-lg bg-gray-50 shadow-sm">
    <h2 class="font-semibold text-lg text-blue-700 mb-2">
        Aadhaar Related Issues
    </h2>

    <ul class="list-disc list-inside text-sm text-gray-700 mb-2">
        @foreach ($aadhaarIssues as $issueItem)
            <li>{{ $issueItem->incompletType->name }}</li>
        @endforeach
    </ul>

    <p class="text-sm text-gray-600">Old Aadhaar: {{ $item->old_value ?? 'N/A' }}</p>

    <x-form.input
        id="aadhar_modification_{{ $aadhaarIssues[0]->application_id }}"
        name="aadhar_modification[{{ $aadhaarIssues[0]->application_id }}]"
        label="Aadhaar Number"
        required
        wire:model.live="formData.aadhar_modification.{{ $aadhaarIssues[0]->application_id }}"
        placeholder="Enter New Aadhaar Number"
        x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0,12)"
    />

    <div class="flex gap-6 mt-3">
        {{-- Previous Approved Document --}}
        {{-- <div class="w-1/2">
            <h3 class="font-semibold mb-2">Previous Approved Document</h3>
            <livewire:enclosure-list
                :application_id="$aadhaarIssues[0]->application_id"
                :doc_type_id_array_list="[108]"
                :is_page="1"
                :key="'previous-' . $aadhaarIssues[0]->application_id"
            />
        </div> --}}

        {{-- Newly Temp Document --}}
        <div class="w-1/2">
            <h3 class="font-semibold mb-2">Newly Temp Document</h3>
            <livewire:enclosure-list
                :application_id="$aadhaarIssues[0]->application_id"
                :doc_type_id_array_list="[108]"
                enclosureSource="5"
                :key="'new-' . $aadhaarIssues[0]->application_id"
            />
        </div>
    </div>

    @error('duplicate_check')
        <span class="text-red-600 text-sm">{{ $message }}</span>
    @enderror

    @error("formData.aadhar_modification.".$aadhaarIssues[0]->application_id)
        <span class="text-red-600 text-sm">{{ $message }}</span>
    @enderror

    <div class="mt-4">
        <x-button.primary wire:click="submit">
            Save Aadhaar
        </x-button.primary>
    </div>

    @if (session()->has('success'))
        <div class="text-green-600 mt-2">
            {{ session('success') }}
        </div>
    @endif
</div>
