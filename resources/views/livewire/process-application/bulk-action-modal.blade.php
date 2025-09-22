<x-modal wire:model="bulkActionModal">





    <x-slot name="body">
        <div class="space-y-4">
            <x-form.select wire:model.live="bulkActionType" class="w-full border rounded p-2" label="Select Operation" name="bulkActionType" required>
                <option value="">Select Operation</option>
                @foreach ($availableActions as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </x-form.select>

            @if (in_array($bulkActionType, ['R', 'T']))
                <x-form.select wire:model="reason" class="w-full border rounded p-2" label="Reason" name="reason" required>
                    <option value="">Select Reason</option>
                    @foreach ($reasons as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </x-form.select>

                <x-form.input type="textarea" wire:model="remark" placeholder="Enter remark" name="remark" label="Remark" class="w-full border rounded p-2" required></x-form.input>
            @endif
        </div>
    </x-slot>


    <x-slot name="actions">
        <div class="flex justify-end gap-2">


            <x-button.danger wire:click="$set('bulkActionModal', false)">
                Cancel
            </x-button.danger>
            <x-button.primary wire:click="performBulkAction" wire-target="performBulkAction">
                Confirm
            </x-button.primary>
        </div>
    </x-slot>

</x-modal>
