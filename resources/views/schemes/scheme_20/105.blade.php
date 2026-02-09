<div class='space-y-6'><div class='w-full'><div class="pl-0">
    <x-form.checkbox
        name="is_resident"
        label="I am the resident of the West Bengal."
        value="1"
        wire:model.live="formData.is_resident"
    />
</div></div><div class='w-full'><div class="pl-0">
    <x-form.checkbox
        name="no_govt_salary"
        label="I am not got any type of Govt. Salary"
        value="1"
        wire:model.live="formData.no_govt_salary"
    />
</div></div><div class="mt-6 mb-2 px-3 py-2 bg-indigo-50 border-l-4 border-indigo-600 rounded">
    <span class="font-semibold text-indigo-700">Nominee</span>
</div><div class='grid grid-cols-1 md:grid-cols-3 gap-5'><div class="pl-6">
    <x-form.input
        type="text"
        name="nominee_name"
        label="Nominee Name"
        wire:model.live="formData.nominee_name"
    />
</div><div class="pl-6">
    <x-form.input
        type="text"
        name="nominee_address"
        label="Nominee Address"
        wire:model.live="formData.nominee_address"
    />
</div><div class="pl-6">
    <x-form.input
        type="text"
        name="nominee_mobile"
        label="Nominee Mobile No."
        wire:model.live="formData.nominee_mobile"
    />
</div></div><div class="mt-6 mb-2 px-3 py-2 bg-indigo-50 border-l-4 border-indigo-600 rounded">
    <span class="font-semibold text-indigo-700">Applicant Details</span>
</div><div class='grid grid-cols-1 md:grid-cols-3 gap-5'><div class="pl-6">
    <x-form.input
        type="text"
        name="applicant name "
        label="Full Name"
        wire:model.live="formData.applicant name "
    />
</div><div class="pl-6">
    <x-form.input
        type="text"
        name="Applicantion_address"
        label="Applicantion Address"
        wire:model.live="formData.Applicantion_address"
    />
</div></div></div>