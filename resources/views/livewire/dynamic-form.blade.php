
<div class="px-6 pt-4 shrink-0">
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

           @includeIf("schemes.scheme_{$schemeId}.{$activeTab}",['schemeId' => $schemeId])       
        </div>

            {{-- ACTION BUTTONS --}}
        <div class="flex justify-between mt-6">

            {{-- LEFT --}}
            <div>
                @if(!$isFirst && $prevTab)
                    <button
                        wire:click="setActiveTab({{ $prevTab }})"
                        class="px-4 py-2 bg-gray-500 text-white rounded">
                        Previous
                    </button>
                @endif
            </div>

            {{-- RIGHT --}}
            <div class="flex gap-2">
                @if(!$isLast && $nextTab)
                    <button
                        wire:click="saveAndNext({{ $nextTab }})"
                        class="px-4 py-2 bg-indigo-600 text-white rounded">
                        Save & Next
                    </button>
                @else
                    <button
                        wire:click="finalSubmit"
                        class="px-4 py-2 bg-green-600 text-white rounded">
                        Submit
                    </button>
                @endif
            </div>

        </div>
    @endif
</div>
