<div>
    @if ($showTabs)
    @php
    $tabs = [
    'tab1' => ['label' => 'Personal Details', 'component' => 'personal-details', 'enabled' => true],
    'tab2' => ['label' => 'Contact Details', 'component' => 'contact-details', 'enabled' => $tab2Enabled],
    'tab3' => ['label' => 'Bank Account Details', 'component' => 'bank-details', 'enabled' => true],
    'tab4' => ['label' => 'Enclosure List (Self Attested)', 'component' => 'enclosure-list', 'enabled' => false],
    'tab5' => ['label' => 'Self Declaration', 'component' => 'self-declaration', 'enabled' => false],
    ];
    @endphp
    <nav class="space-y-1">
        @foreach ($tabs as $key => $tab)
        @if ($tab['enabled'])
        <x-responsive-nav-link :active="$currentTab === $key" wire:click="$set('currentTab', '{{ $key }}')">
            {{ $tab['label'] }}
        </x-responsive-nav-link>
        @else
        <x-responsive-nav-link :active="$currentTab === $key" class="text-gray-400 cursor-not-allowed" onclick="event.preventDefault();">
            {{ $tab['label'] }}
        </x-responsive-nav-link>
        @endif
        @endforeach
    </nav>
    <div class="mt-4">
        @isset($tabs[$currentTab])
        @livewire($tabs[$currentTab]['component'], [], key($currentTab))
        @endisset
    </div>
    @endif
</div>