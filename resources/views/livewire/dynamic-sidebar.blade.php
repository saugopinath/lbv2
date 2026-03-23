{{-- resources/views/livewire/dynamic-sidebar.blade.php --}}
<div>
    <div class="sidebar-wrapper">
        <nav class="sidebar-nav">
            <ul class="nav flex-column">
                @forelse($menus as $menu)
                    <li class="nav-item">
                        @if($menu->children->count() > 0)
                            <a href="#" class="nav-link" wire:click.prevent="toggleMenu({{ $menu->id }})">
                                <i class="{{ $menu->icon ?? 'fas fa-folder' }}"></i>
                                <span>{{ $menu->menu_name }}</span>
                                <i class="fas fa-chevron-{{ $isMenuExpanded($menu->id) ? 'down' : 'right' }} float-right"></i>
                            </a>
                            <ul class="nav flex-column ms-3 {{ $isMenuExpanded($menu->id) ? 'd-block' : 'd-none' }}">
                                @foreach($menu->children as $child)
                                    <li class="nav-item">
                                        <a href="{{ $child->route ? route($child->route) : $child->url }}" 
                                           class="nav-link {{ request()->routeIs($child->route) ? 'active' : '' }}">
                                            <i class="{{ $child->icon ?? 'fas fa-circle' }}"></i>
                                            <span>{{ $child->menu_name }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <a href="{{ $menu->route ? route($menu->route) : $menu->url }}" 
                               class="nav-link {{ request()->routeIs($menu->route) ? 'active' : '' }}">
                                <i class="{{ $menu->icon ?? 'fas fa-link' }}"></i>
                                <span>{{ $menu->menu_name }}</span>
                            </a>
                        @endif
                    </li>
                @empty
                    <li class="nav-item">
                        <div class="text-center text-muted p-3">
                            No menus available
                        </div>
                    </li>
                @endforelse
            </ul>
        </nav>
    </div>
</div>