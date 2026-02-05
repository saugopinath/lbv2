<div>
    <label class="block font-semibold text-gray-700">
        Create Self Declaration Field
    </label>
    <form wire:submit.prevent="save"
        class="grid grid-cols-1 md:grid-cols-2 gap-4">
        {{-- Scheme --}}
        <x-form.select name="scheme_id" label="Scheme" wire:model.live="scheme_id" :disabled="$isContextLocked" required>
            <option value="">-- Select Scheme --</option>
            @foreach ($schemes as $scheme)
            <option value="{{ $scheme->id }}">{{ $scheme->name }}</option>
            @endforeach
        </x-form.select>

        {{-- Tab --}}
        <x-form.select name="tab_code" label="Tab" wire:model.live="tab_code" :disabled="$isContextLocked" required>
            <option value="">-- Select Tab --</option>
            @foreach ($tabs as $tab)
            <option value="{{ $tab->tab_code }}">
                {{ $tab->masterTab->tab_name }}
            </option>
            @endforeach
        </x-form.select>

        {{-- Field Type --}}
        <x-form.select name="field_type" label="Field Type" wire:model.live="field_type" required>
            <option value="">-- Select Field Type --</option>
            @foreach ($fieldTypes as $type)
            <option value="{{ $type->name }}">
                {{ $type->name }}
            </option>
            @endforeach
        </x-form.select>

        {{-- Field Name --}}
        <x-form.input name="field_name" label="Field Name"
            wire:model.live="field_name" required />

        {{-- Field ID --}}
        <x-form.input name="field_id" label="Field ID"
            wire:model.live="field_id"
            required readonly/>

        <x-form.textarea name="level_name" label="Level Name"
            wire:model="level_name" required></x-form.textarea>
        {{-- Is under section --}}
        <div class="grid grid-cols-2 gap-4 md:col-span-2">
            <div>

                <label class="font-semibold mb-1 block">Is under any section / level?</label>
                <div class="flex gap-6">
                    <x-form.radio name="is_under_section" value="yes" label="Yes" wire:model.live="is_under_section" />
                    <x-form.radio name="is_under_section" value="no" label="No" wire:model.live="is_under_section" />
                </div>
            </div>
            @if ($show_multiple)
            <div>
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
        {{-- Section / Level selector --}}
        @if($is_under_section == 'yes')
        <x-form.select name="section_level_type" label="Select Type"
            wire:model.live="section_level_type" required>
            <option value="">-- Select --</option>
            <option value="0">Section</option>
            <option value="1">Level</option>
        </x-form.select>
        @endif
        {{-- Section Dropdown --}}
        @if($section_level_type == 0 && $is_under_section == 'yes')
        <x-form.select name="section_id" label="Select Section"
            wire:model.live="section_id" required>
            <option value="">-- Select Section --</option>
            @foreach($sections as $section)
            <option value="{{ $section->id }}">{{ $section->section_level_name }}</option>
            @endforeach
        </x-form.select>
        @endif
        @if($section_level_type == 1 && $is_under_section == 'yes')
        <x-form.select name="section_id" label="Select Level"
            wire:model.live="section_id" required>
            <option value="">-- Select Section --</option>
            @foreach($sections as $section)
            <option value="{{ $section->id }}">{{ $section->section_level_name }}</option>
            @endforeach
        </x-form.select>
        @endif
        @if (in_array($field_type, ['select','radio']))
        <!-- Options Section -->
        <div class="md:col-span-2 mt-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-form.input
                    name="option_input"
                    label="Options"
                    placeholder="Enter option value"
                    wire:model="option_input" />
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-button.primary
                        type="button"
                        wire:click="addOption"
                        class="w-full mt-6">
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
        <x-form.multiselect
            label="Validation Rules"
            wire:model="validation_rule"
            :options="$validationRuleOptions" />

    </form>
</div>