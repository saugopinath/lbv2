<div class='mt-4 space-y-4'><div   >
        <x-form.checkbox
        name="is_resident"
        value="1"
        label="I am the resident of the west bengal."
        wire:model.live="formData.is_resident"
    />

</div><div   >
        <x-form.checkbox
        name="no_salary"
        value="1"
        label="I am not get any govt. Salary"
        wire:model.live="formData.no_salary"
    />

</div><div   >
        <x-form.checkbox
        name="living_year"
        value="1"
        label="I am live in West Bengal more than 25 years"
        wire:model.live="formData.living_year"
    />

</div></div>