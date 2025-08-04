<div>
    <form wire:submit.prevent="save">
        <x-form.checkbox 
            name="resident" 
            wire:model="resident" 
            value="1" 
            label="I am a resident of West Bengal" 
            required 
        />

        <x-form.checkbox 
            name="no_govt_salary" 
            wire:model="no_govt_salary" 
            value="1" 
            label="I do not earn any monthly remuneration from any regular Government job" 
        />

        <x-form.checkbox 
            name="info_true" 
            wire:model="info_true" 
            value="1" 
            label="That all the information and documents submitted by me are correct / genuine. In case any of the information/ document is found to be false, penal action shall be taken against me and the benefit will be terminated." 
            required 
        />

        <x-form.checkbox 
            name="aadhaar_consent" 
            wire:model="aadhaar_consent" 
            value="1" 
            label="I give consent to the use of the Aadhaar No. for authenticating my identity for social security pension (In case Aadhaar no. provided by the applicant)." 
            required 
        />

        @if ($mode != '0')
            <x-button.danger type="button">Previous</x-button.danger>
        @endif

        <x-button.danger type="submit">
            {{ $mode == '0' ? 'Save' : 'Preview and Submit' }}
        </x-button.danger>
    </form>
</div>
