@props([
    'label' => '',
    'model',
    'options' => [],
    'required' => false,
    'allowCustom' => false,
])

<div x-data="{
    open: false,
    search: '',
    selected: @entangle($attributes->wire('model')).live || [],
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
        this.isSelected(item) ?
            this.selected = this.selected.filter(v => v !== value) :
            this.selected.push(value);
    },
    get filteredOptions() {
        if (this.search === '') return this.options;
        return this.options.filter(i => String(this.getLabel(i)).toLowerCase().includes(this.search.toLowerCase()));
    },
    addCustom() {
        if (!{{ $allowCustom ? 'true' : 'false' }}) return;
        let val = this.search.trim();
        if (val !== '' && !this.selected.includes(val)) {
            this.selected.push(val);
            if (!this.options.some(i => this.getValue(i) === val)) {
                this.options.push({ value: val, label: val });
            }
        }
        this.search = '';
    }
}" class="relative">

    {{-- Label --}}
    @if ($label)
        <label class="font-semibold mb-1 block text-sm">
            {{ $label }}
            @if ($required)
                <span class="text-red-600">*</span>
            @endif
        </label>
    @endif

    {{-- Selected box --}}
    <div @click="open = !open"
        class="border border-gray-300 hover:border-blue-500 focus:border-cyan-500 focus:ring-cyan-500 outline-none text-gray-900 text-sm rounded-lg block w-full bg-white cursor-pointer dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 dark:hover:border-blue-400 dark:focus:border-green-400 dark:focus:ring-green-400">

        <div class="px-2.5 py-2 max-h-36 overflow-y-auto min-h-[42px] custom-scrollbar">
            <template x-if="!selected || selected.length === 0">
                <span class="text-gray-400">Select {{ $label ? strtolower($label) : 'options' }}</span>
            </template>

            <div class="flex flex-wrap gap-2">
                <template x-for="(value, index) in selected" :key="index">
                    <span
                        class="bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded text-sm flex items-center gap-1 mt-0.5">
                        <span
                            x-text="
                                options.find(o => getValue(o) === value)?.label ?? value
                            "></span>

                        <button type="button" @click.stop="selected.splice(index,1)"
                            class="text-indigo-900 border-l border-indigo-200 pl-1 ml-1 hover:text-red-600 font-bold focus:outline-none">
                            &times;
                        </button>
                    </span>
                </template>
            </div>
        </div>
    </div>

    {{-- Dropdown --}}
    <div x-show="open" @click.outside="open = false" x-transition style="display: none;"
        class="absolute z-50 mt-1 w-full bg-white border rounded-lg shadow-xl max-h-64 overflow-y-auto 
         border-gray-300 outline-none text-gray-900 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">

        <div class="px-2 py-2 sticky top-0 bg-white border-b z-10">
            <input type="text" x-model="search" @keydown.enter.prevent="addCustom"
                class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"
                placeholder="Search...">
        </div>

        <template x-for="item in filteredOptions" :key="getValue(item)">
            <div @click="toggle(item)"
                class="px-3 py-2 cursor-pointer hover:bg-indigo-50 flex justify-between items-center border-b border-gray-50 last:border-0"
                :class="{ 'bg-indigo-50/50': isSelected(item) }">
                <span x-text="getLabel(item)"></span>
                <span x-show="isSelected(item)" class="text-indigo-600 font-bold">✔</span>
            </div>
        </template>

        <div x-show="filteredOptions.length === 0 && search.trim() !== ''"
            class="px-3 py-3 text-sm text-gray-500 text-center bg-gray-50">
            No results found
        </div>
    </div>

    {{-- Validation error --}}
    <x-form.error name="{{ $attributes->get('wire:model') }}" />
</div>
