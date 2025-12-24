<div class="p-4">
    <select wire:model="selectedSchemeId" class="border p-2">
        <option value="">Select Scheme</option>
        @foreach($schemes as $scheme)
            <option value="{{ $scheme->id }}">{{ $scheme->name }}</option>
        @endforeach
    </select>

    <div class="mt-4">
        <h3>Select Tabs:</h3>
        @foreach($allTabs as $tab)
            <label>
                <input type="checkbox" wire:model="selectedTabs" value="{{ $tab->tab_code }}">
                {{ $tab->tab_name }} (Default Position: {{ $this->positions[$tab->tab_code] ?? 'Custom' }})
            </label>
            @if(!in_array($tab->tab_code, ['persona', 'contact', 'bank', 'encloser']))
                <input type="number" wire:model="positions.{{ $tab->tab_code }}" min="5" placeholder="Position">
            @endif
            <br>
        @endforeach
    </div>

    <button wire:click="submit" class="bg-blue-500 text-white p-2 mt-4">Submit</button>

    @if(session('message'))
        <p class="text-green-500">{{ session('message') }}</p>
    @endif
</div>
