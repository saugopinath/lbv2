<div>
    <form wire:submit.prevent="save">
        <div class="grid gap-6 mb-2 md:grid-cols-3 pl-4 pr-4">
            <div>
                <x-form.input name="state" label="State" wire:model="stateName" required disabled />
            </div>
        </div>
        <livewire:filter-lgd-master-entry :login_type="'state_office'" :selectedDistrict="$selectedDistrict"
            :selectedRuralurban="$selectedRuralurban"
            :selectedBlockurban="$selectedBlockurban"
            :selectedGpWard="$selectedGpWard" />
        <div class="grid gap-6 mb-2 md:grid-cols-3 pl-4 pr-4">
            <div>
                <x-form.input name="policestation" label="Police Station" wire:model="policestation" required x-on:input="$el.value = $el.value.replace(/[^A-Za-z\s]/g, '')" />
            </div>
            <div>
                <x-form.input name="villtowncity" label="Village/Town/City" wire:model="villtowncity" required x-on:input="$el.value = $el.value.replace(/[^A-Za-z\s]/g, '')" />
            </div>
            <div>
                <x-form.input name="housepremiseno" label="House / Premise No." wire:model="housepremiseno" />
            </div>
            <div>
                <x-form.input name="postoffice" label="Post Office" wire:model="postoffice" required x-on:input="$el.value = $el.value.replace(/[^A-Za-z\s]/g, '')" />
            </div>
            <div>
                <x-form.input name="pincode" label="Pin Code" wire:model="pincode" required x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0,6)" />
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