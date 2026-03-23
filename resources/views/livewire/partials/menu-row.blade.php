{{-- resources/views/livewire/partials/menu-row.blade.php --}}
<tr class="menu-row" data-level="{{ $level }}">
    <td>
        <div class="d-flex align-items-center">
            <i class="fas fa-grip-vertical text-muted me-2" style="cursor: move;"></i>
            <span class="badge bg-light text-dark">{{ $menu->menu_rank }}</span>
        </div>
    </td>
    <td>
        @if($menu->icon)
            <i class="{{ $menu->icon }} fa-lg"></i>
        @else
            <i class="fas fa-link text-muted"></i>
        @endif
    </td>
    <td>
        <div style="margin-left: {{ $level * 20 }}px">
            @if($level > 0)
                <i class="fas fa-level-down-alt text-muted me-2" style="font-size: 12px;"></i>
            @endif
            <span class="fw-bold">{{ $menu->menu_name }}</span>
        </div>
    </td>
    <td>
        @if($menu->route)
            <code class="text-primary">{{ $menu->route }}</code>
        @elseif($menu->url)
            <span class="text-info">{{ $menu->url }}</span>
        @else
            <span class="text-muted">—</span>
        @endif
    </td>
    <td>
        @forelse($menu->permission_names as $perm)
            <span class="badge bg-info me-1 mb-1">{{ $perm }}</span>
        @empty
            <span class="badge bg-secondary">No permissions required</span>
        @endforelse
    </td>
    <td>
        <span class="badge {{ $menu->is_active ? 'bg-success' : 'bg-danger' }} px-3 py-2">
            <i class="fas fa-{{ $menu->is_active ? 'check-circle' : 'times-circle' }} me-1"></i>
            {{ $menu->is_active ? 'Active' : 'Inactive' }}
        </span>
    </td>
    <td>
        <div class="btn-group" role="group">
            <button class="btn btn-sm btn-outline-primary" 
                    wire:click="editMenu({{ $menu->id }})"
                    title="Edit Menu">
                <i class="fas fa-edit"></i>
            </button>
            <button class="btn btn-sm btn-outline-{{ $menu->is_active ? 'warning' : 'success' }}" 
                    wire:click="toggleStatus({{ $menu->id }})"
                    title="{{ $menu->is_active ? 'Deactivate' : 'Activate' }}">
                <i class="fas fa-{{ $menu->is_active ? 'ban' : 'check' }}"></i>
            </button>
            @if($menu->children->count() == 0)
                <button class="btn btn-sm btn-outline-danger" 
                        wire:click="confirmDelete({{ $menu->id }})"
                        title="Delete Menu">
                    <i class="fas fa-trash"></i>
                </button>
            @endif
        </div>
    </td>
</tr>
@foreach($menu->children as $child)
    @include('livewire.partials.menu-row', ['menu' => $child, 'level' => $level + 1])
@endforeach