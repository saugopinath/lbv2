<div
    wire:key="validation-editor-{{ $fieldId }}"
    wire:ignore
    x-data="{
        open: false,
        selected: [],
        locked: [],
        options: @js($this->validationOptions),
        isMandatory: {{ isset($isMandatory) ? (int) $isMandatory : 0 }},

        isRequired(rule) {
            return rule === 'required';
        },

        toggle(item) {
            if (this.locked.includes(item.rule)) {
                return; 
            }

            if (this.selected.includes(item.rule)) {
                this.selected = this.selected.filter(v => v !== item.rule);
            } else {
                this.selected.push(item.rule);
            }
        },

        remove(rule) {
            if (this.locked.includes(rule)) {
                return;
            }
            this.selected = this.selected.filter(v => v !== rule);
        },

        save() {
            $wire.set('selectedValidations.{{ $fieldId }}', this.selected);
            $wire.call('saveValidation', {{ $fieldId }});
        }
    }"
    x-init="
        let existing = JSON.parse(
            JSON.stringify(
                $wire.get('selectedValidations.{{ $fieldId }}') || []
            )
        );

        selected = existing;

        if (isMandatory === 1 && existing.includes('required')) {
            locked = ['required'];   
        } else {
            locked = [];             
        }
    "
    class="relative w-full"
>
    <!-- Selected box -->
    <div
        @click="open = !open"
        class="border rounded px-2 py-1 text-xs cursor-pointer bg-white min-h-[32px]"
    >
        <template x-if="selected.length === 0">
            <span class="text-gray-400">Select validation</span>
        </template>

        <div class="flex flex-wrap gap-1">
            <template x-for="rule in selected" :key="rule">
                <span
                    class="px-2 py-0.5 rounded flex items-center gap-1"
                    :class="locked.includes(rule)
                        ? 'bg-gray-200 text-gray-500 cursor-not-allowed'
                        : 'bg-indigo-100 text-indigo-700'"
                >
                    <span
                        x-text="options.find(o => o.rule === rule)?.description ?? rule">
                    </span>
                    <button
                        x-show="!locked.includes(rule)"
                        type="button"
                        @click.stop="remove(rule)"
                        class="text-red-600 font-bold"
                    >
                        ×
                    </button>
                </span>
            </template>
        </div>
    </div>

    <!-- Dropdown -->
    <div
        x-show="open"
        @click.outside="open = false"
        x-transition
        class="absolute z-50 mt-1 w-full bg-white border rounded shadow max-h-48 overflow-y-auto"
    >
        <template x-for="item in options" :key="item.rule">
            <div
                @click="toggle(item)"
                class="px-3 py-2 flex justify-between"
                :class="locked.includes(item.rule)
                    ? 'bg-gray-100 text-gray-400 cursor-not-allowed'
                    : 'cursor-pointer hover:bg-indigo-50'"
            >
                <span x-text="item.description"></span>
                <span x-show="selected.includes(item.rule)">✔</span>
            </div>
        </template>
    </div>

    <!-- Save -->
    <button
        type="button"
        @click="save()"
        class="mt-1 bg-green-600 hover:bg-green-700 text-white text-xs px-2 py-1 rounded"
    >
        Save
    </button>
</div>