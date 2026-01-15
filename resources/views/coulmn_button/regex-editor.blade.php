<div
    wire:key="regex-{{ $fieldId }}"
    x-data="{
        value: @js($regex ?? ''),

        save() {
            $wire.call('saveRegex', {{ $fieldId }}, this.value);
        }
    }"
    class="relative w-full">
    
    <input
        type="text"
        x-model="value"
        placeholder="Add Regex(Optional)"
        class="border border-gray-300 hover:border-blue-500 focus:border-cyan-500 focus:ring-cyan-500 outline-none text-gray-900 text-sm rounded-lg block w-full p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 dark:hover:border-blue-400 dark:focus:border-green-400 dark:focus:ring-green-400">

    <button
        type="button"
        @click="save"
       class="mt-1 bg-green-600 hover:bg-green-700 text-white text-xs px-2 py-1 rounded">
        Save
    </button>
</div>