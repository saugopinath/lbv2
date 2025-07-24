@props(['name', 'checked'    =>  false])
<style>
    /* CHECKBOX TOGGLE SWITCH */
    input:checked ~ .dot {
        transform: translateX(100%);
        background-color: #48bb78;
    }

</style>

<div class="flex">

    <label
      for="{{ $name }}"
      class="flex items-center cursor-pointer"
    >
      <!-- toggle -->
      <div class="relative">
        <!-- input -->
        <input id="{{ $name }}" type="checkbox" class="sr-only" {{ $checked ? 'checked' : '' }} {{ $attributes }}/>
        <!-- line -->
        <div class="w-10 h-4 bg-gray-200 rounded-full shadow-inner"></div>
        <!-- dot -->
        <div class="dot absolute w-6 h-6 bg-red-400 rounded-full shadow -left-1 -top-1 transition"></div>
      </div>
      <!-- label -->
    </label>

  </div>
