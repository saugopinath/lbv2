<div>
    <form wire:submit.prevent="save">
        <div class="grid gap-6 mb-2 md:grid-cols-3 pl-4 pr-4">
            <div>
                <x-form.input name="state" label="State" wire:model="stateName" required disabled />
            </div>
        </div>
        <livewire:filter-lgd-master :login_type="'state_office'" :selectedDistrict="$selectedDistrict"
            :selectedRuralurban="$selectedRuralurban"
            :selectedBlockurban="$selectedBlockurban"
            :selectedGpWard="$selectedGpWard" />
        <div class="grid gap-6 mb-2 md:grid-cols-3 pl-4 pr-4">
            <div>
                <x-form.input name="police_station" label="Police Station" wire:model="policestation" required />
            </div>
            <div>
                <x-form.input name="vill_town_city" label="Village/Town/City" wire:model="villtowncity" required />
            </div>
            <div>
                <x-form.input name="house_premise_no" label="House / Premise No." wire:model="housepremiseno" />
            </div>
            <div>
                <x-form.input name="post_office" label="Post Office" wire:model="postoffice" required />
            </div>
            <div>
                <x-form.input name="pin_code" label="Pin Code" wire:model="pincode" required />
            </div>
        </div>
        <div class="flex justify-between mt-4 pl-6 pr-6">
            @if ($mode != '0')
            <x-button.danger wire:click="$dispatch('goPrevious')">Previous</x-button.danger>
            @endif
            <x-button.primary type="submit">
                {{ $mode == '0' ? 'Save' : 'Save & Next' }}
            </x-button.primary>
        </div>
    </form>
</div>