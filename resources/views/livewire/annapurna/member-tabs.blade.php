{{-- Horizontal Member Navigation Tabs --}}
<div class="flex flex-wrap items-stretch border-b-2 border-amber-600 gap-1 pb-0">
    <!-- HOF Tab -->
    @php
        $isHofClickable = $this->isMemberClickable(0);
    @endphp
    <button type="button"
        @if ($isHofClickable)
            wire:click="selectMember(0)"
            x-on:click="Livewire.dispatch('showLoader')"
        @else
            disabled
        @endif
        class="px-4 py-2.5 rounded-t-lg font-bold text-xs md:text-sm transition-all duration-150 flex items-center gap-2 border-t border-x {{ $activeMemberIndex === 0 ? 'active-tab shadow-inner' : 'inactive-tab hover:bg-orange-100' }} {{ !$isHofClickable ? 'opacity-50 cursor-not-allowed pointer-events-none' : '' }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
        </svg>
        <div class="text-left">
            <div class="leading-none">Head of Family (HoF)</div>
            <div class="text-[9px] opacity-80 font-normal mt-0.5">পরিবার প্রধান</div>
        </div>
    </button>

    <!-- Member Tabs -->
    @foreach ($members as $index => $member)
        @php
            $memberTabNo = $index + 2;
            $memberName = trim($member['name']) !== '' ? $member['name'] : "Member {$memberTabNo}";
            $isActive = $activeMemberIndex === $index + 1;
            $isMClickable = $this->isMemberClickable($index + 1);
        @endphp
        <div class="relative flex items-stretch" wire:key="member-tab-{{ $index }}">
            <button type="button"
                @if ($isMClickable)
                    wire:click="selectMember({{ $index + 1 }})"
                    x-on:click="Livewire.dispatch('showLoader')"
                @else
                    disabled
                @endif
                class="pl-4 pr-8 py-2.5 rounded-t-lg font-bold text-xs md:text-sm transition-all duration-150 flex items-center gap-2 border-t border-x {{ $isActive ? 'active-tab shadow-inner' : 'inactive-tab hover:bg-orange-100' }} {{ !$isMClickable ? 'opacity-50 cursor-not-allowed pointer-events-none' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <div class="text-left">
                    <div class="leading-none truncate max-w-[120px]">{{ $memberName }}</div>
                    <div class="text-[9px] opacity-80 font-normal mt-0.5">সদস্য {{ $memberTabNo }}</div>
                </div>
            </button>
            <button type="button" wire:click="removeMember({{ $index }})"
                x-on:click="Livewire.dispatch('showLoader')"
                class="absolute right-1.5 top-1/2 -translate-y-1/2 p-1 rounded-full {{ $isActive ? 'text-white hover:bg-amber-800' : 'text-red-500 hover:bg-red-50 hover:text-red-700' }} transition"
                title="Remove Member">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    @endforeach

    <!-- Add Member Tab Button -->
    @if ($this->isMemberFullyFilled($activeMemberIndex))
        <button type="button" wire:click="addMember"
            x-on:click="Livewire.dispatch('showLoader')"
            class="px-4 py-2 rounded-t-lg bg-emerald-600 text-white hover:bg-emerald-700 font-bold text-xs transition duration-150 flex items-center gap-1.5 self-center ml-2 border border-emerald-600 shadow shadow-emerald-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 4v16m8-8H4" />
            </svg>
            <span>Add Member / সদস্য যোগ করুন</span>
        </button>
    @endif
</div>
