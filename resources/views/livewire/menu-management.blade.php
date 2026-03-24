<div x-data="{
    showModal: @entangle('showModal')
}" class="min-h-screen bg-gray-50">

    <div class="py-6 px-4 sm:px-6 lg:px-8">

        <!-- Header -->

        <div class="flex justify-between items-center mb-6">

            <div>

                <h1 class="text-2xl font-bold text-gray-900">
                    Menu Management
                </h1>

            </div>

            <div class="flex space-x-2">
                <a href="{{ route('role-menu-mapping') }}" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                    Assign Menu
                </a>
                <button wire:click="createMenu" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    + Add New Menu
                </button>
            </div>
        </div>

        <!-- Table -->

        <div class="bg-white rounded-lg shadow border">

            <table class="min-w-full divide-y divide-gray-200">

                <thead class="bg-gray-50">

                    <tr>

                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            Menu Name
                        </th>

                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            Route
                        </th>

                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            Status
                        </th>

                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            Actions
                        </th>

                    </tr>

                </thead>

                <tbody class="bg-white divide-y divide-gray-200">

                    @forelse($menus as $menu)

                        <tr>

                            <td class="px-6 py-4">
                                {{ $menu->menu_name }}
                            </td>

                            <td class="px-6 py-4">
                                {{ $menu->route }}
                            </td>

                            <td class="px-6 py-4">

                                @if($menu->is_active)

                                    <span class="text-green-600">
                                        Active
                                    </span>

                                @else

                                    <span class="text-red-600">
                                        Inactive
                                    </span>

                                @endif

                            </td>

                            <td class="px-6 py-4">

                                <button wire:click="editMenu({{ $menu->id }})" class="text-blue-600">

                                    Edit

                                </button>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="4" class="px-6 py-12 text-center text-gray-500">

                                No menus found.
                                Click "Add New Menu"

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

    <!-- Modal -->

    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">

        <div class="bg-white w-full max-w-5xl rounded-lg shadow-lg p-6 overflow-y-auto max-h-screen">

            <h2 class="text-lg font-bold mb-4">

                {{ $isEditing ? 'Edit Menu' : 'Create Menu' }}

            </h2>

            <form wire:submit.prevent="saveMenu">

                <!-- Basic Fields -->

                <div class="grid grid-cols-2 gap-4 mb-4">

                    <div>

                        <label class="block text-sm font-medium mb-1">
                            Menu Name
                        </label>

                        <input type="text" wire:model="menu_name" class="w-full border rounded px-3 py-2">

                        @error('menu_name')

                            <p class="text-red-500 text-sm">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>

                    <div>

                        <label class="block text-sm font-medium mb-1">
                            Icon
                        </label>

                        <input type="text" wire:model="icon" class="w-full border rounded px-3 py-2">

                    </div>

                    <div>

                        <label class="block text-sm font-medium mb-1">
                            Route
                        </label>

                        <input type="text" wire:model="route" class="w-full border rounded px-3 py-2">

                    </div>

                    <div>

                        <label class="block text-sm font-medium mb-1">
                            URL
                        </label>

                        <input type="text" wire:model="url" class="w-full border rounded px-3 py-2">

                    </div>

                    <div>

                        <label class="block text-sm font-medium mb-1">
                            Parent Menu
                        </label>

                        <select wire:model="parent_id" class="w-full border rounded px-3 py-2">

                            <option value="">
                                Root Menu
                            </option>

                            @foreach($parentMenus as $parent)

                                <option value="{{ $parent->id }}">
                                    {{ $parent->menu_name }}
                                </option>

                            @endforeach

                        </select>

                    </div>

                    <div>

                        <label class="block text-sm font-medium mb-1">
                            Menu Rank
                        </label>

                        <input type="number" wire:model="menu_rank" class="w-full border rounded px-3 py-2">

                    </div>

                </div>

                <!-- 🎯 3 Column Dropdown -->

                <div class="grid grid-cols-3 gap-4 mb-4">

                    <!-- Department -->

                    <div>

                        <label class="block text-sm font-medium mb-2">

                            Department

                        </label>

                        <div class="border rounded p-2 h-40 overflow-y-auto bg-gray-50">

                            @foreach($departments as $department)

                                <label class="flex items-center space-x-2 mb-1">

                                    <input type="checkbox" value="{{ $department->id }}" wire:model.live="selectedDepartments"
                                        class="rounded border-gray-300 text-blue-600">

                                    <span class="text-sm">

                                        {{ $department->name }}

                                    </span>

                                </label>

                            @endforeach

                        </div>

                    </div>



                    <!-- Scheme -->

                    <div>

                        <label class="block text-sm font-medium mb-2">

                            Scheme

                        </label>

                        <div class="border rounded p-2 h-40 overflow-y-auto bg-gray-50">

                            @foreach($schemes as $scheme)

                                <label class="flex items-center space-x-2 mb-1">

                                    <input type="checkbox" value="{{ $scheme->id }}" wire:model="selectedSchemes"
                                        class="rounded border-gray-300 text-blue-600">

                                    <span class="text-sm">

                                        {{ $scheme->name }}

                                    </span>

                                </label>

                            @endforeach

                        </div>

                    </div>



                    <!-- Role -->

                    <div>

                        <label class="block text-sm font-medium mb-2">

                            Role

                        </label>

                        <div class="border rounded p-2 h-40 overflow-y-auto bg-gray-50">

                            @foreach($roles as $role)

                                <label class="flex items-center space-x-2 mb-1">

                                    <input type="checkbox" value="{{ $role->id }}" wire:model="selectedRoles"
                                        class="rounded border-gray-300 text-blue-600">

                                    <span class="text-sm">

                                        {{ $role->name }}

                                    </span>

                                </label>

                            @endforeach

                        </div>

                    </div>

                </div>

                <!-- Permissions -->

                <div class="mb-4">

                    <label class="block text-sm font-medium mb-1">
                        Permissions
                    </label>

                    <select multiple wire:model="selectedPermissions" class="w-full border rounded px-3 py-2 h-32">

                        @foreach($permissions as $permission)

                            <option value="{{ $permission->id }}">
                                {{ $permission->name }}
                            </option>

                        @endforeach

                    </select>

                </div>

                <!-- Status -->

                <div class="mb-4">

                    <label class="block text-sm font-medium mb-1">
                        Status
                    </label>

                    <input type="checkbox" wire:model="is_active">

                    <span class="ml-2">
                        Active
                    </span>

                </div>

                <!-- Buttons -->

                <div class="flex justify-end space-x-2">

                    <button type="button" @click="showModal = false" class="px-4 py-2 bg-gray-200 rounded">

                        Cancel

                    </button>

                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">

                        {{ $isEditing ? 'Update' : 'Create' }}

                    </button>

                </div>

            </form>

        </div>

    </div>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

</div>
