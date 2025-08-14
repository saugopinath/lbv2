 <div x-data="{ open: false, modalOpen: false, modalSrc: '' }" class="rounded overflow-hidden">
            <!-- Toggle Button -->
            <button @click="open = !open"
                class="w-full flex justify-between items-center text-left p-3 bg-gray-200 font-semibold">
                <div class="flex items-center space-x-3">
                    <span class="h-6 w-1 bg-orange-500 rounded-full"></span>
                    <span>Encloser Details</span>
                </div>
                <!-- Plus/Minus Icons -->
                <svg x-show="!open" x-cloak xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"
                    class="h-6 w-6 text-gray-600 transition-transform duration-300">
                    <path
                        d="M320 576C461.4 576 576 461.4 576 320C576 178.6 461.4 64 320 64C178.6 64 64 178.6 64 320C64 461.4 178.6 576 320 576zM296 408L296 344L232 344C218.7 344 208 333.3 208 320C208 306.7 218.7 296 232 296L296 296L296 232C296 218.7 306.7 208 320 208C333.3 208 344 218.7 344 232L344 296L408 296C421.3 296 432 306.7 432 320C432 333.3 421.3 344 408 344L344 344L344 408C344 421.3 333.3 432 320 432C306.7 432 296 421.3 296 408z" />
                </svg>
                <svg x-show="open" x-cloak xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"
                    class="h-6 w-6 text-gray-600 transition-transform duration-300">
                    <path
                        d="M320 576C461.4 576 576 461.4 576 320C576 178.6 461.4 64 320 64C178.6 64 64 178.6 64 320C64 461.4 178.6 576 320 576zM232 344C218.7 344 208 333.3 208 320C208 306.7 218.7 296 232 296L408 296C421.3 296 432 306.7 432 320C432 333.3 421.3 344 408 344L232 344z" />
                </svg>
            </button>

            <!-- Encloser List -->
            <div x-show="open" x-transition x-cloak class="p-4 bg-green-50 shadow border-l-4 border-orange-500">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <template
                        x-for="doc in [
                    { name: 'Aadhaar Card', file: 'aadhaar.pdf' },
                    { name: 'Pan Card', file: 'pan.pdf' },
                    { name: 'Passport', file: 'passport.pdf' },
                    { name: 'Photo', file: 'photo.jpg' }
                ]" :key="doc.name">
                        <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                            <p class="text-sm font-semibold text-gray-800" x-text="doc.name"></p>
                            <button @click="modalSrc = doc.file; modalOpen = true"
                                class="mt-2 px-3 py-1 bg-blue-500 text-white text-xs rounded hover:bg-blue-600">
                                View
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Modal -->
            <div x-show="modalOpen" x-cloak x-transition.opacity
                class="fixed inset-0 bg-gray-200 bg-opacity-10 flex items-center justify-center z-50 p-4">
                <div class="bg-white rounded-lg shadow-lg w-full max-w-3xl p-4 relative">
                    <button @click="modalOpen = false"
                        class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 text-2xl font-bold">&times;</button>
                    <iframe :src="modalSrc" class="w-full h-[70vh] rounded" frameborder="0"></iframe>
                </div>
            </div>
        </div>
