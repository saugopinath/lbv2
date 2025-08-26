 <aside :class="sidebar ? 'w-52' : 'w-16'"
      class="transition-all duration-300 bg-gradient-to-r from-cyan-800 to-cyan-600 dark:bg-gray-800 shadow-lg flex flex-col h-screen"
      x-data="{ activeMenu: null }">
      <!-- Logo -->
      <div class="flex flex-col items-center p-2 border-b border-gray-700 dark:border-gray-700">
        <img src="{{ asset('/images/biswo.png') }}" alt="Lakshmir Bhandar" class="w-8 mb-1" />
        <template x-if="sidebar">
          <div class="text-center font-bold text-sm text-white">Lakshmir Bhandar</div>
        </template>
      </div>

      <!-- Menu -->
      <nav class="flex-1 overflow-y-auto mt-2 space-y-1 text-sm">

        <!-- Menu Item: Dashboard -->
        <div>
          <a href="{{ route('dashboard') }}"
            class="flex items-center w-full px-4 py-2 text-left hover:bg-slate-700 dark:hover:bg-slate-700 text-slate-200 hover:text-white rounded">
            <svg class="w-5 h-5 mr-2 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
              stroke="currentColor">
              <path opacity="0.3"
                d="M10.8939 22H13.1061C16.5526 22 18.2759 22 19.451 20.9882C20.626 19.9764 20.8697 18.2827 21.3572 14.8952L21.6359 12.9579C22.0154 10.3208 22.2051 9.00229 21.6646 7.87495C21.1242 6.7476 19.9738 6.06234 17.6731 4.69182L17.6731 4.69181L16.2882 3.86687C14.199 2.62229 13.1543 2 12 2C10.8457 2 9.80104 2.62229 7.71175 3.86687L6.32691 4.69181L6.32691 4.69181C4.02619 6.06234 2.87583 6.7476 2.33537 7.87495C1.79491 9.00229 1.98463 10.3208 2.36407 12.9579L2.64284 14.8952C3.13025 18.2827 3.37396 19.9764 4.54903 20.9882C5.72409 22 7.44737 22 10.8939 22Z"
                fill="currentColor" fill="currentColor" />
              <path
                d="M9.44666 15.397C9.11389 15.1504 8.64418 15.2202 8.39752 15.5529C8.15086 15.8857 8.22067 16.3554 8.55343 16.6021C9.52585 17.3229 10.7151 17.7496 12 17.7496C13.285 17.7496 14.4742 17.3229 15.4467 16.6021C15.7794 16.3554 15.8492 15.8857 15.6026 15.5529C15.3559 15.2202 14.8862 15.1504 14.5534 15.397C13.8251 15.9369 12.9459 16.2496 12 16.2496C11.0541 16.2496 10.175 15.9369 9.44666 15.397Z"
                fill="currentColor" />
            </svg>
            <span x-show="sidebar" class="mr-2 truncate">Dashboard</span>
          </a>
        </div>

        <div>
          <button @click="activeMenu === 'Application' ? activeMenu = null : activeMenu = 'Application'"
            class="flex items-center w-full px-4 py-2 text-left hover:bg-slate-700 dark:hover:bg-slate-700 text-slate-200 hover:text-white rounded">
            <svg class="w-5 h-5 mr-2 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
              stroke="currentColor">
              <path opacity="0.3"
                d="M10.8939 22H13.1061C16.5526 22 18.2759 22 19.451 20.9882C20.626 19.9764 20.8697 18.2827 21.3572 14.8952L21.6359 12.9579C22.0154 10.3208 22.2051 9.00229 21.6646 7.87495C21.1242 6.7476 19.9738 6.06234 17.6731 4.69182L17.6731 4.69181L16.2882 3.86687C14.199 2.62229 13.1543 2 12 2C10.8457 2 9.80104 2.62229 7.71175 3.86687L6.32691 4.69181L6.32691 4.69181C4.02619 6.06234 2.87583 6.7476 2.33537 7.87495C1.79491 9.00229 1.98463 10.3208 2.36407 12.9579L2.64284 14.8952C3.13025 18.2827 3.37396 19.9764 4.54903 20.9882C5.72409 22 7.44737 22 10.8939 22Z"
                fill="currentColor" fill="currentColor" />
              <path
                d="M9.44666 15.397C9.11389 15.1504 8.64418 15.2202 8.39752 15.5529C8.15086 15.8857 8.22067 16.3554 8.55343 16.6021C9.52585 17.3229 10.7151 17.7496 12 17.7496C13.285 17.7496 14.4742 17.3229 15.4467 16.6021C15.7794 16.3554 15.8492 15.8857 15.6026 15.5529C15.3559 15.2202 14.8862 15.1504 14.5534 15.397C13.8251 15.9369 12.9459 16.2496 12 16.2496C11.0541 16.2496 10.175 15.9369 9.44666 15.397Z"
                fill="currentColor" />
            </svg>
            <span x-show="sidebar" class="mr-2 truncate">Lakshmir Bhandar</span>
            <svg class="mr-2 w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
              viewBox="0 0 10 6">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="m1 1 4 4 4-4" />
            </svg>
          </button>
          <!-- Sub-menu -->

          <div id="list_menu" x-show="activeMenu === 'Application'" x-collapse x-transition class="pl-4">
            <ul>
              <li>
                <a href="{{ route('lbform') }}"
                  class="flex item-center px-2 py-1 text-left text-slate-200 rounder hover:bg-slate-700 hover:text-white">
                  <svg class="w-5 h-5 mr-2 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path opacity="0.3"
                      d="M6.22209 4.60104C6.66665 4.30399 7.13344 4.04635 7.6171 3.82975C8.98898 3.21538 9.67491 2.90819 10.5875 3.4994C11.5 4.0906 11.5 5.0604 11.5 7V8.5C11.5 10.3856 11.5 11.3284 12.0858 11.9142C12.6716 12.5 13.6144 12.5 15.5 12.5H17C18.9396 12.5 19.9094 12.5 20.5006 13.4125C21.0918 14.3251 20.7846 15.011 20.1702 16.3829C19.9536 16.8666 19.696 17.3333 19.399 17.7779C18.3551 19.3402 16.8714 20.5578 15.1355 21.2769C13.3996 21.9959 11.4895 22.184 9.64665 21.8175C7.80383 21.4509 6.11109 20.5461 4.78249 19.2175C3.45389 17.8889 2.5491 16.1962 2.18254 14.3534C1.81598 12.5105 2.00412 10.6004 2.72315 8.8645C3.44218 7.12861 4.65982 5.64491 6.22209 4.60104Z"
                      fill="currentColor"></path>
                    <path
                      d="M21.446 7.06899C20.6342 5.0083 18.9917 3.36577 16.931 2.55397C15.3895 1.94668 14 3.34315 14 5V9C14 9.55229 14.4477 10 15 10H19C20.6569 10 22.0533 8.61054 21.446 7.06899Z"
                      fill="currentColor"></path>
                  </svg><span x-show="sidebar" class="truncate" svg="truncate">Lakshmir Bhandar Form</span></a>
              </li>
              <li>
                <a href="{{ route('draftlist') }}"
                  class="flex item-center px-2 py-1 text-left text-slate-200 rounder hover:bg-slate-700 hover:text-white">
                  <svg class="w-5 h-5 mr-2 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path opacity="0.3"
                      d="M6.22209 4.60104C6.66665 4.30399 7.13344 4.04635 7.6171 3.82975C8.98898 3.21538 9.67491 2.90819 10.5875 3.4994C11.5 4.0906 11.5 5.0604 11.5 7V8.5C11.5 10.3856 11.5 11.3284 12.0858 11.9142C12.6716 12.5 13.6144 12.5 15.5 12.5H17C18.9396 12.5 19.9094 12.5 20.5006 13.4125C21.0918 14.3251 20.7846 15.011 20.1702 16.3829C19.9536 16.8666 19.696 17.3333 19.399 17.7779C18.3551 19.3402 16.8714 20.5578 15.1355 21.2769C13.3996 21.9959 11.4895 22.184 9.64665 21.8175C7.80383 21.4509 6.11109 20.5461 4.78249 19.2175C3.45389 17.8889 2.5491 16.1962 2.18254 14.3534C1.81598 12.5105 2.00412 10.6004 2.72315 8.8645C3.44218 7.12861 4.65982 5.64491 6.22209 4.60104Z"
                      fill="currentColor"></path>
                    <path
                      d="M21.446 7.06899C20.6342 5.0083 18.9917 3.36577 16.931 2.55397C15.3895 1.94668 14 3.34315 14 5V9C14 9.55229 14.4477 10 15 10H19C20.6569 10 22.0533 8.61054 21.446 7.06899Z"
                      fill="currentColor"></path>
                  </svg><span x-show="sidebar" class="truncate" svg="truncate">Draft Applications</span></a>
              </li>
              <li>
                <a href="#"
                  class="flex item-center px-2 py-1 text-left text-slate-200 rounder hover:bg-slate-700 hover:text-white">
                  <svg class="w-5 h-5 mr-2 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path opacity="0.3"
                      d="M6.22209 4.60104C6.66665 4.30399 7.13344 4.04635 7.6171 3.82975C8.98898 3.21538 9.67491 2.90819 10.5875 3.4994C11.5 4.0906 11.5 5.0604 11.5 7V8.5C11.5 10.3856 11.5 11.3284 12.0858 11.9142C12.6716 12.5 13.6144 12.5 15.5 12.5H17C18.9396 12.5 19.9094 12.5 20.5006 13.4125C21.0918 14.3251 20.7846 15.011 20.1702 16.3829C19.9536 16.8666 19.696 17.3333 19.399 17.7779C18.3551 19.3402 16.8714 20.5578 15.1355 21.2769C13.3996 21.9959 11.4895 22.184 9.64665 21.8175C7.80383 21.4509 6.11109 20.5461 4.78249 19.2175C3.45389 17.8889 2.5491 16.1962 2.18254 14.3534C1.81598 12.5105 2.00412 10.6004 2.72315 8.8645C3.44218 7.12861 4.65982 5.64491 6.22209 4.60104Z"
                      fill="currentColor"></path>
                    <path
                      d="M21.446 7.06899C20.6342 5.0083 18.9917 3.36577 16.931 2.55397C15.3895 1.94668 14 3.34315 14 5V9C14 9.55229 14.4477 10 15 10H19C20.6569 10 22.0533 8.61054 21.446 7.06899Z"
                      fill="currentColor"></path>
                  </svg><span x-show="sidebar" class="truncate" svg="truncate">Submitted Application</span></a>
              </li>
            </ul>
          </div>
        </div>
        <div>
          <button @click="activeMenu === 'Report' ? activeMenu = null : activeMenu = 'Report'"
            class="flex items-center w-full px-4 py-2 text-left hover:bg-slate-700 dark:hover:bg-slate-700 text-slate-200 hover:text-white rounded">
            <svg class="w-5 h-5 mr-2 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
              stroke="currentColor">
              <path opacity="0.3"
                d="M10.8939 22H13.1061C16.5526 22 18.2759 22 19.451 20.9882C20.626 19.9764 20.8697 18.2827 21.3572 14.8952L21.6359 12.9579C22.0154 10.3208 22.2051 9.00229 21.6646 7.87495C21.1242 6.7476 19.9738 6.06234 17.6731 4.69182L17.6731 4.69181L16.2882 3.86687C14.199 2.62229 13.1543 2 12 2C10.8457 2 9.80104 2.62229 7.71175 3.86687L6.32691 4.69181L6.32691 4.69181C4.02619 6.06234 2.87583 6.7476 2.33537 7.87495C1.79491 9.00229 1.98463 10.3208 2.36407 12.9579L2.64284 14.8952C3.13025 18.2827 3.37396 19.9764 4.54903 20.9882C5.72409 22 7.44737 22 10.8939 22Z"
                fill="currentColor" fill="currentColor" />
              <path
                d="M9.44666 15.397C9.11389 15.1504 8.64418 15.2202 8.39752 15.5529C8.15086 15.8857 8.22067 16.3554 8.55343 16.6021C9.52585 17.3229 10.7151 17.7496 12 17.7496C13.285 17.7496 14.4742 17.3229 15.4467 16.6021C15.7794 16.3554 15.8492 15.8857 15.6026 15.5529C15.3559 15.2202 14.8862 15.1504 14.5534 15.397C13.8251 15.9369 12.9459 16.2496 12 16.2496C11.0541 16.2496 10.175 15.9369 9.44666 15.397Z"
                fill="currentColor" />
            </svg>
            <span x-show="sidebar" class="mr-2 truncate">Report List</span>
            <svg class="mr-2 w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
              viewBox="0 0 10 6">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="m1 1 4 4 4-4" />
            </svg>
          </button>
          <!-- Sub-menu -->

          <div id="list_menu" x-show="activeMenu === 'Report'" x-collapse x-transition class="pl-4">
            <ul>
              <li>
                <a href="{{ route('approved-lists') }}"
                  class="flex item-center px-2 py-1 text-left text-slate-200 rounder hover:bg-slate-700 hover:text-white">
                  <svg class="w-5 h-5 mr-2 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path opacity="0.3"
                      d="M6.22209 4.60104C6.66665 4.30399 7.13344 4.04635 7.6171 3.82975C8.98898 3.21538 9.67491 2.90819 10.5875 3.4994C11.5 4.0906 11.5 5.0604 11.5 7V8.5C11.5 10.3856 11.5 11.3284 12.0858 11.9142C12.6716 12.5 13.6144 12.5 15.5 12.5H17C18.9396 12.5 19.9094 12.5 20.5006 13.4125C21.0918 14.3251 20.7846 15.011 20.1702 16.3829C19.9536 16.8666 19.696 17.3333 19.399 17.7779C18.3551 19.3402 16.8714 20.5578 15.1355 21.2769C13.3996 21.9959 11.4895 22.184 9.64665 21.8175C7.80383 21.4509 6.11109 20.5461 4.78249 19.2175C3.45389 17.8889 2.5491 16.1962 2.18254 14.3534C1.81598 12.5105 2.00412 10.6004 2.72315 8.8645C3.44218 7.12861 4.65982 5.64491 6.22209 4.60104Z"
                      fill="currentColor"></path>
                    <path
                      d="M21.446 7.06899C20.6342 5.0083 18.9917 3.36577 16.931 2.55397C15.3895 1.94668 14 3.34315 14 5V9C14 9.55229 14.4477 10 15 10H19C20.6569 10 22.0533 8.61054 21.446 7.06899Z"
                      fill="currentColor"></path>
                  </svg><span x-show="sidebar" class="truncate" svg="truncate">Approved List(User Address)</span></a>
              </li>
              <li>
                <a href="{{ route('approved-lists-BA-Wise') }}"
                  class="flex item-center px-2 py-1 text-left text-slate-200 rounder hover:bg-slate-700 hover:text-white">
                  <svg class="w-5 h-5 mr-2 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path opacity="0.3"
                      d="M6.22209 4.60104C6.66665 4.30399 7.13344 4.04635 7.6171 3.82975C8.98898 3.21538 9.67491 2.90819 10.5875 3.4994C11.5 4.0906 11.5 5.0604 11.5 7V8.5C11.5 10.3856 11.5 11.3284 12.0858 11.9142C12.6716 12.5 13.6144 12.5 15.5 12.5H17C18.9396 12.5 19.9094 12.5 20.5006 13.4125C21.0918 14.3251 20.7846 15.011 20.1702 16.3829C19.9536 16.8666 19.696 17.3333 19.399 17.7779C18.3551 19.3402 16.8714 20.5578 15.1355 21.2769C13.3996 21.9959 11.4895 22.184 9.64665 21.8175C7.80383 21.4509 6.11109 20.5461 4.78249 19.2175C3.45389 17.8889 2.5491 16.1962 2.18254 14.3534C1.81598 12.5105 2.00412 10.6004 2.72315 8.8645C3.44218 7.12861 4.65982 5.64491 6.22209 4.60104Z"
                      fill="currentColor"></path>
                    <path
                      d="M21.446 7.06899C20.6342 5.0083 18.9917 3.36577 16.931 2.55397C15.3895 1.94668 14 3.34315 14 5V9C14 9.55229 14.4477 10 15 10H19C20.6569 10 22.0533 8.61054 21.446 7.06899Z"
                      fill="currentColor"></path>
                  </svg><span x-show="sidebar" class="truncate" svg="truncate">Approved List(Beneficiary Address)</span></a>
              </li>
              
            </ul>
          </div>
        </div>

        <!-- Menu Item: Reports -->
        <!-- <div>
      <button 
        @click="activeMenu === 'reports' ? activeMenu = null : activeMenu = 'reports'" 
      class="flex items-center w-full px-4 py-2 text-left hover:bg-slate-700 dark:hover:bg-slate-700 text-slate-200 hover:text-white rounded">
        <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-6a2 2 0 012-2h2a2 2 0 012 2v6M7 21h10a2 2 0 002-2v-5a2 2 0 00-2-2h-4l-2 2H9a2 2 0 00-2 2v3a2 2 0 002 2z"/>
        </svg>
        <span x-show="sidebar" class="truncate">Reports</span>
      </button>
      <div x-show="activeMenu === 'reports'" x-collapse x-transition class="pl-10">
        <a href="#" class="block px-2 py-1 rounded hover:bg-gray-100 dark:hover:bg-gray-700">Submenu A</a>
        <a href="#" class="block px-2 py-1 rounded hover:bg-gray-100 dark:hover:bg-gray-700">Submenu B</a>
        <a href="#" class="block px-2 py-1 rounded hover:bg-gray-100 dark:hover:bg-gray-700">Submenu C</a>
      </div>
    </div> -->

        <!-- Menu Item: Settings -->
        <!-- <div>
      <button 
        @click="activeMenu === 'settings' ? activeMenu = null : activeMenu = 'settings'" 
      class="flex items-center w-full px-4 py-2 text-left hover:bg-slate-700 dark:hover:bg-slate-700 text-slate-200 hover:text-white rounded">
        <svg class="w-5 h-5 mr-2 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path d="M12 8c-2.21 0-4 .89-4 2s1.79 2 4 2 4-.89 4-2-1.79-2-4-2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M2 12c0 2.21 3.58 4 8 4s8-1.79 8-4M2 12c0-2.21 3.58-4 8-4s8 1.79 8 4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <span x-show="sidebar" class="truncate">Settings</span>
      </button>
      <div x-show="activeMenu === 'settings'" x-collapse x-transition class="pl-10">
        <a href="#" class="block px-2 py-1 rounded hover:bg-gray-100 dark:hover:bg-gray-700">Profile</a>
        <a href="#" class="block px-2 py-1 rounded hover:bg-gray-100 dark:hover:bg-gray-700">Preferences</a>
        <a href="#" class="block px-2 py-1 rounded hover:bg-gray-100 dark:hover:bg-gray-700">Security</a>
      </div>
    </div> -->
      </nav>
    </aside>