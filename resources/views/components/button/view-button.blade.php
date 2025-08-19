 <div x-data="{ show: false }" class="relative inline-block">
                <button @mouseenter="show = true" @mouseleave="show = false"
                  class="w-6 h-6 flex items-center justify-center bg-gray-200 rounded-md hover:bg-gray-300 transition">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -50 576 576" class="w-4 h-4 text-blue-600"
                    fill="currentColor">
                    <path d="M572.52 241.4C518.29 135.59 410.93 64 288 64S57.68 135.64 3.48 241.41
                      a32.35 32.35 0 000 29.19C57.71 376.41 165.07 448 288 448s230.32-71.64
                      284.52-177.41a32.35 32.35 0 000-29.19zM288 400a144 144 0 1 1 144-144
                      143.93 143.93 0 0 1-144 144zm0-240a95.31 95.31 0 0 0-25.31 3.79
                      a47.85 47.85 0 0 1-66.9 66.9A95.78 95.78 0 1 0 288 160z" />
                  </svg>
                </button>

                <div x-show="show" x-transition
                  class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1 px-2 py-1 text-xs text-white bg-gray-800 rounded shadow z-10">
                  View
                </div>
              </div>
