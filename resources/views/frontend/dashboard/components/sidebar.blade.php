@php
    $menuPath = storage_path('app/menu/dashboard/menu.json');
    $menuData = json_decode(file_get_contents($menuPath), true);
    $sidebar = $menuData['sidebar'];

    $colorMap = [
        'blue' => ['400' => '#60a5fa', '500' => '#3b82f6'],
        'pink' => ['400' => '#f472b6', '500' => '#ec4899'],
        'yellow' => ['400' => '#facc15', '500' => '#eab308'],
        'indigo' => ['400' => '#818cf8', '500' => '#6366f1'],
        'purple' => ['400' => '#c084fc', '500' => '#a855f7'], // v4 purple-500 is typically #a855f7
        'red' => ['400' => '#f87171', '500' => '#ef4444'],
        'green' => ['400' => '#4ade80', '500' => '#22c55e'],
        'emerald' => ['400' => '#34d399', '500' => '#10b981'],
        'orange' => ['400' => '#fb923c', '500' => '#f97316'],
        'teal' => ['400' => '#2dd4bf', '500' => '#14b8a6'],
        'gray' => ['400' => '#9ca3af', '500' => '#6b7280'],
        'cyan' => ['400' => '#22d3ee', '500' => '#06b6d4'],
        'sky' => ['400' => '#38bdf8', '500' => '#0ea5e9'],
        'lime' => ['400' => '#a3e635', '500' => '#84cc16'],
        'rose' => ['400' => '#fb7185', '500' => '#f43f5e'],
        'amber' => ['400' => '#fbbf24', '500' => '#f59e0b'],
        'violet' => ['400' => '#a78bfa', '500' => '#8b5cf6'],
        'fuchsia' => ['400' => '#e879f9', '500' => '#d946ef'],
    ];
@endphp

