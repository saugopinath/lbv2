<div class="relative w-72">
    <input {{ $attributes->merge([
        'type' => "text",
        'placeholder' => "Search",
        'class' => "transition-color block w-full max-w-[16rem] rounded-[10px] border-0 bg-gray-200 dark:bg-gray-800 py-2.5 pl-10 pr-4 text-sm text-gray-800 dark:text-gray-300 placeholder-gray-600 outline-none focus-visible:ring-1 focus-visible:ring-teal-400/50 dark:focus-visible:ring-pink-400/50 sm:max-w-none",
        'autocomplete' => "off"
        ]) }}>
    <div class="absolute inset-0 flex items-center pointer-events-none left-3" aria-hidden="true">
        <svg class="w-5 h-5" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" clip-rule="evenodd" d="M3.25 9.167a5.917 5.917 0 1 1 11.833 0 5.917 5.917 0 0 1-11.833 0ZM9.167 1.75a7.417 7.417 0 1 0 4.687 13.165l2.282 2.282a.75.75 0 0 0 1.061-1.06l-2.282-2.283A7.417 7.417 0 0 0 9.167 1.75Z" fill="#475569" opacity=".8"></path>
        </svg>
    </div>
</div>