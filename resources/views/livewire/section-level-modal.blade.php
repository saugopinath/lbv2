<div>
    @if($show)
        <div class="fixed inset-0 z-[60] bg-black/70 flex items-center justify-center p-4">

            <div class="bg-white rounded-xl shadow-lg w-full max-w-lg overflow-hidden">

                {{-- HEADER --}}
                <div class="bg-indigo-100 px-6 py-4 font-semibold border-b">
                    Add Section / Level
                </div>

                {{-- BODY --}}
                <div class="p-6 space-y-4">

                    <x-form.select name="slType" label="Type" wire:model.live="slType">
                        <option value="">Select Type</option>
                        <option value="0">Section</option>
                        <option value="1">Label</option>
                    </x-form.select>

                    @if($slType === '0')
                        <x-form.input name="slName" label="Section Name" wire:model.live="slName" />
                        <x-form.input name="slShortName" label="Short Name" wire:model="slShortName" readonly />
                    @endif

                    @if($slType === '1')
                        <x-form.input name="slName" label="Label Name" wire:model.live="slName" />
                        <x-form.input name="slShortName" label="Short Name" wire:model="slShortName" readonly />
                    @endif

                </div>

                {{-- FOOTER --}}
                <div class="flex justify-end gap-3 px-6 py-4 border-t">

                    <x-button.primary wire:click="close" class="bg-gray-500">
                        Cancel
                    </x-button.primary>

                    <x-button.primary wire:click="save" class="bg-indigo-600">
                        Save
                    </x-button.primary>

                </div>

            </div>
        </div>
    @endif
</div>
