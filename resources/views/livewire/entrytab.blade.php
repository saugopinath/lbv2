<div>
    @if ($showTabs)
        @php
            $tabs = [
                'tab1' => ['label' => 'Personal Details', 'component' => 'personal-details', 'enabled' => $tab1Enabled, 'icon' => 'M10 0a10 10 0 1 0 10 10A10.011 10.011 0 0 0 10 0Zm0 5a3 3 0 1 1 0 6 3 3 0 0 1 0-6Zm0 13a8.949 8.949 0 0 1-4.951-1.488A3.987 3.987 0 0 1 9 13h2a3.987 3.987 0 0 1 3.951 3.512A8.949 8.949 0 0 1 10 18Z'],
                'tab2' => ['label' => 'Contact Details', 'component' => 'contact-details', 'enabled' => $tab2Enabled, 'icon' => 'M14.25 9.75v-4.5m0 4.5h4.5m-4.5 0 6-6m-3 18c-8.284 0-15-6.716-15-15V4.5A2.25 2.25 0 0 1 4.5 2.25h1.372c.516 0 .966.351 1.091.852l1.106 4.423c.11.44-.054.902-.417 1.173l-1.293.97a1.062 1.062 0 0 0-.38 1.21 12.035 12.035 0 0 0 7.143 7.143c.441.162.928-.004 1.21-.38l.97-1.293a1.125 1.125 0 0 1 1.173-.417l4.423 1.106c.5.125.852.575.852 1.091V19.5a2.25 2.25 0 0 1-2.25 2.25h-2.25Z'],
                'tab3' => ['label' => 'Bank Account Details', 'component' => 'bank-details', 'enabled' => $tab3Enabled, 'icon' => 'M2 10L12 3l10 7v2H2v-2zm1 3h2v6H3v-6zm4 0h2v6H7v-6zm4 0h2v6h-2v-6zm4 0h2v6h-2v-6zm4 0h2v6h-2v-6zM2 20h20v1H2v-1z'],
                'tab4' => ['label' => 'Enclosure List (Self Attested)', 'component' => 'enclosure-list', 'enabled' => $tab4Enabled, 'icon' => 'm18.375 12.739-7.693 7.693a4.5 4.5 0 0 1-6.364-6.364l10.94-10.94A3 3 0 1 1 19.5 7.372L8.552 18.32m.009-.01-.01.01m5.699-9.941-7.81 7.81a1.5 1.5 0 0 0 2.112 2.13'],
                'tab5' => ['label' => 'Self Declaration', 'component' => 'self-declaration', 'enabled' => $tab5Enabled, 'icon' => 'M16 1h-3.278A1.992 1.992 0 0 0 11 0H7a1.993 1.993 0 0 0-1.722 1H2a2 2 0 0 0-2 2v15a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2Zm-3 14H5a1 1 0 0 1 0-2h8a1 1 0 0 1 0 2Zm0-4H5a1 1 0 0 1 0-2h8a1 1 0 1 1 0 2Zm0-5H5a1 1 0 0 1 0-2h2V2h4v2h2a1 1 0 1 1 0 2Z'],
            ];
        @endphp

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
            @if (!empty($tabs[$currentTab]))
                @livewire(
                    $tabs[$currentTab]['component'],
                    ['application_id' => $application_id],
                    key('tab-'.$application_id.'-'.$currentTab.'-'.Str::random(5))
                )
            @endif
        </div>
    @endif
</div>
