<div class="bg-white shadow rounded-xl p-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-xl font-bold text-indigo-700">Age Management</h1>
    </div>
    <form wire:submit.prevent="save" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="grid gap-6 md:grid-cols-2 mb-2 pl-4 pr-4">
            <div>
                <x-form.input id="minage" name="minage" label="Minimum Age" required wire:model.defer="minage"
                    x-on:input="
                    $el.value = $el.value.replace(/[^0-9]/g, '').slice(0,3);
                    $wire.set('minage', $el.value);
                " />
            </div>
            <div>
                <x-form.input id="maxage" name="maxage" label="Maximum Age" required wire:model.defer="maxage"
                    x-on:input="
                    $el.value = $el.value.replace(/[^0-9]/g, '').slice(0,3);
                    $wire.set('maxage', $el.value);
                " />
            </div>
        </div>
        <div class="grid gap-6 md:grid-cols-2 mb-2 pl-4 pr-4">
            <div>
                <label class="font-semibold block mb-1">
                    Is Special?
                </label>
                <div class="flex gap-6">
                    <x-form.radio name="isspecial" value="yes" label="Yes" wire:model.live="isspecial" />
                    <x-form.radio name="isspecial" value="no" label="No" wire:model.live="isspecial" />
                </div>
            </div>
            @if ($isspecial === 'yes')
                <x-form.select name="specialcase" id="specialcase" label="Special Case" required wire:model="specialcase">
                    @foreach ($specialcaseOptions as $specialcaseOption)
                    <option value="{{ $specialcaseOption->id }}">{{ $specialcaseOption->name }}</option>
                    @endforeach
                </x-form.select>
            @endif
        </div>
        <div class="md:col-span-2 mt-6 pt-6 border-t">
            <x-button.loading-button action="save" text="Save"
                class="w-full md:w-auto px-8 py-3 bg-indigo-600 hover:bg-indigo-700" />
        </div>
    </form>
</div>
