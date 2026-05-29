{{-- Left Sidebar: Vertical Navigation Menu --}}
<div class="form-sidebar-left space-y-2">
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-4 bg-orange-50 border-b border-orange-100">
            <h4 class="text-xs font-bold text-amber-800 uppercase tracking-wider">Form Sections</h4>
            <p class="text-[10px] text-amber-600">আবেদনপত্রের বিভাগসমূহ</p>
        </div>
        <nav class="p-2 space-y-1">
            @foreach ($this->getSections() as $secKey => $secVal)
                @php
                    $isActive = $activeSection === $secKey;
                    $isHofOnly = false;
                    $isMember = $activeMemberIndex > 0;
                @endphp
                <button type="button" wire:click="selectSection('{{ $secKey }}')"
                    class="w-full text-left px-3 py-2.5 rounded-md flex items-center gap-3 transition-all duration-150 {{ $isActive ? 'active-sidebar shadow-sm' : 'inactive-sidebar' }}">
                    <div
                        class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold {{ $isActive ? 'active-sidebar-badge' : 'inactive-sidebar-badge' }}">
                        @if ($secKey === 'family_identity')
                            A
                        @elseif ($secKey === 'ration_subsidy')
                            B
                        @elseif ($secKey === 'assets')
                            C
                        @elseif ($secKey === 'income_profession')
                            D
                        @elseif ($secKey === 'other_docs')
                            E
                        @elseif ($secKey === 'social_dependents')
                            F
                        @elseif ($secKey === 'gov_benefits')
                            G
                        @elseif ($secKey === 'declaration')
                            H
                        @endif
                    </div>
                    <div>
                        <div class="text-xs md:text-sm leading-tight font-bold">{{ $secVal['label'] }}</div>
                        <div class="text-[10px] opacity-80 leading-none mt-0.5">{{ $secVal['bengali'] }}</div>
                    </div>
                </button>
            @endforeach
        </nav>
    </div>

    {{-- Instructions Panel --}}
    <div
        class="bg-amber-50 border border-amber-200 rounded-lg p-4 text-xs text-amber-900 leading-relaxed shadow-sm">
        <span class="font-bold flex items-center gap-1.5 mb-1.5 text-amber-950">
            <svg class="w-4 h-4 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Key Instructions
        </span>
        <ul class="list-disc pl-4 space-y-1">
            <li>Name must match official Aadhaar.</li>
            <li>Address is common for all family members.</li>
            <li>Each member applying for AY must declare bank details.</li>
        </ul>
    </div>
</div>
