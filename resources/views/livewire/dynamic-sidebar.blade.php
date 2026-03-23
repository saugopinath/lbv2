<div x-data="{ sidebar: true }" @toggle-sidebar.window="sidebar = !sidebar" :class="sidebar ? 'w-64' : 'w-20'" class="transition-all duration-300 
bg-gradient-to-r from-cyan-800 to-cyan-600 
text-white h-screen flex flex-col">

    <!-- Logo -->

    <div class="flex flex-col items-center p-4 border-b border-cyan-700">

        <img src="{{ asset('/images/biswo.png') }}" class="w-10 mb-2">

        <!-- Text hide when collapsed -->

        <span x-show="sidebar" class="font-bold text-sm">

            Lakshmir Bhandar

        </span>

    </div>

    <!-- Menu -->

    <nav class="flex-1 overflow-y-auto mt-3 space-y-1">

        @forelse($menus as $menu)

            <div class="px-2">

                @if($menu->children->count())

                                <button wire:click="toggleMenu({{ $menu->id }})" class="flex items-center justify-between 
                    w-full px-3 py-2 
                    hover:bg-slate-700 rounded-lg transition">

                                    <div class="flex items-center gap-2">

                                        <i class="{{ $menu->icon ?? 'fas fa-folder' }}"></i>

                                        <span x-show="sidebar">

                                            {{ $menu->menu_name }}

                                        </span>

                                    </div>

                                    <i x-show="sidebar" class="fas fa-chevron-down text-xs 
                    {{ $this->isMenuExpanded($menu->id) ? 'rotate-180' : '' }}">

                                    </i>

                                </button>

                                <!-- Child Menu -->

                                @if($this->isMenuExpanded($menu->id))

                                    <div class="ml-6 mt-1 space-y-1">

                                        @foreach($menu->children as $child)

                                                            @php

                                                                $childHref = '#';

                                                                if ($child->route && Route::has($child->route)) {

                                                                    $childHref = route($child->route);

                                                                } elseif ($child->url) {

                                                                    $childHref = $child->url;

                                                                }

                                                            @endphp

                                                            <a href="{{ $childHref }}" class="flex items-center px-3 py-2 text-sm 
                                            hover:bg-slate-700 rounded-lg">

                                                                <i class="{{ $child->icon ?? 'fas fa-circle' }} text-xs mr-2"></i>

                                                                <span x-show="sidebar">

                                                                    {{ $child->menu_name }}

                                                                </span>

                                                            </a>

                                        @endforeach

                                    </div>

                                @endif

                @else

                                <!-- Single Menu -->

                                @php

                                    $menuHref = '#';

                                    if ($menu->route && Route::has($menu->route)) {

                                        $menuHref = route($menu->route);

                                    } elseif ($menu->url) {

                                        $menuHref = $menu->url;

                                    }

                                @endphp

                                <a href="{{ $menuHref }}" class="flex items-center px-3 py-2 
                    hover:bg-slate-700 rounded-lg">

                                    <i class="{{ $menu->icon ?? 'fas fa-link' }} mr-2"></i>

                                    <span x-show="sidebar">

                                        {{ $menu->menu_name }}

                                    </span>

                                </a>

                @endif

            </div>

        @empty

            <div class="text-center py-4 text-slate-300">

                <p x-show="sidebar">

                    No menus available

                </p>

            </div>

        @endforelse

    </nav>

</div>