@php
// Determine status color based on dynamic status
$statusLower = strtolower($status ?? '');
$dotColor = '#f97316'; // Default orange
$pillBg = 'rgba(249, 115, 22, 0.05)';
$pillBorder = 'rgba(249, 115, 22, 0.1)';
$pillText = '#ea580c';

if (str_contains($statusLower, 'reject') || str_contains($statusLower, 'cancel')) {
$dotColor = '#ef4444';
$pillBg = 'rgba(239, 68, 68, 0.05)';
$pillBorder = 'rgba(239, 68, 68, 0.1)';
$pillText = '#dc2626';
} elseif (str_contains($statusLower, 'approved') || str_contains($statusLower, 'success')) {
$dotColor = '#10b981';
$pillBg = 'rgba(16, 185, 129, 0.05)';
$pillBorder = 'rgba(16, 185, 129, 0.1)';
$pillText = '#059669';
}
@endphp

<div class="beneficiary-card group relative rounded-[2rem] p-6 shadow-[0_15px_40px_rgba(0,0,0,0.06)] transition-all duration-500 hover:-translate-y-2 hover:shadow-[0_20px_50px_rgba(249,115,22,0.12)] border border-gray-100 overflow-hidden flex flex-col bg-white"
    style="font-family: 'Plus Jakarta Sans', sans-serif; min-height: 520px;">

    {{-- ── DECORATIVE LEAVES AT BOTTOM ── --}}
    <div class="absolute bottom-0 left-0 right-0 h-40 bg-gradient-to-t from-orange-50/50 to-transparent pointer-events-none z-0"></div>

    <div class="absolute -bottom-4 -left-6 w-24 h-24 opacity-20 pointer-events-none rotate-12 transition-transform duration-700 group-hover:rotate-[20deg] z-0">
        <svg viewBox="0 0 100 100" fill="#f97316">
            <path d="M50 100C50 100 10 70 10 40C10 10 50 0 50 0C50 0 90 10 90 40C90 70 50 100 50 100Z" />
        </svg>
    </div>
    <div class="absolute -bottom-8 left-12 w-32 h-32 opacity-15 pointer-events-none -rotate-12 transition-transform duration-1000 group-hover:-rotate-[15deg] z-0">
        <svg viewBox="0 0 100 100" fill="#f97316">
            <path d="M50 100C50 100 10 70 10 40C10 10 50 0 50 0C50 0 90 10 90 40C90 70 50 100 50 100Z" />
        </svg>
    </div>
    <div class="absolute -bottom-6 right-0 w-28 h-28 opacity-20 pointer-events-none rotate-45 transition-transform duration-700 group-hover:rotate-[55deg] z-0">
        <svg viewBox="0 0 100 100" fill="#f97316">
            <path d="M50 100C50 100 10 70 10 40C10 10 50 0 50 0C50 0 90 10 90 40C90 70 50 100 50 100Z" />
        </svg>
    </div>
    <div class="absolute -bottom-10 -right-8 w-40 h-40 opacity-10 pointer-events-none -rotate-12 transition-transform duration-1000 group-hover:-rotate-[20deg] z-0">
        <svg viewBox="0 0 100 100" fill="#f97316">
            <path d="M50 100C50 100 10 70 10 40C10 10 50 0 50 0C50 0 90 10 90 40C90 70 50 100 50 100Z" />
        </svg>
    </div>

    {{-- ── HEADER ── --}}
    <div class="relative flex justify-between items-start mb-6 z-10">
        <div class="flex items-center gap-2 px-3 py-1.5 rounded-full border text-[10px] font-black uppercase tracking-widest"
            style="background: {{ $pillBg }}; border-color: {{ $pillBorder }}; color: {{ $pillText }};">
            <span class="w-2 h-2 rounded-full animate-pulse" style="background: {{ $dotColor }}; box-shadow: 0 0 8px {{ $dotColor }};"></span>
            {{ $status }}
        </div>
        <div class="text-right">
            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">Application ID</p>
            <p class="text-xs font-mono font-bold tracking-wider text-orange-600">{{ $applicationId }}</p>
        </div>
    </div>

    {{-- ── PROFILE ── --}}
    <div class="relative flex items-center gap-4 mb-6 z-10">
        <div class="relative shrink-0">
            <div class="absolute -inset-1 rounded-full bg-orange-500 opacity-20 blur-[4px]"></div>
            @if ($ben_profile_pic && isset($ben_profile_pic['attched_document']))
            @php
            $document_mime_type = $ben_profile_pic['document_mime_type'] ?? 'image/jpeg';
            $image_extension = ($document_mime_type == 'image/png') ? 'png' : 'jpg';
            $row_image = "data:image/" . $image_extension . ";base64," . $ben_profile_pic['attched_document'];
            @endphp
            <img src="{{ $row_image }}" alt="Profile" class="relative w-16 h-16 rounded-full border-2 border-white object-cover shadow-sm">
            @else
            <div class="relative w-16 h-16 rounded-full flex items-center justify-center border-2 border-white shadow-sm"
                style="background: linear-gradient(135deg, #f97316, #ea580c);">
                <i class="fas fa-user text-white text-2xl"></i>
            </div>
            @endif
        </div>
        <div class="min-w-0">
            <h3 class="text-2xl font-bold text-slate-800 truncate leading-tight">
                {{ $name }}
            </h3>
            <div class="flex items-center gap-2 mt-1">
                <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-tighter border border-orange-500/20"
                    style="background: rgba(249, 115, 22, 0.05); color: #ea580c;">
                    {{ $relation }}
                </span>
                <span class="text-sm font-medium text-slate-500 truncate">{{ $relationName }}</span>
            </div>
        </div>
    </div>

    {{-- ── INFO SECTION (Individual Containers) ── --}}
    <div class="relative space-y-3 mb-8 z-10">
        @php
        $infoItems = [
        ['icon' => 'fa-shield-alt', 'label' => 'Applied Scheme', 'value' => $schemeName],
        ['icon' => 'fa-map-marker-alt', 'label' => 'Location', 'value' => $location],
        ['icon' => 'fa-phone-alt', 'label' => 'Contact', 'value' => $mobile],
        ];
        @endphp

        @foreach($infoItems as $item)
        <div class="flex items-center gap-4 p-3 rounded-2xl border border-orange-500/5 transition-all duration-300 hover:border-orange-500/20 bg-orange-50/20 backdrop-blur-sm">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-white shadow-sm border border-orange-100">
                <i class="fas {{ $item['icon'] }} text-sm text-orange-500"></i>
            </div>
            <div class="min-w-0">
                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest leading-none mb-1">{{ $item['label'] }}</p>
                <p class="text-[13px] font-bold text-slate-700 truncate">{{ $item['value'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ── ACTIONS ── --}}
    <div class="relative mt-auto pt-2 space-y-3 z-10">
        <a href="{{ $beneficiaryDetailsUrl }}"
            class="group/btn flex items-center justify-center gap-3 w-full py-4 rounded-xl text-sm font-black uppercase tracking-widest text-white transition-all duration-300 shadow-[0_8px_20px_rgba(249,115,22,0.2)] hover:shadow-[0_12px_28px_rgba(249,115,22,0.3)] hover:-translate-y-1 active:scale-95"
            style="background: linear-gradient(135deg, #f97316, #ea580c);">
            View Full Profile
            <i class="fas fa-arrow-right text-xs transition-transform duration-300 group-hover/btn:translate-x-1"></i>
        </a>

        <a href="{{ $paymentUrl }}"
            class="flex items-center justify-center gap-3 w-full py-4 rounded-xl text-sm font-black uppercase tracking-widest text-orange-600 border-2 border-orange-500/20 bg-white/80 backdrop-blur-sm transition-all duration-300 hover:border-emerald-500/40 hover:bg-orange-50/30">
            <i class="fas fa-indian-rupee-sign text-xs"></i>
            Payment History
        </a>
    </div>

</div>

@once
@push('styles')
<style>
    .beneficiary-card {
        backface-visibility: hidden;
        animation: bc-reveal 0.8s cubic-bezier(0.16, 1, 0.3, 1) both;
    }

    @keyframes bc-reveal {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
@endpush
@endonce