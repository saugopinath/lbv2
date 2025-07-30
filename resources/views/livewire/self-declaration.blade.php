<div>
    <x-form.checkbox name="ok" value="1" label="I am a resident of West Bengal" />
    <x-form.checkbox name="ok" value="2" label="I do not earn any monthly remuneration from any regular Government job" />
    <x-form.checkbox name="ok" value="3" label="That all the information and documents submitted by me are correct / genuine. In case any of the information/ document is found to be false, penal action shall be taken against me and the benefit will be terminated." />
    <x-form.checkbox name="ok" value="4" label="I give consent to the use of the Aadhaar No. for authenticating my identity for social security pension (In case Aadhaar no. provided by the applicant)." />
    @if ($mode != '0')
    <x-button.danger>Previous</x-button.danger>
    @endif
    <x-button.danger>
        {{ $mode == '0' ? 'Save' : 'Preview and Submit' }}
    </x-button.danger>
</div>