<div>
    <form wire:submit.prevent="submit">
        @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
        @endif

        {{-- Your Page Content --}}
        <div class="space-y-2">
            {{-- Dropdowns --}}
            <div class="grid gap-6 mb-4 pl-4 pr-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">
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
                    <x-form.select name="menu_id" label="Menu" wire:model.live="selectedMenu">
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
                    <x-form.select name="sub_menu_id" label="Sub Menu" wire:model.live="selectedSubMenu">
                        <option value="">--Select Sub Menu--</option>
                        @foreach ($SubMenus as $subMenu)
                        <option value="{{ $subMenu->id }}">{{ $subMenu->name }}</option>
                        @endforeach
                    </x-form.select>
                </div>
                @endif

                {{-- Parameters For --}}
                @if ($Validation_parameters)
                <div>
                    <x-form.select name="parameter_id" label="Parameters For" wire:model.live="selectedParameter">
                        <option value="">--Select Parameter For--</option>
                        <option value="{{ $Validation_parameters->id }}">{{ $Validation_parameters->name }}</option>
                    </x-form.select>
                </div>
                @endif
            </div>

            {{-- Parameters Checkboxes --}}
            @if ($selectedParameter)
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
            @if ($selectedParameter)
            <div class="flex justify-center">
                <x-button.gradient-button type="submit">Save</x-button.gradient-button>
            </div>
            @endif
        </div>
    </form>
</div>