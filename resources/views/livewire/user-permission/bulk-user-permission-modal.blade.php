<div>
    @if($isOpen)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl w-full sm:w-3/4 md:w-1/2 lg:w-2/5 max-h-[90vh] flex flex-col">
            {{-- Header --}}
            <div class="px-6 py-4 border-b">
                <h2 class="text-lg font-semibold mb-4">Assign Or Remove Permissions to Selected Users</h2>
            </div>

            {{-- Body (scrollable) --}}
            <div class="flex-1 overflow-y-auto px-6 py-4">
                <form wire:submit.prevent="save" class="flex flex-col h-full">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                        @foreach($permissions as $id => $name)
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" wire:model="selectedPermissions" value="{{ $id }}" class="rounded" />
                            <span>{{ $name }}</span>
                        </label>
                        @endforeach
                    </div>
                </form>
            </div>

            {{-- Footer --}}
            <div class="px-6 py-4 border-t flex justify-end space-x-2">
                 <button type="button" wire:click="close"
                    class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                    Cancel
                </button>
                <x-button.primary type="button" wire:click="save"
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Assign
                </x-button.primary>
                 <button type="button" wire:click="remove"
                    class="px-4 py-2 bg-red-500 rounded hover:bg-red-600">
                    Remove
                </button>
            </div>
        </div>
    </div>
    @endif
</div>