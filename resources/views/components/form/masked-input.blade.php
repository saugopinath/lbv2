@props(['name', 'label' => null, 'required' => false])

<x-form.field>
    {{-- Label --}}
    @if ($required)
        <div class="flex items-center gap-1">
            <x-form.label :name="$name" :label="$label" />
            <span class="text-red-700 font-bold">*</span>
        </div>
    @else
        <x-form.label :name="$name" :label="$label" />
    @endif

    <input
        type="text"
        autocomplete="off"
        name="{{ $name }}"
        id="{{ $name }}"
        wire:ignore
        x-data="{
            realValue: @entangle($attributes->wire('model')) ?? '',
            mask() {
                this.$refs.input.value = this.realValue ? '*'.repeat(this.realValue.length) : '';
            },
            showLastDigit(key) {
                let len = this.realValue ? this.realValue.length : 0;
                this.$refs.input.value = '*'.repeat(len - 1) + key;
                setTimeout(() => this.mask(), 500);
            }
        }"
        x-ref="input"
        x-init="mask()"
        placeholder="Enter {{ $label ?? $name }}"
        @keydown="
            const key = $event.key;

            if (!realValue) realValue = '';

            if (key >= '0' && key <= '9') {
                realValue += key;
                showLastDigit(key);
            } else if (key === 'Backspace') {
                realValue = realValue.slice(0, -1);
                mask();
            } else if (['ArrowLeft','ArrowRight','Tab'].includes(key)) {
                return;
            } else {
                $event.preventDefault();
            }

            $wire.set('{{ $attributes->wire('model')->value() }}', realValue);
            $event.preventDefault();
        "
        @paste="
            let pasted = (event.clipboardData || window.clipboardData).getData('text');
            pasted = pasted.replace(/[^0-9]/g,'');
            if (!realValue) realValue = '';
            realValue += pasted;
            mask();
            $wire.set('{{ $attributes->wire('model')->value() }}', realValue);
            $event.preventDefault();
        "
        class="border border-gray-300 hover:border-blue-500 focus:border-cyan-500 focus:ring-cyan-500 outline-none text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 dark:hover:border-blue-400 dark:focus:border-green-400 dark:focus:ring-green-400"
    >

    <x-form.error :name="$name" />
</x-form.field>
