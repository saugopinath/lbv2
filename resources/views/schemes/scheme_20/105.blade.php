<div class='space-y-6'><div class='w-full'><div class="pl-0">
    <x-form.checkbox
        name="resident"
        label="I am a resident of West Bengal"
        value="1"
        wire:model.live="formData.resident"
    />
</div></div><div class='w-full'><div class="pl-0">
    <x-form.checkbox
        name="no_govt_salary"
        label="I do not earn any monthly remuneration from any regular Government job"
        value="1"
        wire:model.live="formData.no_govt_salary"
    />
</div></div><div class='w-full'><div class="pl-0">
    <x-form.checkbox
        name="info_true"
        label="That all the information and documents submitted by me are correct / genuine. In case any of the information/ document is found to be false, penal action shall be taken against me and the benefit will be terminated."
        value="1"
        wire:model.live="formData.info_true"
    />
</div></div><div class='w-full'><div class="pl-0">
    <x-form.checkbox
        name="aadhaar_consent"
        label="I give consent to the use of the Aadhaar No. for authenticating my identity for social security pension (In case Aadhaar no. provided by the applicant)."
        value="1"
        wire:model.live="formData.aadhaar_consent"
    />
</div></div></div>