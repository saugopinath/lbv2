<div>
    @if ($showTabs)
    <nav class="flex flex-wrap border-b border-gray-200 text-xl font-medium text-center mb-6 space-x-2">
        @foreach ($tabs as $key => $tab)
        @if ($tab['enabled'])
        <x-entrytab-nav-link
            wire:key="nav-{{ $application_id }}-{{ $key }}"
            :active="$currentTab === $key"
            :icon="$tab['icon']"
            class="cursor-pointer"
            wire:click="$set('currentTab', '{{ $key }}')">
            {{ $tab['label'] }}
        </x-entrytab-nav-link>
        @else
        <x-entrytab-nav-link
            wire:key="nav-{{ $application_id }}-{{ $key }}"
            :active="$currentTab === $key"
            :icon="$tab['icon']"
            class="text-gray-400 cursor-not-allowed"
            onclick="event.preventDefault();">
            {{ $tab['label'] }}
        </x-entrytab-nav-link>
        @endif
        @endforeach
    </nav>

    <div class="mt-4">
        @if (!empty($tabMessages[$currentTab]))
        <x-tabalert type="success" :tab="$currentTab">
            {{ $tabMessages[$currentTab] }}
        </x-tabalert>
        @endif
        @if (!empty($tabs[$currentTab]))
        @livewire(
        $tabs[$currentTab]['component'],
        $currentTab === 'tab1' && empty($application_id)
        ? ['aadhaarData' => $aadhaarData]
        : ['application_id' => $application_id],
        key('tab-'.$application_id.'-'.$currentTab)
        )
        @endif
    </div>
    @endif
    @if($showModal)
    <livewire:apllicant-modal :application_id="$application_id" key="applicant-modal-{{ $application_id ?? 'default' }}" />
    @endif
</div>