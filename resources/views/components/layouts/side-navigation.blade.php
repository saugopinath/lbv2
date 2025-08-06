<nav class="sidebar fixed z-50 flex-none w-[250px] border-r-2 border-lightgray/[8%] dark:border-gray/20 transition-all duration-300">
    <div class="bg-white dark:bg-dark h-full">
        <div class="p-3.5">
            <a href="#" class="main-logo w-full">
                <x-logos.lb-logo width="200px" height="50px" />
                <p class="text-center">Lakshmir Bhandar</p>

            </a>
        </div>
        <div class="flex items-center gap-2.5 py-2.5 pe-2.5">
            <div class="h-[2px] bg-lightgray/10 dark:bg-gray/50 block flex-1"></div>
            <button type="button" class="shrink-0 btn-toggle hover:text-primary duration-300" @click="$store.app.toggleSidebar()">
                <svg class="w-3.5" viewBox="0 0 13 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path opacity="0.2" d="M5.46133 6.00002L11.1623 12L12.4613 10.633L8.05922 6.00002L12.4613 1.36702L11.1623 0L5.46133 6.00002Z" fill="currentColor" />
                    <path d="M0 6.00002L5.70101 12L7 10.633L2.59782 6.00002L7 1.36702L5.70101 0L0 6.00002Z" fill="currentColor" />
                </svg>
            </button>
        </div>
        <div class="h-[calc(100vh-93px)] overflow-y-auto overflow-x-hidden space-y-16 px-4 pt-2 pb-4">
            <ul class="relative flex flex-col gap-1 text-sm" x-data="{ activeMenu: 'social' }">

                <li class="menu nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link group items-center justify-between"
                        :class="{ 'active': activeMenu === 'dashboard' }" @click="activeMenu = 'dashboard'">

                        <div class="flex items-center">
                            <svg width="24" height="24" viewBox="0 0 20 20" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.2"
                                    d="M10 2.5C5.85786 2.5 2.5 5.85786 2.5 10C2.5 14.1421 5.85786 17.5 10 17.5C14.1421 17.5 17.5 14.1421 17.5 10C17.5 5.85786 14.1421 2.5 10 2.5ZM10 15C8.34315 15 7 13.6569 7 12C7 10.3431 8.34315 9 10 9C11.6569 9 13 10.3431 13 12C13 13.6569 11.6569 15 10 15Z"
                                    fill="currentColor" />
                                <path
                                    d="M10 -4C4.47715 -4 -1 -0.477149 -1 -6C-1 -11.5228 -4.47715 -16 -10 -16C-15.5228 -16 -20 -11.5228 -20 -6C-20 -0.477149 -15.5228 -4 -10 -4ZM-10 -12C-8.34315 -12 -7 -10.6569 -7 -9C-7 -7.34315 -8.34315 -6 -10 -6C-11.6569 -6 -13 -7.34315 -13 -9C-13 -10.6569 -11.6569 -12 -10 -12Z"
                                    fill="currentColor" />
                            </svg>
                            <span class="pl-1">Dashboard</span>
                        </div>

                </li>


                <li class="menu nav-item">
                    <a href="javaScript:;" class="nav-link group items-center justify-between" :class="{'active' : activeMenu === 'tabels'}" @click="activeMenu === 'tabels' ? activeMenu = null : activeMenu = 'tabels'">
                        <div class="flex items-center">
                            <svg width="24" height="24" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.2" d="M17.5001 2.5C17.9603 2.5 18.3334 2.8731 18.3334 3.33333V16.6667C18.3334 17.1269 17.9603 17.5 17.5001 17.5H2.50008C2.03985 17.5 1.66675 17.1269 1.66675 16.6667V3.33333C1.66675 2.8731 2.03985 2.5 2.50008 2.5H17.5001Z" fill="currentColor" />
                                <path d="M17.5001 2.5C17.9603 2.5 18.3334 2.8731 18.3334 3.33333V16.6667C18.3334 17.1269 17.9603 17.5 17.5001 17.5H2.50008C2.03985 17.5 1.66675 17.1269 1.66675 16.6667V3.33333C1.66675 2.8731 2.03985 2.5 2.50008 2.5H17.5001ZM16.6667 13.3333H3.33341V15.8333H16.6667V13.3333ZM6.66675 4.16667H3.33341V11.6667H6.66675V4.16667ZM11.6667 4.16667H8.33341V11.6667H11.6667V4.16667ZM16.6667 4.16667H13.3334V11.6667H16.6667V4.16667Z" fill="currentColor" />
                            </svg>
                            <span class="pl-1.5">Lakshmir Bhandar</span>
                        </div>
                        <div class="w-4 h-4 flex items-center justify-center dropdown-icon" :class="{'!rotate-180' : activeMenu === 'tabels'}">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-6 h-6">
                                <path d="M11.9997 13.1714L16.9495 8.22168L18.3637 9.63589L11.9997 15.9999L5.63574 9.63589L7.04996 8.22168L11.9997 13.1714Z" fill="currentColor"></path>
                            </svg>
                        </div>
                    </a>
                    <ul x-cloak x-show="activeMenu === 'tabels'" x-collapse class="sub-menu flex flex-col gap-1">
                        <li><a href="{{route('submitted-list')}}">Submitted Application</a></li>

                    </ul>
                </li>


            </ul>

        </div>
    </div>
</nav>
