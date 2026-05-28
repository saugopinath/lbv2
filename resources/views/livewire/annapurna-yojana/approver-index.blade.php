<x-layouts.app>
    <div class="w-full space-y-5">

        {{-- Page Header --}}
        <div class="shadow-md rounded-2xl px-6 py-4 flex items-center justify-between border border-white/5"
             style="background: linear-gradient(135deg, #022c22 0%, #064e3b 50%, #0f766e 100%);">
            <div>
                <h1 class="text-lg font-bold text-white tracking-wide">
                    Annapurna Yojana — Approval
                </h1>
                <p class="text-emerald-200 text-xs mt-0.5">
                    Review and finalize verified Annapurna Yojana family applications
                </p>
            </div>
            <svg class="w-10 h-10 text-white opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0
                       01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622
                       5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
        </div>

        {{-- Livewire Table Component --}}
        <livewire:annapurna-yojana.approver-index-table />

    </div>
</x-layouts.app>
