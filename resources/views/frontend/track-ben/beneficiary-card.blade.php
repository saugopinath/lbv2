<div class="beneficiary-card group relative rounded-[2.5rem] p-7 shadow-2xl transition-all duration-500 hover:-translate-y-2 hover:shadow-[0_20px_60px_rgba(37,99,235,0.18)] border border-blue-200/60 overflow-hidden"
    style="background: linear-gradient(145deg, #dbeafe 0%, #e0f2fe 30%, #f0f9ff 60%, #eff6ff 100%); font-family: 'Plus Jakarta Sans', sans-serif;">

    {{-- Decorative ambient blobs --}}
    <div class="absolute -top-16 -right-16 w-48 h-48 rounded-full pointer-events-none"
        style="background: radial-gradient(circle, rgba(99,179,237,0.22) 0%, transparent 70%);"></div>
    <div class="absolute -bottom-16 -left-14 w-44 h-44 rounded-full pointer-events-none"
        style="background: radial-gradient(circle, rgba(147,197,253,0.18) 0%, transparent 70%);"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-64 h-64 rounded-full pointer-events-none"
        style="background: radial-gradient(circle, rgba(186,230,255,0.12) 0%, transparent 70%);"></div>

    {{-- Top shimmer line --}}
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-2/3 h-px"
        style="background: linear-gradient(90deg, transparent, rgba(99,179,237,0.7), transparent);"></div>
    {{-- Bottom shimmer line --}}
    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 w-1/3 h-px"
        style="background: linear-gradient(90deg, transparent, rgba(99,179,237,0.4), transparent);"></div>

    {{-- ── HEADER ROW: Status pill + IDs ── --}}
    <div class="relative flex justify-between items-start mb-7 gap-3 flex-wrap">

        {{-- Status pill --}}
        <div
            class="flex items-center gap-2.5 px-4 py-1.5 rounded-full border bg-{{ $statusColor }}-100/90 border-{{ $statusColor }}-200 shadow-sm backdrop-blur-md">
            <span class="relative flex h-2 w-2">
                <span
                    class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75 bg-{{ $statusColor }}-400"></span>
                <span
                    class="relative inline-flex rounded-full h-2 w-2 bg-{{ $statusColor }}-500 shadow-sm shadow-{{ $statusColor }}-500/50"></span>
            </span>
            <span class="text-[10px] font-black uppercase tracking-[0.15em] text-{{ $statusColor }}-700">
                {{ $status }}
            </span>
        </div>

        {{-- IDs --}}
        <div class="flex flex-col gap-1.5 items-end">
            <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl border"
                style="background: rgba(255,255,255,0.65); border-color: rgba(147,197,253,0.5); backdrop-filter: blur(8px);">
                <span class="text-[9px] font-bold uppercase tracking-widest" style="color: #6b7280;">Application
                    ID:</span>
                <span class="font-mono font-bold text-xs tracking-wider"
                    style="color: #2563eb; font-family: 'JetBrains Mono', monospace;">{{ $applicationId }}</span>
            </div>
            @if ($beneficiaryId)
                <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl border"
                    style="background: rgba(255,255,255,0.65); border-color: rgba(147,197,253,0.5); backdrop-filter: blur(8px);">
                    <span class="text-[9px] font-bold uppercase tracking-widest" style="color: #6b7280;">Beneficiary
                        ID:</span>
                    <span class="font-mono font-bold text-xs tracking-wider"
                        style="color: #2563eb; font-family: 'JetBrains Mono', monospace;">{{ $beneficiaryId }}</span>
                </div>
            @endif
        </div>
    </div>

    {{-- ── PROFILE ROW: Avatar + Name + Relation ── --}}
    <div class="relative flex items-center gap-5 mb-6">
        {{-- Avatar --}}
        <div class="relative shrink-0">
            {{-- Glow halo --}}
            <div class="absolute -inset-1 rounded-[20px] opacity-0 group-hover:opacity-60 transition-opacity duration-500 -z-10"
                style="background: linear-gradient(135deg, #60a5fa, #818cf8); filter: blur(8px);"></div>
            {{-- Avatar box --}}
            @if ($ben_profile_pic && ($ben_profile_pic['document_mime_type'] == 'image/jpeg' || $ben_profile_pic['document_mime_type'] == 'image/png'))
                @php
                    $document_mime_type = $ben_profile_pic['document_mime_type'];
                    if ($document_mime_type == 'image/jpeg') {
                        $image_extension = 'jpg';
                    } else if ($document_mime_type == 'image/png') {
                        $image_extension = 'png';
                    } else if ($document_mime_type == 'application/pdf') {
                        $image_extension = 'pdf';
                    }
                    $row_image = "data:image/" . $image_extension . ";base64," . $ben_profile_pic['attched_document']; 
                @endphp
                <img src="{{ $row_image }}" alt="Profile Picture" class="w-20 h-20 rounded-full">
            @else
                <div class="relative w-16 h-16 rounded-[18px] flex items-center justify-center border-2 border-white/80 transform group-hover:scale-110 group-hover:rotate-2 transition-all duration-500"
                    style="background: linear-gradient(135deg, #3b82f6, #6366f1); box-shadow: 0 4px 16px rgba(59,130,246,0.3);">
                    <i class="fas fa-user text-white text-2xl drop-shadow-md"></i>
                </div>
            @endif
        </div>

        {{-- Name + relation --}}
        <div class="flex-1 min-w-0">
            <h3 class="text-2xl font-extrabold truncate tracking-tight transition-colors duration-200 group-hover:text-blue-700"
                style="color: #1e3a5f; letter-spacing: -0.02em;">
                {{ $name }}
            </h3>
            <div class="flex items-center gap-2 mt-2">
                <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-tight border"
                    style="background: rgba(219,234,254,0.9); color: #1d4ed8; border-color: rgba(147,197,253,0.6);">
                    {{ $relation }}
                </span>
                <span class="text-sm truncate font-medium" style="color: #64748b;">{{ $relationName }}</span>
            </div>
        </div>
    </div>

    {{-- Divider --}}
    <div class="mb-5 h-px" style="background: linear-gradient(90deg, transparent, rgba(147,197,253,0.5), transparent);">
    </div>

    {{-- ── SCHEME INFO ITEM ── --}}
    <div class="relative space-y-3 mb-5">
        <div class="flex items-center gap-4 p-4 rounded-[1.4rem] border transition-all duration-300 hover:translate-x-1"
            style="background: rgba(255,255,255,0.55); border-color: rgba(147,197,253,0.35); backdrop-filter: blur(8px);">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 border transition-transform duration-300 group-hover:scale-110"
                style="background: rgba(219,234,254,0.9); border-color: rgba(147,197,253,0.5);">
                <i class="fas fa-shield-alt text-sm" style="color: #2563eb;"></i>
            </div>
            <div class="overflow-hidden">
                <p class="text-[9px] font-black uppercase tracking-[0.14em] mb-0.5" style="color: #94a3b8;">Applied
                    Scheme</p>
                <p class="text-sm font-bold truncate" style="color: #1e293b;">{{ $schemeName }}</p>
            </div>
        </div>

        {{-- Grid: Location + Contact --}}
        <div class="grid grid-cols-2 gap-3">
            {{-- Location --}}
            <div class="p-4 rounded-[1.4rem] border transition-all duration-300 hover:translate-x-1 group/loc"
                style="background: rgba(255,255,255,0.50); border-color: rgba(147,197,253,0.30); backdrop-filter: blur(8px);">
                <div class="flex items-center gap-2 mb-2.5">
                    <span class="w-2 h-2 rounded-full shrink-0" style="background: #38bdf8;"></span>
                    <p class="text-[9px] font-black uppercase tracking-widest" style="color: #94a3b8;">Location</p>
                </div>
                <p class="text-[13px] font-bold leading-snug line-clamp-1 transition-colors" style="color: #1e293b;">
                    {{ $location }}
                </p>
                <div class="mt-2 h-0.5 rounded-full w-6 group-hover/loc:w-10 transition-all duration-300"
                    style="background: rgba(56,189,248,0.35);"></div>
            </div>

            {{-- Contact --}}
            <div class="p-4 rounded-[1.4rem] border transition-all duration-300 hover:translate-x-1 group/cont"
                style="background: rgba(255,255,255,0.50); border-color: rgba(147,197,253,0.30); backdrop-filter: blur(8px);">
                <div class="flex items-center gap-2 mb-2.5">
                    <span class="w-2 h-2 rounded-full shrink-0" style="background: #34d399;"></span>
                    <p class="text-[9px] font-black uppercase tracking-widest" style="color: #94a3b8;">Contact</p>
                </div>
                <p class="text-[13px] font-bold tracking-wider transition-colors" style="color: #1e293b;">
                    {{ $mobile }}
                </p>
                <div class="mt-2 h-0.5 rounded-full w-6 group-hover/cont:w-10 transition-all duration-300"
                    style="background: rgba(52,211,153,0.35);"></div>
            </div>
        </div>
    </div>

    {{-- ── ACTION BUTTONS ── --}}
    <div class="relative flex gap-3">
        {{-- Full Profile CTA --}}
        <a href="{{ $beneficiaryDetailsUrl }}"
            class="flex-[1.6] group/btn relative overflow-hidden rounded-2xl px-4 py-4 transition-all duration-300 active:scale-[0.96] flex items-center justify-center gap-2.5"
            style="background: linear-gradient(135deg, #2563eb, #1d4ed8); border: 1px solid rgba(147,197,253,0.3); box-shadow: 0 6px 20px rgba(37,99,235,0.3);"
            onmouseover="this.style.boxShadow='0 10px 28px rgba(37,99,235,0.38)'; this.style.background='linear-gradient(135deg,#3b82f6,#2563eb)'"
            onmouseout="this.style.boxShadow='0 6px 20px rgba(37,99,235,0.3)'; this.style.background='linear-gradient(135deg,#2563eb,#1d4ed8)'">
            {{-- Shine sweep --}}
            <div class="absolute top-0 left-[-40px] w-8 h-full skew-x-[-20deg] opacity-0 group-hover/btn:opacity-100 pointer-events-none btn-shine-el"
                style="background: rgba(255,255,255,0.22); animation: none;"></div>
            <i
                class="fas fa-address-card text-white/90 drop-shadow group-hover/btn:rotate-12 transition-transform duration-300"></i>
            <span class="text-sm font-black text-white uppercase tracking-wider drop-shadow-sm">Full Profile</span>
        </a>

        {{-- Payment History --}}
        <a href="{{ $paymentUrl }}"
            class="flex-1 group/hist relative rounded-2xl px-4 py-4 transition-all duration-300 active:scale-[0.96] flex items-center justify-center gap-2"
            style="background: rgba(255,255,255,0.55); border: 1px solid rgba(147,197,253,0.45); backdrop-filter: blur(8px);"
            onmouseover="this.style.background='rgba(255,255,255,0.85)'; this.style.borderColor='rgba(99,179,237,0.6)'"
            onmouseout="this.style.background='rgba(255,255,255,0.55)'; this.style.borderColor='rgba(147,197,253,0.45)'">
            <i class="fa-solid fa-indian-rupee-sign group-hover/hist:-rotate-[30deg] transition-transform duration-300 drop-shadow"
                style="color: #6366f1;"></i>
            <span class="text-sm font-bold tracking-wide transition-colors" style="color: #334155;">Payment
                History</span>
        </a>
    </div>

</div>

@once
    @push('styles')
        <style>
            @keyframes shine-sweep {
                0% {
                    transform: translateX(-40px) skewX(-20deg);
                    opacity: 0;
                }

                20% {
                    opacity: 1;
                }

                100% {
                    transform: translateX(320px) skewX(-20deg);
                    opacity: 0;
                }
            }

            .beneficiary-card {
                backface-visibility: hidden;
                animation: bc-fade-up 0.5s ease both;
                transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .beneficiary-card:hover {
                border-color: rgba(37, 99, 235, 0.4);
                box-shadow: 0 20px 50px rgba(37, 99, 235, 0.15), 0 0 0 4px rgba(37, 99, 235, 0.05);
            }

            @keyframes bc-fade-up {
                from {
                    opacity: 0;
                    transform: translateY(16px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .beneficiary-card .group\/btn:hover .btn-shine-el {
                animation: shine-sweep 0.65s ease forwards;
            }
        </style>
    @endpush
@endonce

<script>
    window.viewDetails = (id) => console.log('view details', id);
    window.viewPayments = (id) => console.log('view payments', id);
</script>