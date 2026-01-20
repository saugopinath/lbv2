
<div class="px-6 pt-4 border-b shrink-0">
    <nav class="flex space-x-6">

        @foreach($views as $view)
            @php $tab = $tabs[$view] ?? null; @endphp

            <button wire:click="setActiveTab({{ $view }})" class="flex items-center gap-2 pb-2 text-sm font-medium  
                        {{ $activeTab == $view
            ? 'border-indigo-600 text-indigo-600'
            : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                <x-entrytab-nav-link :active="$activeTab == $view" :icon="$tab?->tab_icon">
                    {{ $tab?->tab_name ?? 'Tab ' . $view }}
                </x-entrytab-nav-link>
            </button>

        @endforeach

    </nav>
    @if($activeTab)
        <div class="border rounded p-4">

            @includeIf(
                "schemes.scheme_{$schemeId}.{$activeTab}",
                ['schemeId' => $schemeId]
            )

            </div>
    @endif
</div>
