<div>
    @if($isOpen)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl w-full sm:w-3/4 md:w-1/2 lg:w-2/5 max-h-[90vh] flex flex-col">
            {{-- Header --}}
            <div class="px-6 py-4 border-b">
                <h2 class="text-lg font-semibold">
                    Edit Permissions:
                    {{ implode(', ', (array) $selectedPermissions) ?: 'None' }}
                </h2>
                <h2 class="text-lg font-semibold">User id : {{ $userId }}</h2>
                <h2 class="text-lg font-semibold">User Role : {{ $baseuserrole }}</h2>
                <h2 class="text-lg font-semibold">User Name : {{ $userName }}</h2>
            </div>


            {{-- Footer --}}
            <div class="px-6 py-4 border-t flex justify-end space-x-2">
                <button type="button" wire:click="close"
                    class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                    Cancel
                </button>
                <button wire:click="syncbaseuserpermission"
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    SyncPermission
                </button>
            </div>
        </div>
    </div>
    @endif
</div>