<div class="bg-white shadow rounded-xl p-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-xl font-bold text-indigo-700">Set Dynamic Form Field</h1>

    </div>

    {{-- Flash Message --}}
    @if (session()->has('success'))
    <div class="mb-4 text-green-600 font-semibold">
        {{ session('success') }}
    </div>
    @endif

    <form wire:submit.prevent="save"
        class="grid grid-cols-1 md:grid-cols-2 gap-4">

        {{-- Scheme --}}
        <x-form.select
            name="scheme_id"
            label="Scheme"
            wire:model.live="scheme_id"
            required>
            <option value="">-- Select Scheme --</option>
            @foreach ($schemes as $scheme)
            <option value="{{ $scheme->id }}">
                {{ $scheme->name }}
            </option>
            @endforeach
        </x-form.select>

        {{-- Level Name --}}
        <x-form.input
            name="level_name"
            label="Level Name"
            placeholder="Enter Level Name"
            wire:model="level_name"
            required />

        {{-- Field ID --}}
        <x-form.input
            name="field_id"
            label="Field ID"
            placeholder="Enter Field ID"
            wire:model="field_id"
            required />

        {{-- Field Label --}}
        <x-form.input
            name="field_name"
            label="Field Name"
            placeholder="Enter Field Label"
            wire:model="field_name"
            required />

        {{-- Field Type --}}
        <x-form.select
            name="field_type"
            label="Field Type"
            wire:model.live="field_type"
            required>
            <option value="">-- Select Field Type --</option>
            @foreach ($fieldTypes as $type)
            <option value="{{ $type->name }}">
                {{ $type->name }}
            </option>
            @endforeach
        </x-form.select>

        <x-form.select
            name="view_type"
            label="View Type"
            wire:model="view_type"
            required>
            <option value="">-- Select View Type --</option>
            <option value="1">1</option>
            <option value="2">1/2</option>
            <option value="3">1/3</option>
        </x-form.select>
        <div class="grid grid-cols-2 gap-4 md:col-span-2">
            <x-form.multiselect
                label="Validation Rules"
                wire:model="validation_rule"
                :options="$validationRuleOptions"
                required />

            <div class="">
                <label class="font-semibold block mb-1">
                    Is under any section?
                </label>
                <div class="flex gap-6">
                    <x-form.radio
                        name="is_under_section"
                        value="yes"
                        label="Yes"
                        wire:model.live="is_under_section" />

                    <x-form.radio
                        name="is_under_section"
                        value="no"
                        label="No"
                        wire:model.live="is_under_section" />
                </div>
            </div>
            @if ($is_under_section === 'yes')
            <x-form.select
                name="section_id"
                label="Select Section"
                wire:model.live="section_id"
                required>
                <option value="">-- Select Section --</option>

                @forelse ($sections as $section)
                <option value="{{ $section->id }}">
                    {{ $section->section_name }}
                </option>
                @empty
                <option value="">No sections found</option>
                @endforelse
            </x-form.select>
            @endif
            @if($isdepenentsec)
            <div class="">
                <label class="font-semibold block mb-1">
                    Is depenent?
                </label>
                <div class="flex gap-6">
                    <x-form.radio
                        name="isdependent"
                        value="yes"
                        label="Yes"
                        wire:model.live="isdependent" />

                    <x-form.radio
                        name="isdependent"
                        value="no"
                        label="No"
                        wire:model.live="isdependent" />
                </div>
            </div>
            @endif
            @if ($isdependent === 'yes')
            <x-form.select
                name="depenent_on"
                label="Depenent On"
                wire:model.live="depenent_on"
                required>
                <option value="">-- Select --</option>
                @foreach ($depenentOptions as $option)
                <option value="{{ $option->id }}">
                    {{ $option->level_name }}
                </option>
                @endforeach
            </x-form.select>
            @endif
            @if ($depvalueradio)
            <div>
                <label class="font-semibold block mb-1">
                    Dependent on Values?
                </label>

                <div class="flex gap-6">
                    <x-form.radio
                        name="isdependentvalue"
                        value="yes"
                        label="Yes"
                        wire:model.live="isdependentvalue" />

                    <x-form.radio
                        name="isdependentvalue"
                        value="no"
                        label="No"
                        wire:model.live="isdependentvalue" />
                </div>
            </div>
            @endif


            @if ($isdependentvalue === 'yes' && $depvaluesopt)
            <div wire:key="container-{{ $depenent_on }}">
                <x-form.multiselect
                    label="Dependent on Values"
                    wire:model="depvalues"
                    :options="$depvaluesopt"
                    required />
            </div>
            @endif


            
            <div class="">
                <label class="font-semibold block mb-1">
                    Is choose from default?
                </label>
                <div class="flex gap-6">
                    <x-form.radio
                        name="is_choose_default"
                        value="yes"
                        label="Yes"
                        wire:model.live="is_choose_default" />

                    <x-form.radio
                        name="is_choose_default"
                        value="no"
                        label="No"
                        wire:model.live="is_choose_default" />
                </div>
            </div>
            @if ($is_choose_default === 'yes')
            <x-form.select
                name="default_value"
                label="Default Value"
                wire:model.live="default_value"
                required>
                <option value="">-- Select --</option>
                @foreach ($default_values as $key => $value)
                <option value="{{ $key }}">
                    {{ $key }}
                </option>
                @endforeach
            </x-form.select>
            @endif
            @if ($field_type === 'select')
            <div class="">
                <label class="font-semibold block mb-2">
                    Is Multiple Select Allowed?
                </label>

                <div class="flex gap-6">
                    <x-form.radio
                        name="is_multiple"
                        value="yes"
                        label="Yes"
                        wire:model.live="is_multiple" />

                    <x-form.radio
                        name="is_multiple"
                        value="no"
                        label="No"
                        wire:model.live="is_multiple" />
                </div>
            </div>
            @endif

        </div>

        {{-- Validation Rules (Alpine Multi-select) --}}


        {{-- Is Under Any Section --}}


        @if($isdependent === 'no')
        @if (
        in_array($field_type, ['checkbox','radio']) ||
        ($field_type === 'select' && $is_choose_default === 'no')
        )
        <!-- Options Section -->
        <div class="md:col-span-2 mt-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-form.input
                    name="option_input"
                    label="Options"
                    placeholder="Enter option value"
                    wire:model="option_input" />

                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 items-end ">
                    <x-button.primary
                        type="button"
                        wire:click="addOption"
                        class="w-full py-3">
                        + Add Option
                    </x-button.primary>
                </div>
            </div>

            @if(count($options) > 0)
            <div class="mt-4">
                <div class="space-y-2">
                    @foreach ($options as $index => $opt)
                    <div class="flex justify-between items-center bg-gray-50 p-3 rounded border">
                        <span class="text-gray-700">{{ $opt }}</span>
                        <x-button.danger
                            type="button"
                            wire:click="removeOption({{ $index }})"
                            class="text-sm text-red-600 hover:text-red-800">
                            Remove
                        </x-button.danger>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        @endif
        @endif
        <!-- Save Button (outside conditional block) -->
        <div class="md:col-span-2 mt-6 pt-6 border-t">
            <x-button.loading-button
                action="save"
                text="Save Field"
                class="w-full md:w-auto px-8 py-3 bg-indigo-600 hover:bg-indigo-700" />
        </div>
    </form>
</div>