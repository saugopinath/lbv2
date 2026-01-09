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
                                name="selectedRoles.{{$loop->index}}"
                                wire:model.live="selectedRoles"
                                :value="$role->id"
                                :label="$role->name"
                                :disabled="count($selectedRoles) == 1
            && !in_array($role->id, $selectedRoles)" />
                        </label>
                        @endforeach
                    </div>
                    <x-form.error name="selectedRoles" />
                </div>
                <div class="px-6 py-4 border-t flex justify-end space-x-2">
                    <x-button.danger type="button" wire:click="close">
                        Cancel
                    </x-button.danger>
                    <x-button.primary-with-disable :disabled="count($selectedRoles) == 0" type="submit">
                        Save
                    </x-button.primary-with-disable>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>