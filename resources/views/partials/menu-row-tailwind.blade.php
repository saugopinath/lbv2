{{-- resources/views/livewire/partials/menu-row-tailwind.blade.php --}}
<tr class="hover:bg-gray-50 transition-colors duration-150">
    <td class="px-6 py-4 whitespace-nowrap">
        <div class="flex items-center space-x-2">
            <svg class="w-4 h-4 text-gray-400 cursor-move" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path>
            </svg>
            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                {{ $menu->menu_rank }}
            </span>
        </div>
    </td>
    <td class="px-6 py-4 whitespace-nowrap">
        @if($menu->icon)
            <i class="{{ $menu->icon }} text-gray-600"></i>
        @else
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.102m1.102-4.768a4 4 0 115.656 5.656l-4 4a4 4 0 01-5.656 0"></path>
            </svg>
        @endif
    </td>
    <td class="px-6 py-4">
        <div style="margin-left: {{ $level * 20 }}px" class="flex items-center">
            @if($level > 0)
                <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            @endif
            <span class="text-sm font-medium text-gray-900">{{ $menu->menu_name }}</span>
        </div>
    </td>
    <td class="px-6 py-4">
        @if($menu->route)
            <code class="text-xs bg-gray-100 px-2 py-1 rounded text-blue-600">{{ $menu->route }}</code>
        @elseif($menu->url)
            <span class="text-xs text-gray-600">{{ $menu->url }}</span>
        @else
            <span class="text-xs text-gray-400">—</span>
        @endif
    </td>
    <td class="px-6 py-4">
        <div class="flex flex-wrap gap-1">
            @forelse($menu->permission_names as $perm)
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                    {{ $perm }}
                </span>
            @empty
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">
                    No permissions
                </span>
            @endforelse
        </div>
    </td>
    <td class="px-6 py-4 whitespace-nowrap">
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $menu->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $menu->is_active ? 'M5 13l4 4L19 7' : 'M6 18L18 6M6 6l12 12' }}"></path>
            </svg>
            {{ $menu->is_active ? 'Active' : 'Inactive' }}
        </span>
    </td>
    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
        <div class="flex space-x-2">
            <button wire:click="editMenu({{ $menu->id }})" class="text-blue-600 hover:text-blue-900 transition-colors duration-150" title="Edit Menu">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
            </button>
            <button wire:click="toggleStatus({{ $menu->id }})" class="{{ $menu->is_active ? 'text-yellow-600 hover:text-yellow-900' : 'text-green-600 hover:text-green-900' }} transition-colors duration-150" title="{{ $menu->is_active ? 'Deactivate' : 'Activate' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $menu->is_active ? 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636' : 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' }}"></path>
                </svg>
            </button>
            @if($menu->children->count() == 0)
                <button wire:click="confirmDelete({{ $menu->id }})" class="text-red-600 hover:text-red-900 transition-colors duration-150" title="Delete Menu">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            @endif
        </div>
    </tr>
@foreach($menu->children as $child)
    @include('livewire.partials.menu-row-tailwind', ['menu' => $child, 'level' => $level + 1])
@endforeach