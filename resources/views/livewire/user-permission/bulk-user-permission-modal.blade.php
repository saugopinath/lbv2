<div>
    @if($isOpen)
    <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl w-11/12 md:w-2/5 lg:w-1/3 max-h-[80vh] overflow-y-auto p-6">

            <h2 class="text-lg font-semibold mb-4">Assign Permissions to Selected Users</h2>

            <form wire:submit.prevent="save">
                <!-- Permissions in 4 columns -->
                @if(!empty($duplicateMessages))
                <div class="mt-4 rounded-md bg-red-50 p-4 border border-red-200">
                    <div class="flex">
                        <!-- Warning Icon -->
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L4.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>

                        <!-- Messages -->
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">
                                Duplicate Permissions Found
                            </h3>
                            <div class="mt-2 text-sm text-red-700">
                                <ul class="list-disc list-inside space-y-1">
                                    @foreach($duplicateMessages as $msg)
                                    <li>{{ $msg }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if($errors->has('selectedUserIds'))
                <div class="mt-4 p-3 bg-red-100 text-red-800 rounded">
                    <ul class="list-disc list-inside text-sm">
                        @foreach($errors->get('selectedUserIds') as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                
                <label class="block mb-2 font-medium">Select Permissions:</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                    
                    @foreach($permissions as $id => $name)
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" wire:model="selectedPermissions" value="{{ $id }}" class="rounded" />
                        <span>{{ $name }}</span>
                    </label>
                    @endforeach
                </div>
                <div class="mt-6 flex justify-end space-x-2">
                    <button type="button" wire:click="close"
                        class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Assign
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>