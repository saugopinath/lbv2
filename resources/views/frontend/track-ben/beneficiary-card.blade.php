@php
$statusLower = strtolower($status ?? '');
$dotColor = '#4f46e5';
$pillBg = '#eef2ff';
$pillBorder = '#e0e7ff';
$pillText = '#4338ca';

if (str_contains($statusLower, 'reject') || str_contains($statusLower, 'cancel')) {
$dotColor = '#ef4444';
$pillBg = '#fef2f2';
$pillBorder = '#fee2e2';
$pillText = '#b91c1c';
} elseif (str_contains($statusLower, 'approved') || str_contains($statusLower, 'success')) {
$dotColor = '#10b981';
$pillBg = '#f0fdf4';
$pillBorder = '#dcfce7';
$pillText = '#15803d';
} elseif (str_contains($statusLower, 'pend') || str_contains($statusLower, 'process')) {
$dotColor = '#f59e0b';
$pillBg = '#fffbeb';
$pillBorder = '#fef3c7';
$pillText = '#b45309';
}
@endphp

<div class="beneficiary-card group relative rounded-[1.5rem] p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] transition-all duration-500 hover:-translate-y-1.5 hover:shadow-[0_20px_40px_rgba(79,70,229,0.08)] border border-slate-100 overflow-hidden flex flex-col bg-white"
    style="font-family: 'Plus Jakarta Sans', sans-serif; min-height: 480px;">

    {{-- ── DECORATIVE AMBIENT GLOW ── --}}
    <div class="absolute -top-24 -right-24 w-64 h-64 rounded-full pointer-events-none opacity-[0.03]"
        style="background: radial-gradient(circle, #4f46e5 0%, transparent 70%);"></div>

    {{-- ── HEADER ── --}}
    <div class="relative flex justify-between items-start mb-6">
        <div class="flex items-center gap-2 px-3 py-1.5 rounded-full border text-[10px] font-bold uppercase tracking-wider"
            style="background: {{ $pillBg }}; border-color: {{ $pillBorder }}; color: {{ $pillText }};">
            <span class="w-2 h-2 rounded-full" style="background: {{ $dotColor }};"></span>
            {{ $status }}
        </div>
        <div class="text-right">
            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Application ID</p>
            <p class="text-xs font-mono font-bold tracking-wider text-indigo-600">{{ $applicationId }}</p>
        </div>
    </div>

    {{-- ── PROFILE SECTION ── --}}
    <div class="relative flex items-center gap-4 mb-8">
        <div class="relative shrink-0">
            <div class="absolute -inset-1 rounded-full bg-indigo-500 opacity-10 blur-[4px]"></div>
            @if ($ben_profile_pic && isset($ben_profile_pic['attched_document']))
            @php
            $document_mime_type = $ben_profile_pic['document_mime_type'] ?? 'image/jpeg';
            $image_extension = ($document_mime_type == 'image/png') ? 'png' : 'jpg';
            $isUuid = \Illuminate\Support\Str::isUuid($ben_profile_pic['attched_document'] ?? '');
            $row_image = $isUuid 
                ? route('document.view', $ben_profile_pic['id']) 
                : "data:image/" . $image_extension . ";base64," . $ben_profile_pic['attched_document'];
            @endphp
            <img src="{{ $row_image }}" alt="Profile" class="relative w-14 h-14 rounded-full border border-slate-200 object-cover">
            @else
            <div class="relative w-14 h-14 rounded-full flex items-center justify-center border border-slate-200 bg-slate-50">
                <i class="fas fa-user text-slate-400 text-xl"></i>
            </div>
            @endif
        </div>
        <div class="min-w-0">
            <h3 class="text-xl font-bold text-slate-800 truncate leading-tight group-hover:text-indigo-600 transition-colors">
                {{ $name }}
            </h3>
            <div class="flex items-center gap-2 mt-1">
                <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-tighter bg-slate-100 text-slate-600 border border-slate-200">
                    {{ $relation }}
                </span>
                <span class="text-sm font-medium text-slate-500 truncate">{{ $relationName }}</span>
            </div>
        </div>
    </div>

    {{-- ── INFO SECTION ── --}}
    <div class="relative flex-1 space-y-4 mb-8">
        @php
        $infoItems = [
        ['icon' => 'fa-shield-alt', 'label' => 'Applied Scheme', 'value' => $schemeName, 'color' => 'indigo'],
        ['icon' => 'fa-map-marker-alt', 'label' => 'Location', 'value' => $location, 'color' => 'slate'],
        ['icon' => 'fa-phone-alt', 'label' => 'Contact', 'value' => $mobile, 'color' => 'slate'],
        ];
        @endphp

        @foreach($infoItems as $item)
        <div class="flex items-center gap-4 group/item">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center transition-all duration-300 bg-slate-50 border border-slate-100 group-hover/item:bg-white group-hover/item:border-indigo-100 group-hover/item:shadow-sm">
                <i class="fas {{ $item['icon'] }} text-xs text-slate-400 group-hover/item:text-indigo-500"></i>
            </div>
            <div class="min-w-0">
                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">{{ $item['label'] }}</p>
                <p class="text-sm font-semibold text-slate-700 truncate">{{ $item['value'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ── ACTIONS ── --}}
    <div class="relative space-y-3">
        <form action="{{ $beneficiaryDetailsUrl }}" method="POST" onsubmit="window.dispatchEvent(new CustomEvent('showLoader'))">
            @csrf
            <input type="hidden" name="id" value="{{ $encryptedId }}">
            <button type="submit"
                class="flex items-center justify-center gap-2 w-full py-3 rounded-xl text-sm font-bold text-white transition-all duration-300 shadow-sm hover:shadow-md hover:-translate-y-0.5 active:scale-95"
                style="background: #4f46e5;">
                View Full Profile
                <i class="fas fa-arrow-right text-[10px]"></i>
            </button>
        </form>

        <form action="{{ $paymentUrl }}" method="POST" onsubmit="window.dispatchEvent(new CustomEvent('showLoader'))">
            @csrf
            <input type="hidden" name="id" value="{{ $encryptedId }}">
            <button type="submit"
                class="flex items-center justify-center gap-2 w-full py-3 rounded-xl text-sm font-bold text-slate-600 border border-slate-200 bg-white transition-all duration-300 hover:border-indigo-200 hover:bg-slate-50 hover:text-indigo-600">
                <i class="fas fa-indian-rupee-sign text-[10px]"></i>
                Payment History
            </button>
        </form>
    </div>

</div>

@once
@push('styles')
<style>
    .beneficiary-card {
        backface-visibility: hidden;
        animation: bc-reveal-smooth 0.6s cubic-bezier(0.22, 1, 0.36, 1) both;
    }

    @keyframes bc-reveal-smooth {
        from {
            opacity: 0;
            transform: translateY(12px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
@endpush
@endonce