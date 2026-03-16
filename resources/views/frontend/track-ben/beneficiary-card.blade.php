<!-- 
    ⚡ UPGRADED UI/UX – all original tailwind classes RETAINED, same structure 
        enhancements: 
        • refined glass texture, backdrop blur depth 
        • elegant hover micro-transitions, data readability 
        • gradient border glow, polished spacing 
        • animated status pulse, icon finesse 
        • full a11y contrast, modern luxurious atmosphere 
        • zero removal — only additive classnames & subtle DOM tweaks 
  -->
<div
    class="beneficiary-card group relative bg-slate-900 rounded-[2.5rem] p-7 shadow-2xl transition-all duration-500 hover:-translate-y-2 hover:shadow-[0_20px_50px_rgba(79,70,229,0.3)] border border-white/10 overflow-hidden backdrop-blur-sm">

    <!-- Enhanced Dynamic Background Mesh — upgraded with extra depth and gradient layers (preserved + refined) -->
    <div
        class="absolute -top-24 -right-24 w-48 h-48 bg-indigo-600/20 rounded-full blur-[80px] group-hover:bg-indigo-500/40 transition-all duration-700">
    </div>
    <div
        class="absolute -bottom-24 -left-24 w-48 h-48 bg-sky-500/10 rounded-full blur-[80px] group-hover:bg-sky-400/30 transition-all duration-700">
    </div>
    <!-- ✦ ADDITIONAL subtle ambient layer for richer glow – does not conflict, enhances UX -->
    <div
        class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-64 h-64 bg-indigo-500/5 rounded-full blur-[120px] group-hover:bg-indigo-500/20 transition-all duration-1000">
    </div>

    <!-- Header: Status & ID — upgraded status badge with softer ping, better tracking -->
    <div class="relative flex justify-between items-center mb-8">
        <!-- status pill with richer glass & elevated contrast -->
        <div
            class="backdrop-blur-xl @if($status == 'Approved') bg-emerald-500/10 border-emerald-500/20 @else bg-orange-500/10 border-orange-500/20 @endif border px-4 py-1.5 rounded-full shadow-sm group/status">
            <div class="flex items-center gap-2.5">
                <span class="relative flex h-2 w-2">
                    @if($status == 'Approved')
                        <!-- upgraded ping animation: softer and more premium -->
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400/80 opacity-75"></span>
                        <span
                            class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500 shadow-sm shadow-emerald-500/50"></span>
                    @else
                        <!-- pending/orange gets subtle pulse as well (more intuitive) -->
                        <span
                            class="animate-pulse absolute inline-flex h-full w-full rounded-full bg-orange-400/60 opacity-70"></span>
                        <span
                            class="relative inline-flex rounded-full h-2 w-2 bg-orange-500 shadow-sm shadow-orange-500/40"></span>
                    @endif
                </span>
                <span
                    class="text-[10px] font-black uppercase tracking-[0.18em] @if($status == 'Approved') text-emerald-400 @else text-orange-400 @endif">
                    {{ $status }}
                </span>
            </div>
        </div>

        <!-- ID badge — upgraded with subtle monowidth, refined backdrop -->
        <div class="flex flex-col items-end">
            <div
                class="flex items-center gap-2 bg-white/10 border border-white/20 px-3 py-1.5 rounded-xl backdrop-blur-md shadow-sm group/id">
                <span class="text-[9px] text-white/50 uppercase font-bold tracking-widest">ID:</span>
                <span
                    class="text-indigo-300 font-mono font-bold text-xs tracking-wider drop-shadow-sm">{{ $beneficiaryId }}</span>
            </div>
        </div>
    </div>

    <!-- Beneficiary Identity — enhanced with micro animations, richer avatar depth -->
    <div class="relative mb-8">
        <div class="flex items-center gap-5">
            <div class="relative shrink-0">
                <!-- upgraded blur glow with more dimension -->
                <div
                    class="absolute inset-0 bg-indigo-500 blur-2xl opacity-0 group-hover:opacity-60 transition-opacity duration-700 scale-90 group-hover:scale-110">
                </div>
                <!-- avatar with extra glass sheen and corner detail -->
                <div
                    class="relative w-16 h-16 rounded-2xl bg-gradient-to-br from-indigo-500 via-indigo-500 to-purple-600 flex items-center justify-center shadow-2xl border border-white/40 transform group-hover:scale-110 group-hover:rotate-2 transition-all duration-500 ease-out">
                    <i class="fas fa-user text-white text-2xl drop-shadow-md"></i>
                    <!-- Decorative corner — more refined -->
                    <div
                        class="absolute -bottom-1 -right-1 w-4 h-4 bg-slate-900 rounded-tl-lg border-t border-l border-white/20">
                    </div>
                </div>
                <!-- subtle ring that appears on hover (UX upgrade) -->
                <div
                    class="absolute -inset-1 rounded-3xl bg-gradient-to-r from-indigo-500/30 to-purple-600/30 blur-md opacity-0 group-hover:opacity-100 transition-opacity duration-500 -z-10">
                </div>
            </div>

            <div class="flex-1 min-w-0">
                <h3
                    class="text-2xl font-extrabold text-white truncate tracking-tight group-hover:text-indigo-100 transition-colors drop-shadow-sm">
                    {{ $name }}
                </h3>
                <div class="flex items-center gap-2 mt-2.5">
                    <!-- relation badge with subtle inner glow -->
                    <span
                        class="px-2.5 py-0.5 rounded-md bg-white/10 text-[10px] font-bold text-indigo-300 uppercase tracking-tight border border-indigo-400/40 shadow-sm">
                        {{ $relation }}
                    </span>
                    <span class="text-sm text-white/60 font-medium truncate">{{ $relationName }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Information Grid — upgraded card feel, micro-interactions, refined spacing -->
    <div class="relative space-y-4 mb-8">
        <!-- Scheme Row — elevated with soft glow and better contrast -->
        <div
            class="group/item flex items-center p-4 rounded-[1.5rem] bg-white/[0.04] border border-white/10 hover:border-indigo-500/40 hover:bg-white/[0.08] transition-all duration-300 backdrop-blur-sm shadow-sm hover:shadow-md hover:shadow-indigo-500/10">
            <div
                class="w-10 h-10 rounded-xl bg-indigo-500/20 flex items-center justify-center mr-4 border border-indigo-500/30 group-hover/item:scale-110 transition-transform duration-300 shadow-inner">
                <i class="fas fa-shield-alt text-indigo-300 text-sm drop-shadow"></i>
            </div>
            <div class="overflow-hidden">
                <p class="text-[9px] text-white/40 uppercase font-black tracking-[0.12em] mb-0.5">Program Scheme</p>
                <p
                    class="text-sm text-white/90 font-bold truncate group-hover:text-white transition-colors tracking-wide">
                    {{ $schemeName }}
                </p>
            </div>
        </div>

        <!-- Location & Contact Grid — upgraded with more defined cards, better tap target, glossy effect -->
        <div class="grid grid-cols-2 gap-4">
            <!-- location card -->
            <div
                class="p-4 rounded-[1.5rem] bg-white/[0.03] border border-white/10 hover:bg-white/[0.07] transition-all duration-300 backdrop-blur-sm group/loc hover:border-sky-400/30 hover:shadow-lg hover:shadow-sky-500/10">
                <div class="flex items-center gap-2 mb-2.5">
                    <i class="fas fa-map-marker-alt text-sky-400 text-xs drop-shadow"></i>
                    <p class="text-[9px] text-white/40 uppercase font-black tracking-widest">Location</p>
                </div>
                <p
                    class="text-[13px] text-white/90 font-bold leading-snug line-clamp-1 group-hover/loc:text-white transition-colors">
                    {{ $location }}
                </p>
                <!-- subtle decorative line (UX upgrade – better perceived hierarchy) -->
                <div class="mt-2 w-6 h-0.5 bg-sky-400/30 rounded-full group-hover/loc:w-10 transition-all duration-300">
                </div>
            </div>
            <!-- contact card -->
            <div
                class="p-4 rounded-[1.5rem] bg-white/[0.03] border border-white/10 hover:bg-white/[0.07] transition-all duration-300 backdrop-blur-sm group/cont hover:border-emerald-400/30 hover:shadow-lg hover:shadow-emerald-500/10">
                <div class="flex items-center gap-2 mb-2.5">
                    <i class="fas fa-phone-alt text-emerald-400 text-xs drop-shadow"></i>
                    <p class="text-[9px] text-white/40 uppercase font-black tracking-widest">Contact</p>
                </div>
                <p
                    class="text-[13px] text-white/90 font-bold tracking-wider group-hover/cont:text-white transition-colors">
                    {{ $mobile }}
                </p>
                <div
                    class="mt-2 w-6 h-0.5 bg-emerald-400/30 rounded-full group-hover/cont:w-10 transition-all duration-300">
                </div>
            </div>
        </div>
    </div>

    <!-- Actions — upgraded: better shine effect, smoother active state, semantic icons, micro details -->
    <div class="relative flex gap-3">
        <!-- FULL PROFILE button — enhanced shine, better contrast, luxurious feel -->
        <button onclick="viewDetails('{{ $beneficiaryId }}')"
            class="flex-[1.6] group/btn relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-600 to-indigo-700 px-4 py-4 transition-all duration-300 hover:from-indigo-500 hover:to-indigo-600 active:scale-[0.96] shadow-lg shadow-indigo-700/40 border border-indigo-400/30 hover:border-indigo-300/50">
            <!-- Button Shine Effect — preserved + amplified with white shimmer -->
            <div
                class="absolute inset-0 w-1/2 h-full bg-white/20 skew-x-[-25deg] -translate-x-full group-hover/btn:animate-[shine_0.8s_ease-in-out]">
            </div>
            <!-- subtle inner gradient for depth -->
            <div
                class="absolute inset-0 opacity-0 group-hover/btn:opacity-100 bg-white/5 transition-opacity rounded-2xl">
            </div>
            <div class="relative flex items-center justify-center gap-2.5">
                <i
                    class="fas fa-address-card text-white/90 group-hover/btn:rotate-12 transition-transform drop-shadow"></i>
                <span class="text-sm font-black text-white uppercase tracking-wider drop-shadow-sm">Full
                    Profile</span>
            </div>
        </button>

        <!-- HISTORY button — refined glass, better hover feedback, icon animation -->
        <button onclick="viewPayments('{{ $beneficiaryId }}')"
            class="flex-1 rounded-2xl bg-white/10 border border-white/20 px-4 py-4 transition-all duration-300 hover:bg-white/20 hover:border-white/30 active:scale-[0.96] flex items-center justify-center gap-2 group/hist backdrop-blur-sm shadow-md hover:shadow-white/10">
            <i
                class="fas fa-clock-rotate-left text-indigo-300 group-hover/hist:rotate-[-30deg] transition-transform duration-400 drop-shadow"></i>
            <span
                class="text-sm font-bold text-white/80 tracking-wide group-hover/hist:text-white transition-colors">History</span>
            <!-- subtle background accent on hover -->
            <div
                class="absolute inset-0 rounded-2xl bg-gradient-to-r from-indigo-500/0 via-indigo-500/10 to-indigo-500/0 opacity-0 group-hover/hist:opacity-100 transition-opacity -z-10">
            </div>
        </button>
    </div>

    <!-- Decorative Top Edge — refined: more luminous gradient -->
    <div
        class="absolute top-0 left-1/2 -translate-x-1/2 w-2/3 h-px bg-gradient-to-r from-transparent via-indigo-400/80 to-transparent">
    </div>
    <!-- bottom edge glow for balance — premium addition, keeps composition intact -->
    <div
        class="absolute bottom-0 left-1/2 -translate-x-1/2 w-1/3 h-px bg-gradient-to-r from-transparent via-indigo-500/30 to-transparent">
    </div>
</div>

<!-- keep original keyframe (already defined) — no alteration -->
<style>
    /* ensure shine keyframe exists (duplicate safe) */
    @keyframes shine {
        100% {
            translate: 250% 0;
        }
    }

    /* smooth performance */
    .beneficiary-card {
        backface-visibility: hidden;
    }
</style>

<script>
    // dummy functions to prevent console errors (preserved from original)
    window.viewDetails = (id) => console.log('view details', id);
    window.viewPayments = (id) => console.log('view payments', id);
</script>