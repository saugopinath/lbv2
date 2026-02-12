@push('styles')
    <style>
        .marquee {
            position: relative;
            overflow: hidden;
            width: 100%;
            white-space: nowrap;
        }

        .marquee-content {
            display: inline-flex;
            gap: 2rem;
            animation: marquee 18s linear infinite;
        }

        @keyframes marquee {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(-50%);
            }
        }

        /* when paused */
        .paused {
            animation-play-state: paused !important;
        }
    </style>
@endpush

<section class="bg-gradient-to-r from-indigo-50 to-blue-50 py-3 border-y border-indigo-100">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-center gap-4">

            <!-- Notification Icon -->
            <div class="flex-shrink-0">
                <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 
                            rounded-full flex items-center justify-center shadow-md">
                    <i class="fas fa-bell text-white text-lg"></i>
                </div>
            </div>

            <!-- MARQUEE -->
            <div class="flex-1 marquee">
                <div id="marqueeContent" class="marquee-content">

                    <!-- Notifications (add as many as you want) -->
                    @include('frontend.components.notification-item-h')
                    @include('frontend.components.notification-item-h')
                    @include('frontend.components.notification-item-h')
                    @include('frontend.components.notification-item-h')

                </div>
            </div>

            <!-- Pause/Play Button -->
            <button id="marqueeToggle" class="flex-shrink-0 w-8 h-8 flex items-center justify-center 
                           bg-white rounded-full shadow hover:shadow-md transition">
                <i class="fas fa-pause text-indigo-600 text-sm"></i>
            </button>

        </div>
    </div>
</section>
@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", () => {

            const marquee = document.getElementById("marqueeContent");
            const toggleBtn = document.getElementById("marqueeToggle");
            const icon = toggleBtn.querySelector("i");

            let isPaused = false;

            toggleBtn.addEventListener("click", () => {
                isPaused = !isPaused;

                if (isPaused) {
                    marquee.classList.add("paused");
                    icon.classList.remove("fa-pause");
                    icon.classList.add("fa-play");
                } else {
                    marquee.classList.remove("paused");
                    icon.classList.remove("fa-play");
                    icon.classList.add("fa-pause");
                }
            });

        });
    </script>
@endpush