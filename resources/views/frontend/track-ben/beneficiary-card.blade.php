<div
    class="beneficiary-card group relative bg-slate-900 rounded-[2rem] p-6 shadow-2xl transition-all duration-500 hover:-translate-y-2 hover:shadow-indigo-500/20 border border-white/5 overflow-hidden">

    <!-- Dynamic Background Mesh -->
    <div
        class="absolute inset-0 bg-gradient-to-br from-indigo-600/20 via-transparent to-sky-400/10 opacity-50 group-hover:opacity-100 transition-opacity duration-500">
    </div>

    <!-- Header: Status & ID -->
    <div class="relative flex justify-between items-start mb-8">
        <div class="backdrop-blur-xl bg-white/5 border border-white/10 px-3 py-1.5 rounded-2xl shadow-inner">
            <div class="flex items-center gap-2.5">
                <span class="relative flex h-2.5 w-2.5">
                    @if($status == 'Approved')
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                    @else
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-orange-600"></span>
                    @endif
                </span>
                <span class="text-[11px] font-bold uppercase tracking-widest text-white/90">
                    {{ $status }}
                </span>
            </div>
        </div>

        <div class="flex flex-col items-end">
            <span class="text-[10px] text-white/40 uppercase font-bold tracking-tighter mb-1">Beneficiary ID </span>
            <div class="bg-indigo-500/10 border border-indigo-400/20 px-3 py-1 rounded-lg">
                <span class="text-indigo-300 font-mono font-bold text-sm tracking-wider">{{ $beneficiaryId }}</span>
            </div>
        </div>
    </div>

    <!-- Beneficiary Identity -->
    <div class="relative mb-8">
        <div class="flex items-center gap-5">
            <div class="relative">
                <div
                    class="absolute inset-0 bg-indigo-500 blur-xl opacity-20 group-hover:opacity-40 transition-opacity">
                </div>
                <div
                    class="relative w-14 h-14 rounded-2xl bg-gradient-to-tr from-indigo-600 to-violet-500 flex items-center justify-center shadow-2xl border border-white/20 group-hover:rotate-6 transition-transform duration-300">
                    <i class="fas fa-user text-white text-xl"></i>
                </div>
            </div>

            <div class="flex-1">
                <h3 class="text-2xl font-bold text-white tracking-tight group-hover:text-indigo-200 transition-colors">
                    {{ $name }}
                </h3>
                <div class="flex items-center gap-2 mt-1.5">
                    <span
                        class="text-xs font-semibold text-indigo-400/90 uppercase tracking-wider">{{ $relation }}</span>
                    <span class="w-1 h-1 rounded-full bg-white/20"></span>
                    <span class="text-sm text-white/60 font-medium">{{ $relationName }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Information Grid -->
    <div class="relative space-y-3 mb-8">
        <!-- Scheme Row -->
        <div
            class="flex items-center p-3 rounded-2xl bg-white/[0.03] border border-white/5 hover:bg-white/[0.06] transition-colors">
            <div
                class="w-9 h-9 rounded-xl bg-indigo-500/10 flex items-center justify-center mr-4 border border-indigo-500/20">
                <i class="fas fa-project-diagram text-indigo-400 text-xs"></i>
            </div>
            <div class="overflow-hidden">
                <p class="text-[10px] text-white/40 uppercase font-bold tracking-widest">Program Scheme</p>
                <p class="text-sm text-white/90 font-semibold truncate">{{ $schemeName }}</p>
            </div>
        </div>

        <!-- Location & Contact Grid -->
        <div class="grid grid-cols-2 gap-3">
            <div class="p-3 rounded-2xl bg-white/[0.03] border border-white/5">
                <i class="fas fa-map-marker-alt text-sky-400 text-xs mb-2 block"></i>
                <p class="text-[10px] text-white/40 uppercase font-bold mb-0.5">Location</p>
                <p class="text-[13px] text-white/90 font-semibold leading-tight">{{ $location }}</p>
            </div>
            <div class="p-3 rounded-2xl bg-white/[0.03] border border-white/5">
                <i class="fas fa-phone text-emerald-400 text-xs mb-2 block"></i>
                <p class="text-[10px] text-white/40 uppercase font-bold mb-0.5">Contact</p>
                <p class="text-[13px] text-white/90 font-semibold leading-tight tracking-wider">{{ $mobile }}</p>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="relative flex gap-3">
        <button onclick="viewDetails('{{ $beneficiaryId }}')"
            class="flex-[1.5] group/btn relative overflow-hidden rounded-2xl bg-indigo-600 px-4 py-3.5 transition-all hover:bg-indigo-500 active:scale-95 shadow-lg shadow-indigo-600/20">
            <div class="flex items-center justify-center gap-2">
                <i class="fas fa-id-card text-white/80 group-hover/btn:scale-110 transition-transform"></i>
                <span class="text-sm font-bold text-white tracking-wide">View Profile</span>
            </div>
        </button>

        <button onclick="viewPayments('{{ $beneficiaryId }}')"
            class="flex-1 rounded-2xl bg-white/[0.05] border border-white/10 px-4 py-3.5 transition-all hover:bg-white/10 hover:border-white/20 active:scale-95 flex items-center justify-center gap-2">
            <i class="fas fa-history text-indigo-400 text-xs"></i>
            <span class="text-sm font-bold text-white/80 tracking-wide">History</span>
        </button>
    </div>

    <!-- Subtle Glass Edge Highlight -->
    <div class="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>
</div>