{{-- resources/views/livewire/menu-management.blade.php --}}
<div>
    <div class="container mx-auto px-4 py-8">
        @if (session()->has('message'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                {{ session('message') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                {{ session('error') }}
            </div>
        @endif

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Menu Management</h1>
            <div class="space-x-2">
                <button wire:click="generateJson" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    View JSON
                </button>
                {{--  <button wire:click="regenerateAllJson" class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                    Regenerate All JSON
                </button>  --}}
                <button wire:click="create" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Add New Menu
                </button>
            </div>
        </div>

        @if($showJson)
            <div class="bg-gray-900 rounded-lg shadow p-4 mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-white">Generated JSON Structure</h2>
                    <button wire:click="closeJson" class="text-white bg-red-500 hover:bg-red-700 px-3 py-1 rounded">Close</button>
                </div>
                <pre class="text-green-400 text-sm overflow-x-auto"><code>{{ $generatedJson }}</code></pre>
            </div>
        @endif

        @if($showForm)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-bold mb-4">
                    {{ $isEditing ? 'Edit Menu' : 'Create New Menu' }}
                </h2>

                <form wire:submit.prevent="save">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-2">Menu Name *</label>
                            <input type="text" wire:model="name" class="w-full px-3 py-2 border rounded-md">
                            @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2">Icon Class</label>
                            <input type="text" wire:model="icon" placeholder="fas fa-home" class="w-full px-3 py-2 border rounded-md">
                            @error('icon') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2">Route Name</label>
                            <input type="text" wire:model="route" class="w-full px-3 py-2 border rounded-md">
                            @error('route') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2">URL</label>
                            <input type="text" wire:model="url" class="w-full px-3 py-2 border rounded-md">
                            @error('url') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2">Parent Menu</label>
                            <select wire:model="parent_id" class="w-full px-3 py-2 border rounded-md">
                                <option value="">None (Top Level)</option>
                                @foreach($parentMenus as $parent)
                                    @if($parent->id != $menuId)
                                        <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2">Order</label>
                            <input type="number" wire:model="order" class="w-full px-3 py-2 border rounded-md">
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2">Permission Key</label>
                            <input type="text" wire:model="permission_key" class="w-full px-3 py-2 border rounded-md">
                        </div>

                        <div>
                            <label class="flex items-center mt-6">
                                <input type="checkbox" wire:model="is_active" class="mr-2">
                                <span class="text-sm">Active</span>
                            </label>
                        </div>

                        <div class="col-span-2">
                            <label class="block text-sm font-medium mb-2">Assign Roles</label>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-2 border rounded-md p-4">
                                @foreach($roles as $role)
                                    <label class="flex items-center">
                                        <input type="checkbox" wire:model="selectedRoles" value="{{ $role->id }}" class="mr-2">
                                        <span class="text-sm">{{ $role->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 flex justify-end space-x-2">
                        <button type="button" wire:click="cancelForm" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                            {{ $isEditing ? 'Update' : 'Save' }}
                        </button>
                    </div>
                </form>
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Icon</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Order</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($menus as $menu)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if($menu->icon)
                                        <i class="{{ $menu->icon }} mr-2"></i>
                                    @endif
                                    <span>{{ $menu->name }}</span>
                                </div>
                                @if($menu->children->count())
                                    <div class="ml-6 mt-2">
                                        @foreach($menu->children as $child)
                                            <div class="py-1 text-sm text-gray-500">
                                                ↳ {{ $child->name }}
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <code class="text-sm">{{ $menu->icon }}</code>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $menu->route ? 'Route' : ($menu->url ? 'URL' : 'Parent') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $menu->order }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button wire:click="toggleActive({{ $menu->id }})" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $menu->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $menu->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button wire:click="edit({{ $menu->id }})" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                                @if(!$menu->children->count())
                                    <button wire:click="delete({{ $menu->id }})" wire:confirm="Are you sure?" class="text-red-600 hover:text-red-900">Delete</button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="px-6 py-4">
                {{ $menus->links() }}
            </div>
        </div>
    </div>
</div>
