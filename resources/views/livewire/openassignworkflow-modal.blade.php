<div>
    @if($isOpen)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl w-full sm:w-3/4 md:w-1/2 lg:w-2/5 max-h-[90vh] flex flex-col">
            <div class="px-6 py-4 border-b">
                <h2 class="text-lg font-semibold">Assign role to: {{$name}}</h2>
            </div>
            <form wire:submit.prevent="save">
                <div class="flex-1 overflow-y-auto px-6 py-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                        @foreach($roles as $role)
                        <label class="flex items-center space-x-2">
                            <x-form.checkbox
                                name="selectedroles"
                                wire:model="selectedRoles"
                                value="{{ $role->id }}"
                                label="{{ $role->name }}" />
                        </label>
                        @endforeach
                    </div>
                </div>
                <div class="px-6 py-4 border-t flex justify-end space-x-2">
                    <button type="button" wire:click="close"
                        class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>