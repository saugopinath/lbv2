<div>
    @if ($open)
    <div class="fixed inset-0 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded shadow max-w-md w-full">
            <h2 class="text-lg font-bold">Confirm {{ ucfirst($action) }}</h2>
            <form wire:submit.prevent="confirm">
                <x-form.input type="textarea" wire:model.defer="reason" placeholder="Enter reason" id="reason"
                    name="reason"
                    label="Reason" required />
                <div class="flex space-x-2 mt-4">
                    <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded">
                        Yes, {{ ucfirst($action) }}
                    </button>
                    <button type="button" wire:click="close" class="bg-gray-300 px-4 py-2 rounded">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>