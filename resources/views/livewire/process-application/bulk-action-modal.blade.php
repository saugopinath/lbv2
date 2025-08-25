<x-modal wire:model="bulkActionModal">


    <x-slot name="title">
        Select Operation
    </x-slot>


    <x-slot name="body">
        <div class="space-y-4">
            <select wire:model.live="bulkActionType" class="w-full border rounded p-2">
                <option value="">Select Operation</option>
                @foreach ($availableActions as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>

            @if (in_array($bulkActionType, ['R', 'T']))
                <select wire:model="reason" class="w-full border rounded p-2">
                    <option value="">Select Reason</option>
                    @foreach ($reasons as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>

                <textarea wire:model="remark" placeholder="Enter remark" class="w-full border rounded p-2"></textarea>
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
