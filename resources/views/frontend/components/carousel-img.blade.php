<div class="carousel-slide absolute inset-0 opacity-0 transition-opacity duration-700 ease-in-out z-0">
    <img src="{{ asset($image) }}" 
         alt="{{ $header ?: 'Slide Image' }}"
         class="w-full h-full object-cover">

    <!-- Overlay -->
    <div class="absolute inset-0 bg-gradient-to-b from-black/60 to-black/40"></div>

    <!-- Content -->
    <div class="absolute inset-0 flex items-center px-6 md:px-16 text-white">
        <div class="max-w-xl space-y-4 animate-fade-up">
            <img src="{{ asset($image) }}" 
                 class="w-40 md:w-56 rounded-lg shadow-xl hidden" />
            
            <h2 class="text-2xl md:text-4xl font-bold leading-snug drop-shadow-lg">
                {!! $title !!}
                @if ($header)
                    <span class="text-yellow-300">{{ $header }}</span>
                @endif
            </h2>
        </div>
    </div>
</div>
