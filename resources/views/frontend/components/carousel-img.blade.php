<div class="carousel-slide absolute inset-0 opacity-0 transition-opacity duration-700 ease-in-out z-0">
    <img src="{{ asset($image) }}"
        alt="{{ $header ?: 'Slide Image' }}"
        class="w-full h-full object-cover {{ $object_position ?? 'object-center' }}">

    <!-- Overlay -->
    <div class="absolute inset-0 bg-black/30"></div>

    <!-- Content -->
    <div class="absolute inset-0 flex items-center px-6 md:px-20 text-white">
        <div class="max-w-4xl p-8 md:p-12 rounded-[2rem] backdrop-blur-md bg-black/30 border border-white/20 shadow-2xl space-y-6 animate-fade-up">
            @if ($header)
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-yellow-400 text-black text-[10px] md:text-xs font-black uppercase tracking-[0.2em]">
                <span class="w-1.5 h-1.5 rounded-full bg-black animate-pulse"></span>
                {{ $header }}
            </div>
            @endif

            <h2 class="text-2xl md:text-4xl font-extrabold leading-[1.1] tracking-tight text-transparent bg-clip-text bg-gradient-to-br from-white via-white to-white/70 drop-shadow-[0_10px_20px_rgba(0,0,0,0.4)]">
                {!! $title !!}
            </h2>
        </div>
    </div>
</div>