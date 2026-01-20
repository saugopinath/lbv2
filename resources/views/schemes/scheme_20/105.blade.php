<div class="mt-4 space-y-3">

<div class="flex items-start gap-2">
    <x-form.checkbox name="resident" value="1" label="I am a resident of West Bengal" wire:model="formData.resident"
    />
</div>
<div class="flex items-start gap-2">
    <x-form.checkbox name="no_govt_salary" value="1" label="I do not earn any monthly remuneration from any regular Government job" wire:model="formData.no_govt_salary"
    />
</div>
<div class="flex items-start gap-2">
    <x-form.checkbox name="info_true" value="1" label="That all the information and documents submitted by me are correct / genuine. In case any of the information/ document is found to be false, penal action shall be taken against me and the benefit will be terminated." wire:model="formData.info_true"
    />
</div>
<div class="flex items-start gap-2">
    <x-form.checkbox name="aadhaar_consent" value="1" label="I give consent to the use of the Aadhaar No. for authenticating my identity for social security pension (In case Aadhaar no. provided by the applicant)." wire:model="formData.aadhaar_consent"
    />
</div>
</div>