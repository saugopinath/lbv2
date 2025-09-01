<form wire:submit.prevent="submit">
    @if (session()->has('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
    @endif


    <div class="space-y-2">

        <div class="grid gap-6 mb-4 pl-4 pr-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
            {{-- Scheme Dropdown --}}
            <div>
                <x-form.select name="scheme_id" label="Scheme" wire:model.live="selectedScheme">
                    <option value="">--Select Scheme--</option>
                    @foreach ($Schemes as $scheme)
                    <option value="{{ $scheme->id }}">{{ $scheme->name }}</option>
                    @endforeach
                </x-form.select>
            </div>

            {{-- Menu Dropdown --}}
            @if ($selectedScheme)
            <div>
                <x-form.select name="menu_id" label="Master Failed Type" wire:model.live="selectedMenu">
                    <option value="">--Select Menu--</option>
                    @foreach ($Menus as $menu)
                    <option value="{{ $menu->id }}">{{ $menu->name }}</option>
                    @endforeach
                </x-form.select>
            </div>
            @endif

            {{-- Sub Menu Dropdown --}}
            @if ($selectedMenu)
            <div>
                <x-form.select name="sub_menu_id" label="Failed Type" wire:model.live="selectedSubMenu">
                    <option value="">--Select Sub Menu--</option>
                    @foreach ($SubMenus as $subMenu)
                    <option value="{{ $subMenu->id }}">{{ $subMenu->name }}</option>
                    @endforeach
                </x-form.select>
            </div>
            @endif

            {{-- Parameters For --}}
            @if ($selectedSubMenu && $selectedSubMenu != $Validation_parameters)
            <div class="grid grid-cols-1 md:grid-cols-2 space-y-2">

                <x-form.input
                    name="min_score"
                    label="Min Score"
                    wire:model.live="min_score"
                    placeholder="Enter Min Score" />


                <x-form.input
                    name="max_score"
                    label="Max Score"
                    wire:model.live="max_score"
                    placeholder="Enter Max Score" />
            </div>
            @endif
        </div>

        {{-- Parameters Checkboxes --}}
        @if ($selectedSubMenu)
        <div class="grid gap-6 mb-2 pl-4 pr-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($Parameters as $Parameter)
            <x-form.checkbox
                name="parameters[]"
                id="parameter_{{ $Parameter->id }}"
                wire:model="selectedsetParameter"
                value="{{ $Parameter->id }}"
                label="{{ $Parameter->name }}" />
            @endforeach
        </div>
        @endif


        {{-- Save Button --}}
        
        <div class="flex justify-center">
            <x-button.gradient-button type="submit">Save</x-button.gradient-button>
        </div>
        
    </div>
</form>