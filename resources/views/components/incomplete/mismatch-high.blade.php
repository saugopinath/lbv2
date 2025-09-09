<div x-data="{ selected: '' }" class="mt-4">
    <div class="grid gap-6 mb-4 md:grid-cols-3 pl-4 pr-4">
        <div>
            <x-form.input name="application_id" label="Application Id" value="{{ $application_id ?? '' }}" disabled />
        </div>
        <div>
            <x-form.input name="beneficiary_name" label="Beneficiary Name" value="{{ $beneficiary_name ?? '' }}"
                disabled />
        </div>
        <div>
            <x-form.input name="mobile_no" label="Mobile No" value="{{ $mobile_no ?? '' }}" disabled />
        </div>
        <div>
            <x-form.input name="father_name" label="Father's Name" value="{{ $father_name ?? '' }}" disabled />
        </div>
        <div>
            <x-form.input name="ifscode" label="IFSC Code" value="{{ $ifscode ?? '' }}" disabled />
        </div>
        <div>
            <x-form.input name="bankname" label="Bank Name" value="{{ $bankname ?? '' }}" disabled />
        </div>
        <div>
            <x-form.input name="bankbranchname" label="Branch Name" value="{{ $bankbranchname ?? '' }}" disabled />
        </div>
        <div>
            <x-form.input name="new_bank_account" label="Existing Bank Account Number"
                value="{{ $new_bank_account ?? '' }}" disabled />
        </div>
    </div>

    {{-- Name Matching Info --}}
    <div class="mt-6 grid gap-4 md:grid-cols-3 pl-4 pr-4 text-center">
        <div>
            <label class="block text-gray-700 font-medium">Name As In Portal : {{ $name_as_in_portal ?? 'N/A' }}</label>
        </div>

        <div>
            <label class="block text-gray-700 font-medium">Name Response For Bank :
                {{ $name_response_for_bank ?? 'N/A' }}</label>
        </div>

        <div>
            <label class="block text-gray-700 font-medium">Name Matching Score :
                {{ $name_matching_score ?? 'N/A' }}</label>
        </div>
    </div>

    {{-- Radio Buttons --}}
    <div class="flex gap-6 mt-6 pl-4 pr-4">
        <label class="flex items-center space-x-2">
            <input type="radio" class="form-radio text-blue-600"
                wire:model="formData.bank_action.{{ $item->id }}" value="1" x-model="selected" />
            <span>KEEP SAME</span>
        </label>

        <label class="flex items-center space-x-2">
            <input type="radio" class="form-radio text-blue-600"
                wire:model="formData.bank_action.{{ $item->id }}" value="2" x-model="selected" />
            <span>CHANGE</span>
        </label>
    </div>
</div>
