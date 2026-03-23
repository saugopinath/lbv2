<div
    x-data="{
        sidebar: @entangle('sidebar'),
        activeMenu: @entangle('activeMenu')
    }"
    :class="sidebar ? 'w-64' : 'w-20'"
    class="transition-all duration-300 bg-gradient-to-r from-cyan-800 to-cyan-600 shadow-lg flex flex-col h-screen relative">

    <!-- Logo -->
    <div class="flex flex-col items-center p-4 border-b border-gray-700">
        <img src="{{ asset('/images/biswo.png') }}" alt="Logo" class="w-10 mb-2" />
        <template x-if="sidebar">
            <div class="text-center font-bold text-sm text-white">Lakshmir Bhandar</div>
        </template>
    </div>



    <!-- Menu -->
    <nav class="flex-1 overflow-y-auto mt-4 space-y-1">
        @forelse($menus as $menu)
            @if(count($menu['children'] ?? []) > 0)
                <div class="px-2">
                    <button
                        @click="activeMenu === {{ $menu['id'] }} ? activeMenu = null : activeMenu = {{ $menu['id'] }}"
                        class="flex items-center w-full px-3 py-2 text-left hover:bg-slate-700 text-white rounded-lg transition-colors"
                        :class="{ 'bg-slate-700': activeMenu === {{ $menu['id'] }} }">

                        <i class="{{ $menu['icon'] ?? 'fas fa-folder' }} text-lg mr-2"></i>

                        <span x-show="sidebar" class="flex-1 text-sm">{{ $menu['name'] }}</span>

                        <svg x-show="sidebar" class="w-4 h-4 transition-transform"
                             :class="{ 'rotate-180': activeMenu === {{ $menu['id'] }} }"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <div x-show="activeMenu === {{ $menu['id'] }}"
                         x-collapse
                         class="mt-1 ml-6 space-y-1">
                        @foreach($menu['children'] as $child)
                            @php
                                $childHref = '#';
                                if (!empty($child['route'])) {
                                    if (\Illuminate\Support\Facades\Route::has($child['route'])) {
                                        $childHref = route($child['route']);
                                    } elseif (preg_match('/^(https?:\/\/|\/)/', $child['route'])) {
                                        $childHref = $child['route'];
                                    }
                                } elseif (!empty($child['url'])) {
                                    $childHref = $child['url'];
                                }
                            @endphp
                            <a href="{{ $childHref }}"
                                class="flex items-center px-3 py-2 text-sm text-slate-200 hover:bg-slate-700 hover:text-white rounded-lg transition-colors">
                                <i class="{{ $child['icon'] ?? 'fas fa-circle' }} text-sm mr-2"></i>
                                <span x-show="sidebar">{{ $child['name'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="px-2">
                    @php
                        $menuHref = '#';
                        if (!empty($menu['route'])) {
                            if (\Illuminate\Support\Facades\Route::has($menu['route'])) {
                                $menuHref = route($menu['route']);
                            } elseif (preg_match('/^(https?:\/\/|\/)/', $menu['route'])) {
                                $menuHref = $menu['route'];
                            }
                        } elseif (!empty($menu['url'])) {
                            $menuHref = $menu['url'];
                        }
                    @endphp
                    <a href="{{ $menuHref }}"
                        class="flex items-center px-3 py-2 text-white hover:bg-slate-700 rounded-lg transition-colors">
                        <i class="{{ $menu['icon'] ?? 'fas fa-link' }} text-lg mr-2"></i>
                        <span x-show="sidebar" class="text-sm">{{ $menu['name'] }}</span>
                    </a>
                </div>
            @endif
        @empty
            <div class="text-center text-slate-300 py-4">
                <p x-show="sidebar">No menus available</p>
            </div>
        @endforelse
    </nav>
</div>
