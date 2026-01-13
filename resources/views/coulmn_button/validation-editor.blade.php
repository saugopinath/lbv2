<div
    wire:key="validation-editor-{{ $fieldId }}"
    wire:ignore
    x-data="{
        open: false,
        selected: [],
        options: @js($this->validationOptions),

        toggle(item) {
            if (this.selected.includes(item.rule)) {
                this.selected = this.selected.filter(v => v !== item.rule);
            } else {
                this.selected.push(item.rule);
            }
        },

        save() {
            $wire.set('selectedValidations.{{ $fieldId }}', this.selected);
            $wire.call('saveValidation', {{ $fieldId }});
        }
    }"
    x-init="
        selected = JSON.parse(
            JSON.stringify(
                $wire.get('selectedValidations.{{ $fieldId }}') || []
            )
        );
    "
    class="relative w-full">
    {{-- Selected box --}}
    <div
        @click="open = !open"
        class="border rounded px-2 py-1 text-xs cursor-pointer bg-white">
        <template x-if="selected.length === 0">
            <span class="text-gray-400">Select validation</span>
        </template>

        <div class="flex flex-wrap gap-1">
            <template x-for="(value, index) in selected" :key="index">
                <span
                    class="bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded flex items-center gap-1">
                    <span
                        x-text="options.find(o => o.rule === value)?.description ?? value"></span>
                    <button
                        type="button"
                        @click.stop="selected.splice(index,1)"
                        class="text-red-600 font-bold">
                        ×
                    </button>
                </span>
            </template>
        </div>
    </div>

    {{-- Dropdown --}}
    <div
        x-show="open"
        @click.outside="open = false"
        x-transition
        class="absolute z-50 mt-1 w-full bg-white border rounded shadow max-h-48 overflow-y-auto">
        <template x-for="item in options" :key="item.rule">
            <div
                @click="toggle(item)"
                class="px-3 py-2 cursor-pointer hover:bg-indigo-50 flex justify-between">
                <span x-text="item.description"></span>
                <span x-show="selected.includes(item.rule)">✔</span>
            </div>
        </template>
    </div>

    {{-- Save button --}}
    <button
        type="button"
        @click="save()"
        class="mt-1 bg-green-600 hover:bg-green-700 text-white text-xs px-2 py-1 rounded">
        Save
    </button>
</div>
