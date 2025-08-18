<div>
    <form wire:submit.prevent="save">
        <div class="grid gap-6 mb-4 md:grid-cols-1 mt-4 pl-6 pr-4">
            <div>
                <x-form.checkbox
                    name="resident"
                    wire:model="resident"
                    value="1"
                    label="I am a resident of West Bengal" />
            </div>
            <div>
                <x-form.checkbox
                    name="no_govt_salary"
                    wire:model="no_govt_salary"
                    value="1"
                    label="I do not earn any monthly remuneration from any regular Government job" />
            </div>
            <div>
                <x-form.checkbox
                    name="info_true"
                    wire:model="info_true"
                    value="1"
                    label="That all the information and documents submitted by me are correct / genuine. In case any of the information/ document is found to be false, penal action shall be taken against me and the benefit will be terminated." />
            </div>
            <div>
                <x-form.checkbox
                    name="aadhaar_consent"
                    wire:model="aadhaar_consent"
                    value="1"
                    label="I give consent to the use of the Aadhaar No. for authenticating my identity for social security pension (In case Aadhaar no. provided by the applicant)." />
            </div>
            <div class="flex justify-between mt-4 pl-6 pr-6">
                @if ($mode != '0')
                <x-button.danger wire:click="$dispatch('goPrevious')">Previous</x-button.danger>
                @endif
                <x-button.success type="submit">
                    {{ $mode == '0' ? 'Save' : 'Preview and Submit' }}
                </x-button.success>
            </div>
    </form>
</div>