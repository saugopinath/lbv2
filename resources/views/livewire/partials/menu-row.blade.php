{{-- resources/views/livewire/partials/menu-row.blade.php --}}
<tr>
    <td class="px-6 py-4 whitespace-nowrap">
        <div style="padding-left: {{ $level * 20 }}px" class="flex items-center">
            @if($menu->icon)
                <i class="{{ $menu->icon }} mr-2"></i>
            @endif
            <span class="text-sm text-gray-900 dark:text-white">{{ $menu->name }}</span>
        </div>
    </td>
    <td class="px-6 py-4 whitespace-nowrap">
        <code class="text-xs">{{ $menu->icon ?? '-' }}</code>
    </td>
    <td class="px-6 py-4 whitespace-nowrap">
        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
            {{ $menu->route ? 'Route' : ($menu->url ? 'URL' : 'Parent') }}
        </span>
    </td>
    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
        {{ $menu->parent ? $menu->parent->name : '-' }}
    </td>
    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
        {{ $menu->order }}
    </td>
    <td class="px-6 py-4 whitespace-nowrap">
        <button wire:click="toggleStatus({{ $menu->id }})"
            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $menu->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
            {{ $menu->is_active ? 'Active' : 'Inactive' }}
        </button>
    </td>
    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
        <button wire:click="edit({{ $menu->id }})" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
        <button wire:click="manageRoles({{ $menu->id }})" class="text-green-600 hover:text-green-900 mr-3">Roles</button>
        @if($menu->children->count() == 0)
            <button wire:click="delete({{ $menu->id }})"
                wire:confirm="Are you sure you want to delete this menu?"
                class="text-red-600 hover:text-red-900">Delete</button>
        @endif
    </td>
</tr>
@if($menu->children->count())
    @foreach($menu->children as $child)
        @include('livewire.partials.menu-row', ['menu' => $child, 'level' => $level + 1])
    @endforeach
@endif
