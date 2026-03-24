<div x-data="{
    showModal: @entangle('showModal')
}" class="min-h-screen bg-gray-50">

    <div class="py-6 px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Assign Menu Management</h1>
            <button wire:click="createMapping" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                + Assign Menu
            </button>
        </div>

        <div class="bg-white rounded-lg shadow p-4 border">
            <livewire:role-menu-user-mapping-table />
        </div>
    </div>

    <!-- Modal -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white w-full max-w-2xl rounded-lg shadow-lg p-6 overflow-y-auto max-h-screen">
            <h2 class="text-lg font-bold mb-4">
                {{ $isEditing ? 'Edit Assignment' : 'Assign Menu' }}
            </h2>

            <form wire:submit.prevent="saveMapping">
                <div class="grid grid-cols-1 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Menu Name</label>
                        <select wire:model="menu_id" class="w-full border rounded px-3 py-2">
                            <option value="">-- Select Menu --</option>
                            @foreach($menus as $menu)
                                <option value="{{ $menu->id }}">{{ $menu->menu_name }}</option>
                            @endforeach
                        </select>
                        @error('menu_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Role</label>
                        <select wire:model="role_id" class="w-full border rounded px-3 py-2">
                            <option value="">-- Select Role --</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Scheme</label>
                        <select wire:model="scheme_id" class="w-full border rounded px-3 py-2">
                            <option value="">-- Select Scheme --</option>
                            @foreach($schemes as $scheme)
                                <option value="{{ $scheme->id }}">{{ $scheme->name ?? $scheme->scheme_name ?? 'ID: ' . $scheme->id }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Department</label>
                        <select wire:model="department_id" class="w-full border rounded px-3 py-2">
                            <option value="">-- Select Department --</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}">{{ $department->name ?? $department->department_name ?? 'ID: ' . $department->id }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Permission</label>
                        <select wire:model="permission_id" class="w-full border rounded px-3 py-2">
                            <option value="">-- Select Permission --</option>
                            @foreach($permissions as $permission)
                                <option value="{{ $permission->id }}">{{ $permission->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex justify-end space-x-2 mt-6">
                    <button type="button" @click="showModal = false" class="px-4 py-2 bg-gray-200 rounded">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">
                        {{ $isEditing ? 'Update' : 'Assign' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <style>
        [x-cloak] { display: none !important; }
    </style>
</div>
