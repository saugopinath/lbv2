<div>
    <form wire:submit.prevent="save">
        <x-form.input name="state" label="State" wire:model="stateName" required disabled />
        <livewire:filter-lgd-master :login_type="'state_office'" :selectedDistrict="$selectedDistrict"
        :selectedRuralurban="$selectedRuralurban"
        :selectedBlockurban="$selectedBlockurban"
        :selectedGpWard="$selectedGpWard" />
        <x-form.input name="police_station" label="Police Station" wire:model="policestation" required />
        <x-form.input name="vill_town_city" label="Village/Town/City" wire:model="villtowncity" required />
        <x-form.input name="house_premise_no" label="House / Premise No." wire:model="housepremiseno" />
        <x-form.input name="post_office" label="Post Office" wire:model="postoffice" required />
        <x-form.input name="pin_code" label="Pin Code" wire:model="pincode" required />
        @if ($mode != '0')
        <x-button.danger wire:click="">Previous</x-button.danger>
        @endif
        <x-button.danger type="submit">
            {{ $mode == '0' ? 'Save' : 'Save & Next' }}
        </x-button.danger>
    </form>
</div>