<div class='space-y-6'><div class='grid grid-cols-1 md:grid-cols-1 gap-5'><div class="pl-0">
    <x-form.checkbox
        name="resident"
        label="I am a resident of West Bengal"
        value="1"
        wire:model.live="formData.resident"
    />
</div></div><div class='grid grid-cols-1 md:grid-cols-1 gap-5'><div class="pl-0">
    <x-form.checkbox
        name="no_govt_salary"
        label="I do not earn any monthly remuneration from any regular Government job"
        value="1"
        wire:model.live="formData.no_govt_salary"
    />
</div></div><div class='grid grid-cols-1 md:grid-cols-1 gap-5'><div class="pl-0">
    <x-form.checkbox
        name="info_true"
        label="That all the information and documents submitted by me are correct / genuine. In case any of the information/ document is found to be false, penal action shall be taken against me and the benefit will be terminated."
        value="1"
        wire:model.live="formData.info_true"
    />
</div></div><div class='grid grid-cols-1 md:grid-cols-1 gap-5'><div class="pl-0">
    <x-form.checkbox
        name="aadhaar_consent"
        label="I give consent to the use of the Aadhaar No. for authenticating my identity for social security pension (In case Aadhaar no. provided by the applicant)."
        value="1"
        wire:model.live="formData.aadhaar_consent"
    />
</div></div><div class="mt-6 mb-2 px-3 py-2 bg-indigo-50 border-l-4 border-indigo-600 rounded">
    <span class="font-semibold text-indigo-700">sham</span>
</div><div class='grid grid-cols-1 md:grid-cols-2 gap-5'><div class="pl-6">
    <x-form.input
        type="number"
        name="number"
        label="Number"
        wire:model.live="formData.number"
    />
</div><div class="pl-6">
    <label class="block font-medium text-gray-700 mb-1">radio</label>
    <div class="flex flex-wrap gap-4">
        <label class="flex items-center gap-2">
    <input
        type="radio"
        name="radio"
        value="a"
        wire:model.live="formData.radio"
    />
    a
</label><label class="flex items-center gap-2">
    <input
        type="radio"
        name="radio"
        value="b"
        wire:model.live="formData.radio"
    />
    b
</label>
    </div>
</div></div><div class="mt-6 mb-2 px-3 py-2 bg-indigo-50 border-l-4 border-indigo-600 rounded">
    <span class="font-semibold text-indigo-700">ram</span>
</div><div class='grid grid-cols-1 md:grid-cols-3 gap-5'><div class="pl-6">
    <x-form.input
        type="text"
        name="test"
        label="test"
        wire:model.live="formData.test"
    />
</div><div class="pl-6">
    <x-form.input
        type="number"
        name="number"
        label="number"
        wire:model.live="formData.number"
    />
</div><div class="pl-6">
    <x-form.select
        name="select"
        label="select"
        wire:model.live="formData.select"
    >
        <option value="">-- Select select --</option>
        <option value="a">a</option>
<option value="b">b</option>

    </x-form.select>
</div></div><div class='grid grid-cols-1 md:grid-cols-1 gap-5'><div class="pl-6">
    <x-form.input
        type="text"
        name="date"
        label="date"
        wire:model.live="formData.date"
    />
</div></div><div class='grid grid-cols-1 md:grid-cols-1 gap-5'><div class="pl-0">
    <x-form.checkbox
        name="checkbox"
        label="checkbox"
        value="1"
        wire:model.live="formData.checkbox"
    />
</div></div></div>