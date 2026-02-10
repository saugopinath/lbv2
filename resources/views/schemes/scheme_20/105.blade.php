<div class='space-y-6'><div class='w-full'><div class="pl-0">
    <x-form.checkbox
        name="resident"
        label="I am the resident of the west Bengal"
        value="1"
        wire:model.live="formData.resident"
    />
</div></div><div class='w-full'><div class="pl-0">
    <x-form.checkbox
        name="no_govt_salary"
        label="I don't get any type of the govt salary"
        value="1"
        wire:model.live="formData.no_govt_salary"
    />
</div></div></div>