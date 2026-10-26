@aware(['isTailwind', 'isBootstrap'])

<div 
    @class([
        'mb-3 mb-md-0 input-group' => $isBootstrap,
        'flex items-center gap-2' => $isTailwind,
    ])>

    @if ($isTailwind)
        <label for="table-search-input" class="text-xs font-bold text-gray-700 uppercase tracking-wider whitespace-nowrap">
            Search:
        </label>
    @elseif ($isBootstrap)
        <label for="table-search-input" class="form-label mb-0 me-2 fw-bold text-muted text-uppercase small">
            Search:
        </label>
    @endif

    <div 
        @class([
            'rounded-md shadow-sm' => $isTailwind,
            'flex' => ($isTailwind && !$this->hasSearchIcon),
            'relative inline-flex flex-row items-center' => $this->hasSearchIcon,
        ])>

        @if($this->hasSearchIcon)
            <x-livewire-tables::tools.toolbar.items.search.icon :searchIcon="$this->getSearchIcon" :searchIconClasses="$this->getSearchIconClasses" :searchIconOtherAttributes="$this->getSearchIconOtherAttributes"  />
        @endif

        <x-livewire-tables::tools.toolbar.items.search.input id="table-search-input" />

        @if ($this->hasSearch)
            <x-livewire-tables::tools.toolbar.items.search.remove />
        @endif
    </div>
</div>
