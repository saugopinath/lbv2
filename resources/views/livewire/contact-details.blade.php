<div>
    <x-form.input name="state" label="State" required disabled />
    <x-form.input name="police_station" label="Police Station" required />
    <x-form.input name="vill_town_city" label="Village/Town/City" required />
    <x-form.input name="house_premise_no" label="House / Premise No." />
    <x-form.input name="post_office" label="Post Office" required />
    <x-form.input name="pin_code" label="Pin Code" required />
    @if ($mode != '0')
    <x-button.danger wire:click="">Previous</x-button.danger>
    @endif
    <x-button.danger>
        {{ $mode == '0' ? 'Save' : 'Save & Next' }}
    </x-button.danger>
</div>