<div id="application-sidebar"
    class="sidebar fixed top-0 start-0 bottom-0 z-[60] w-64 bg-gray-900 border-e border-gray-700 pt-7 pb-10 overflow-y-auto lg:translate-x-0 lg:end-auto lg:bottom-0 transition-all duration-300 transform -translate-x-full lg:translate-x-0">

    <!-- Logo Section -->
    <a href="{{ route('/') }}" class="block hover:text-white">
        <div class="px-6 mb-8">
            <div class="flex items-center gap-3">
                <div
                    class="w-12 h-12 rounded-xl flex items-center justify-center shadow-lg bg-gradient-to-br from-blue-500 to-indigo-600">
                   
                </div>
                <div>
                    <h1 class="text-lg font-bold text-white">{{ $sidebar['title'] }}</h1>
                    <p class="text-xs text-gray-400">{{ $sidebar['subtitle'] }}</p>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="px-3 w-full flex flex-col">
            <ul class="space-y-1">

                @foreach ($sidebar['menus'] as $item)

                    {{-- SINGLE MENU --}}
                    @if ($item['type'] === 'single')
                        <li>
                            <a href="{{ $item['route'] }}"
                                class="sidebar-link flex items-center gap-x-3 py-3 px-4 text-sm text-gray-300 rounded-lg hover:bg-gray-800 hover:text-white transition-colors duration-200 group">
                                <div
                                    class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-800 group-hover:bg-blue-500/20 transition-colors duration-200">
                                    <i class="{{ $item['icon'] }} group-hover:text-blue-400"></i>
                                </div>
                                <span class="font-medium">{{ $item['label'] }}</span>

                                @if (!empty($item['badge']))
                                    <span class="ml-auto bg-blue-500 text-white text-xs px-2 py-1 rounded-full font-medium">
                                        {{ $item['badge'] }}
                                    </span>
                                @endif
                            </a>
                        </li>
                    @endif

                    {{-- PARENT MENU (SCHEMES) --}}
                    @if ($item['type'] === 'parent' && !empty($item['children']))
                        <li class="accordion-parent" id="accordion-{{ $item['key'] }}">
                            <button type="button"
                                class="accordion-toggle w-full flex items-center justify-between gap-x-3 py-3 px-4 text-sm text-gray-300 rounded-lg hover:bg-gray-800 hover:text-white transition-colors duration-200"
                                data-accordion-target="content-{{ $item['key'] }}">

                                <div class="flex items-center gap-x-3">
                                    <div class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-800">
                                        <i class="{{ $item['icon'] }}"></i>
                                    </div>
                                    <span class="font-medium">{{ $item['label'] }}</span>
                                </div>

                                <div class="flex items-center gap-x-2">
                                    @if (!empty($item['badge']))
                                        <span class="bg-purple-500 text-white text-xs px-2 py-1 rounded-full font-medium">
                                            {{ $item['badge'] }}
                                        </span>
                                    @endif

                                    <svg class="accordion-arrow w-4 h-4 text-gray-400 transition-transform duration-300"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </button>

                            <div id="content-{{ $item['key'] }}"
                                class="accordion-content overflow-hidden transition-all duration-300 max-h-0"
                                aria-labelledby="accordion-{{ $item['key'] }}">
                                <ul class="pt-2 pb-2 ps-12 pe-4 space-y-1">
                                    @foreach ($item['children'] as $child)
                                        <li>
                                            <a href="{{ $child['route'] }}"
                                                class="flex items-center gap-x-3 py-2.5 px-3 text-sm text-gray-400 rounded-lg hover:bg-gray-800 hover:text-white transition-colors duration-200 group">
                                                <div
                                                    class="w-1.5 h-1.5 rounded-full bg-gray-600 group-hover:bg-blue-400 transition-colors duration-200">
                                                </div>
                                                <span class="font-medium">{{ $child['label'] }}</span>
                                                @if (!empty($child['badge']))
                                                    <span class="ml-auto bg-gray-700 text-gray-300 text-xs px-2 py-0.5 rounded-full">
                                                        {{ $child['badge'] }}
                                                    </span>
                                                @endif
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </li>
                    @endif

                @endforeach

            </ul>

            <!-- Divider -->
            <div class="my-6 mx-4 border-t border-gray-700"></div>

            <!-- Categories -->
            <div class="px-3">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3 px-1">Integrations</h3>
                <ul class="space-y-2">
                    @foreach ($sidebar['categories'] as $cat)
                        @php
                            $catColor = $colorMap[$cat['color']] ?? $colorMap['gray'];
                            // Hex to RGB for opacity
                            $hex = $catColor['500'];
                            list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x");
                            $bgRgba = "rgba($r, $g, $b, 0.2)";
                            $borderRgba = "rgba($r, $g, $b, 0.2)";
                        @endphp
                        <li>
                            <a href="#"
                                class="flex items-center gap-x-3 py-2.5 px-3 text-sm text-gray-300 rounded-lg hover:bg-gray-800 hover:text-white transition-colors duration-200">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center border"
                                    style="background-color: {{ $bgRgba }}; border-color: {{ $borderRgba }}">
                                    <i class="{{ $cat['icon'] }}" style="color: {{ $catColor['400'] }}"></i>
                                </div>
                                <span class="font-medium">{{ $cat['label'] }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

        </nav>
</div>

@push('styles')
    <style>
        .accordion-content.open {
            max-height: 500px !important;
        }

        .accordion-toggle.active .accordion-arrow {
            transform: rotate(180deg);
        }

        .accordion-toggle.active {
            background-color: rgb(31 41 55 / var(--tw-bg-opacity));
            /* gray-800 */
            color: white;
        }

        .sidebar-link.active {
            background-color: rgb(31 41 55 / var(--tw-bg-opacity));
            /* gray-800 */
            color: white;
            position: relative;
        }

        .sidebar-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background-color: rgb(59 130 246 / var(--tw-bg-opacity));
            /* blue-500 */
            border-radius: 0 3px 3px 0;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Accordion functionality
            const accordionToggles = document.querySelectorAll('.accordion-toggle');

            accordionToggles.forEach(toggle => {
                toggle.addEventListener('click', function () {
                    const targetId = this.getAttribute('data-accordion-target');
                    const target = document.getElementById(targetId);
                    const arrow = this.querySelector('.accordion-arrow');

                    // Check if target exists
                    if (!target) {
                        console.error('Accordion target not found:', targetId);
                        return;
                    }

                    const isOpen = target.classList.contains('open');

                    // Close all other accordions
                    accordionToggles.forEach(otherToggle => {
                        if (otherToggle !== this) {
                            const otherTargetId = otherToggle.getAttribute('data-accordion-target');
                            const otherTarget = document.getElementById(otherTargetId);
                            const otherArrow = otherToggle.querySelector('.accordion-arrow');

                            if (otherTarget) {
                                otherTarget.classList.remove('open');
                                otherTarget.style.maxHeight = '0';
                            }
                            if (otherArrow) {
                                otherArrow.classList.remove('rotate-180');
                            }
                            otherToggle.classList.remove('active');
                        }
                    });

                    // Toggle current accordion
                    if (isOpen) {
                        target.classList.remove('open');
                        target.style.maxHeight = '0';
                        if (arrow) {
                            arrow.classList.remove('rotate-180');
                        }
                        this.classList.remove('active');
                    } else {
                        target.classList.add('open');
                        // Calculate max height based on content
                        const contentHeight = target.scrollHeight + 'px';
                        target.style.maxHeight = contentHeight;
                        if (arrow) {
                            arrow.classList.add('rotate-180');
                        }
                        this.classList.add('active');
                    }
                });
            });

            // Set active link based on current URL
            function setActiveLinks() {
                const currentPath = window.location.pathname;
                const sidebarLinks = document.querySelectorAll('.sidebar-link');

                // First, remove all active classes
                sidebarLinks.forEach(link => {
                    link.classList.remove('active');
                });

                // Find and set active link
                sidebarLinks.forEach(link => {
                    const href = link.getAttribute('href');
                    if (href && currentPath.startsWith(href)) {
                        link.classList.add('active');

                        // If this is inside an accordion, open the parent accordion
                        const parentAccordion = link.closest('.accordion-content');
                        if (parentAccordion) {
                            const accordionId = parentAccordion.id;
                            const toggleButton = document.querySelector(`[data-accordion-target="#${accordionId}"]`);
                            if (toggleButton) {
                                // Trigger click to open accordion
                                toggleButton.click();
                            }
                        }
                    }
                });
            }

            // Initial active link setup
            setActiveLinks();

            // Listen for URL changes (for SPA-like behavior if applicable)
            window.addEventListener('popstate', setActiveLinks);

            // If using AJAX navigation, you might need to call setActiveLinks() after page changes

            // Optional: Auto-open first accordion if no active link is found
            setTimeout(() => {
                const hasActiveLink = document.querySelector('.sidebar-link.active');
                if (!hasActiveLink && accordionToggles.length > 0) {
                    // Open the first accordion
                    accordionToggles[0].click();
                }
            }, 100);
        });
    </script>
@endpush