@push('styles')
<style>
    /* Accessibility option active state */
    .acc-active {
        background: #f0fdf4;
        color: #16a34a;
        font-weight: 600;
    }

    /* Body accessibility overrides */
    body.big-text {
        font-size: 1.2rem;
    }

    body.large-line-height {
        line-height: 2;
    }

    body.large-cursor,
    body.large-cursor * {
        cursor: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='32' height='32'><circle cx='16' cy='16' r='10' fill='black'/></svg>") 16 16, auto !important;
    }

    body.text-spacing * {
        letter-spacing: 0.08em;
    }

    body.highlight-links a {
        text-decoration: underline !important;
        background: #fef08a !important;
        color: #1e3a5f !important;
        border-radius: 2px;
    }

    body.dyslexia-mode {
        font-family: 'Courier New', monospace !important;
    }

    body.hide-images img {
        visibility: hidden !important;
    }

    body.invert-colors {
        filter: invert(1) hue-rotate(180deg);
    }

    body.dark-mode {
        filter: brightness(0.85) contrast(1.1);
    }

    /* Accessibility dropdown panel */
    #acc-panel {
        display: none;
        position: absolute;
        right: 0;
        top: calc(100% + 8px);
        width: 280px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.18);
        border: 1px solid #e5e7eb;
        z-index: 9999;
        overflow: hidden;
    }

    #acc-panel.open {
        display: block;
        animation: fadeSlideDown 0.18s ease;
    }

    @keyframes fadeSlideDown {
        from {
            opacity: 0;
            transform: translateY(-8px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .acc-btn {
        width: 100%;
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 16px;
        font-size: 13px;
        color: #374151;
        border: none;
        background: none;
        text-align: left;
        cursor: pointer;
        border-bottom: 1px solid #f3f4f6;
        transition: background 0.15s;
    }

    .acc-btn:last-child {
        border-bottom: none;
    }

    .acc-btn:hover {
        background: #f9fafb;
    }

    .acc-btn i {
        width: 18px;
        text-align: center;
        color: #6b7280;
    }

    .dark-mode {
        background-color: #121212 !important;
        color: #e0e0e0 !important;
    }

    .dark-mode .bg-black,
    .dark-mode .bg-gradient-to-r {
        background-color: #000000 !important;
        background-image: none !important;
    }

    .dark-mode .border-gray-700 {
        border-color: #333 !important;
    }

    /* Mobile: hide date on very small screens */
    @media (max-width: 480px) {
        #currentDateTime {
            display: none;
        }

        #dateTimeWrapper {
            display: none;
        }
    }
</style>
@endpush

{{-- ─── Top Header Bar ─────────────────────────────────────────────── --}}
<div class="bg-black sticky top-0 z-50 text-white text-xs md:text-sm border-b border-gray-700">
    <div class="max-w-7xl mx-auto px-4 py-2 flex flex-wrap items-center justify-between gap-y-1">

        {{-- Left: Phone --}}
        <div class="flex items-center gap-2 text-gray-300">
            <i class="fa-solid fa-phone text-green-400 text-xs"></i>
            <span class="font-medium tracking-wide">{{ config('constants.contact_phone') }}</span>
        </div>

        {{-- Right: Date + Accessibility --}}
        <div class="flex items-center gap-3 md:gap-5">

            {{-- Date & Time --}}
            <div id="dateTimeWrapper" class="hidden sm:flex items-center gap-2 text-gray-300">
                <i class="fa-solid fa-calendar-days text-blue-400 text-xs"></i>
                <span id="currentDateTime" class="font-mono tracking-tight"></span>
            </div>

            <span class="hidden sm:inline text-gray-600">|</span>

            {{-- Accessibility Toggle --}}
            <div class="relative" id="accWrapper">
                <button id="accToggle" aria-haspopup="true" aria-expanded="false" aria-controls="acc-panel"
                    class="flex items-center gap-2 text-gray-300 hover:text-white transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-400 rounded px-1 py-0.5">
                    <i class="fa-solid fa-universal-access text-blue-400"></i>
                    <span
                        class="hidden sm:inline font-semibold tracking-widest uppercase text-[11px]">Accessibility</span>
                    <i class="fa-solid fa-chevron-down text-[10px] transition-transform duration-200"
                        id="accChevron"></i>
                </button>

                {{-- Dropdown Panel --}}
                <div id="acc-panel" role="menu" aria-label="Accessibility Options">
                    <div
                        class="px-4 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-xs font-bold uppercase tracking-widest rounded-t-xl">
                        <i class="fa-solid fa-universal-access mr-2"></i>Accessibility Options
                    </div>

                    <div class="py-1">
                        <button class="acc-btn" id="btn-screen-reader" role="menuitem" onclick="toggleScreenReader()">
                            <i class="fa-solid fa-headphones"></i> Screen Reader Info
                        </button>
                        <button class="acc-btn" id="btn-big-text" role="menuitem" onclick="toggleBigText()">
                            <i class="fa-solid fa-text-height"></i> Bigger Text
                        </button>
                        <button class="acc-btn" id="btn-text-spacing" role="menuitem" onclick="toggleTextSpacing()">
                            <i class="fa-solid fa-arrows-left-right"></i> Text Spacing
                        </button>
                        <button class="acc-btn" id="btn-line-height" role="menuitem" onclick="toggleLineHeight()">
                            <i class="fa-solid fa-align-left"></i> Line Height
                        </button>
                        <button class="acc-btn" id="btn-highlight-links" role="menuitem"
                            onclick="toggleHighlightLinks()">
                            <i class="fa-solid fa-link"></i> Highlight Links
                        </button>
                        <button class="acc-btn" id="btn-dyslexia" role="menuitem" onclick="toggleDyslexia()">
                            <i class="fa-solid fa-font"></i> Dyslexia Friendly
                        </button>
                        <button class="acc-btn" id="btn-hide-images" role="menuitem" onclick="toggleImages()">
                            <i class="fa-solid fa-image"></i> Hide Images
                        </button>
                        <button class="acc-btn" id="btn-cursor" role="menuitem" onclick="toggleCursor()">
                            <i class="fa-solid fa-arrow-pointer"></i> Large Cursor
                        </button>
                        <button class="acc-btn" id="btn-dark" role="menuitem" onclick="toggleDark()">
                            <i class="fa-solid fa-circle-half-stroke"></i> Dark Mode
                        </button>
                        <button class="acc-btn" id="btn-invert" role="menuitem" onclick="toggleInvert()">
                            <i class="fa-solid fa-adjust"></i> Invert Colors
                        </button>
                    </div>

                    <div class="px-4 py-2 border-t border-gray-100">
                        <button onclick="resetAccessibility()"
                            class="w-full text-xs text-red-500 hover:text-red-700 font-semibold text-center transition-colors">
                            <i class="fa-solid fa-rotate-left mr-1"></i> Reset All
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {

        /* ── Live Date & Time ─────────────────────────── */
        const dtEl = document.getElementById("currentDateTime");

        function updateDateTime() {
            if (!dtEl) return;
            dtEl.textContent = new Date().toLocaleString("en-IN", {
                day: "2-digit",
                month: "short",
                year: "numeric",
                hour: "2-digit",
                minute: "2-digit",
                second: "2-digit",
                hour12: true
            });
        }
        updateDateTime();
        setInterval(updateDateTime, 1000);

        /* ── Accessibility Dropdown ───────────────────── */
        const toggle = document.getElementById("accToggle");
        const panel = document.getElementById("acc-panel");
        const chevron = document.getElementById("accChevron");

        function openPanel() {
            panel.classList.add("open");
            toggle.setAttribute("aria-expanded", "true");
            chevron.style.transform = "rotate(180deg)";
        }

        function closePanel() {
            panel.classList.remove("open");
            toggle.setAttribute("aria-expanded", "false");
            chevron.style.transform = "";
        }

        function isOpen() {
            return panel.classList.contains("open");
        }

        toggle.addEventListener("click", function(e) {
            e.stopPropagation();
            isOpen() ? closePanel() : openPanel();
        });

        document.addEventListener("click", function(e) {
            if (!document.getElementById("accWrapper").contains(e.target)) closePanel();
        });

        document.addEventListener("keydown", function(e) {
            if (e.key === "Escape") closePanel();
        });

        /* ── Helper: toggle body class + active style ── */
        function bodyToggle(cls, btnId) {
            document.body.classList.toggle(cls);
            const btn = document.getElementById(btnId);
            if (btn) btn.classList.toggle("acc-active", document.body.classList.contains(cls));
        }

        /* ── Accessibility Actions ─────────────────────── */
        window.toggleBigText = () => bodyToggle("big-text", "btn-big-text");
        window.toggleTextSpacing = () => bodyToggle("text-spacing", "btn-text-spacing");
        window.toggleLineHeight = () => bodyToggle("large-line-height", "btn-line-height");
        window.toggleHighlightLinks = () => bodyToggle("highlight-links", "btn-highlight-links");
        window.toggleDyslexia = () => bodyToggle("dyslexia-mode", "btn-dyslexia");
        window.toggleImages = () => bodyToggle("hide-images", "btn-hide-images");
        window.toggleCursor = () => bodyToggle("large-cursor", "btn-cursor");
        window.toggleDark = () => bodyToggle("dark-mode", "btn-dark");
        window.toggleInvert = () => bodyToggle("invert-colors", "btn-invert");

        window.toggleScreenReader = function() {
            alert("Screen reader support depends on your browser/OS settings (e.g. NVDA, JAWS, TalkBack). Please enable it from your system settings.");
        };

        window.resetAccessibility = function() {
            const classes = ["big-text", "text-spacing", "large-line-height", "highlight-links",
                "dyslexia-mode", "hide-images", "large-cursor", "dark-mode", "invert-colors"
            ];
            classes.forEach(c => document.body.classList.remove(c));
            document.querySelectorAll(".acc-btn").forEach(b => b.classList.remove("acc-active"));
        };

    });
</script>
@endpush