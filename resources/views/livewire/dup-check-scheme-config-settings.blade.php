<div class="bg-white shadow rounded-xl p-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-xl font-bold text-indigo-700">Dup Check Scheme Config</h1>
    </div>
    <form wire:submit.prevent="save" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <x-form.label label="Duplicate Check With" name="duplicate_check_with" />
            <div class="grid grid-cols-3">
                @foreach ($dupcheckOptions as $key => $label)
                    <div wire:key="dup-opt-{{ $key }}">
                        <x-form.checkbox name="selecteddupcheckOptions" wire:model.live="selecteddupcheckOptions"
                            :value="$key" :label="$label" />
                    </div>
                @endforeach
            </div>
        </div>
        @if (count($schemeOptions) > 0)
            <div>
                <label class="font-semibold block mb-1">
                    Is Cross?
                </label>
                <div class="flex gap-6">
                    <x-form.radio name="iscross" value="yes" label="Yes" wire:model.live="iscross" />
                    <x-form.radio name="iscross" value="no" label="No" wire:model.live="iscross" />
                </div>
            </div>
            @if ($iscross === 'yes')
                <x-form.multiselect label="Schemes" wire:model="schemes" :options="$schemeOptions" required />
            @endif
        @endif
        <div class="md:col-span-2 mt-6 pt-6 border-t">
            <x-button.loading-button action="save" text="Save"
                class="w-full md:w-auto px-8 py-3 bg-indigo-600 hover:bg-indigo-700" />
        </div>
    </form>
</div>
