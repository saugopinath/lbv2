<div
    class="flex flex-wrap -mb-px text-2xl font-medium text-center justify-center border-b mb-6 space-x-4 text-gray-500 dark:text-gray-400"
    x-data="{ tab: 1, tabs: {{ json_encode($tabs) }} }">
    <template x-for="(tabItem, i) in tabs" :key="i">
        <button
            @click="tab = i + 1"
            :class="tab === (i + 1) 
                ? 'border-blue-600 text-blue-600' 
                : 'border-transparent text-gray-600 hover:text-blue-500'"
            class="py-2 px-4 border-b-2 -mb-px flex items-center space-x-1">
            <svg
                class="w-6 h-6 me-2 text-gray-400 group-hover:text-gray-500 dark:text-gray-500 dark:group-hover:text-gray-300"
                aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg"
                fill="currentColor"
                viewBox="0 0 20 24">
                <path :d="tabItem.icon"></path>
            </svg>
            <span x-text="tabItem.name" class="hidden md:inline"></span>
        </button>
    </template>
</div>