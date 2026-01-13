@props([
'label' => '',
'model',
'options' => [],
'required' => false,
])

<div
    x-data="{
        open: false,
        selected: @entangle($attributes->wire('model')).live,
        options: Object.entries({{ Js::from($options) }}).map(
            ([value, label]) => ({ value, label })
        ),

        getLabel(item) {
            return typeof item === 'object' ? item.label : item;
        },
        getValue(item) {
            return typeof item === 'object' ? item.value : item;
        },
        isSelected(item) {
            return this.selected.includes(this.getValue(item));
        },
        toggle(item) {
            let value = this.getValue(item);
            this.isSelected(item)
                ? this.selected = this.selected.filter(v => v !== value)
                : this.selected.push(value);
        }
    }"
    class="relative">

    {{-- Label --}}
    <label class="font-semibold mb-1 block">
        {{ $label }}
        @if($required)
        <span class="text-red-600">*</span>
        @endif
    </label>

    {{-- Selected box --}}
    <div
        @click="open = !open"
        class="border border-gray-300 hover:border-blue-500 focus:border-cyan-500 focus:ring-cyan-500 outline-none text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 dark:hover:border-blue-400 dark:focus:border-green-400 dark:focus:ring-green-400">
        <template x-if="selected.length === 0">
            <span class="text-gray-400">Select {{ strtolower($label) }}</span>
        </template>

        <div class="flex flex-wrap gap-2">
            <template x-for="(value, index) in selected" :key="index">
                <span
                    class="bg-indigo-100 text-indigo-700 px-2 py-1 rounded text-sm flex items-center gap-1">
                    <span
                        x-text="
                            options.find(o => getValue(o) === value)?.label ?? value
                        "></span>

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
        class="absolute z-50 mt-1 w-full bg-white border rounded shadow max-h-48 overflow-y-auto 
         border-gray-300 hover:border-blue-500 focus:border-cyan-500 focus:ring-cyan-500 outline-none text-gray-900 text-sm  dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 dark:hover:border-blue-400 dark:focus:border-green-400 dark:focus:ring-green-400">
        <template x-for="item in options" :key="getValue(item)">
            <div
                @click="toggle(item)"
                class="px-3 py-2 cursor-pointer hover:bg-indigo-50 flex justify-between">
                <span x-text="getLabel(item)"></span>
                <span x-show="isSelected(item)">✔</span>
            </div>
        </template>
    </div>

    {{-- Validation error --}}
    <x-form.error name="{{ $attributes->get('wire:model') }}" />
</div>