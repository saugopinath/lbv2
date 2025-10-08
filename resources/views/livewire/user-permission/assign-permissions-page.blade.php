<div class="bg-white shadow-md rounded p-4 space-y-4">

    <h1 class="text-xl font-bold mb-4">Assign Permissions to Users</h1>

    <div class="relative inline-block w-full">
        <label class="font-semibold mb-2 block">Select Users</label>

        <!-- Dropdown button -->
        <button type="button"
            class="w-full border rounded p-2 text-left"
            onclick="document.getElementById('userDropdown').classList.toggle('hidden')">
            Select Users
        </button>

        <!-- Dropdown menu -->
        <div id="userDropdown" class="absolute w-full border rounded bg-white mt-1 max-h-40 overflow-y-auto hidden z-10">
            @foreach($allUsers as $user)
            <label class="flex items-center px-2 py-1 cursor-pointer hover:bg-gray-100">
                <input type="checkbox" wire:model="users" value="{{ $user->id }}" class="mr-2">
                {{ $user->name }}
            </label>
            @endforeach
        </div>
    </div>

    <!-- Permissions Checkboxes -->
    <div>
        <label class="font-semibold mb-2 block">Select Permissions</label>
        <div class="border rounded p-2 h-40 overflow-y-auto">
            @foreach($allPermissions as $permission)
            <label class="flex items-center mb-1 cursor-pointer">
                <input type="checkbox" value="{{ $permission->id }}" wire:model="permissions" class="mr-2">
                {{ $permission->name }}
            </label>
            @endforeach
        </div>
    </div>

    <div class="flex justify-end gap-2 mt-4">
        <button wire:click="saveUserPermission" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Assign Permissions
        </button>
    </div>
</div>