<x-layouts.app>
    <div class="bg-white dark:bg-gray-800 shadow-md rounded p-4 space-y-4">
        <div class="grid gap-6 mb-2 md:grid-cols-3 pl-4 pr-4">
            <div>
                <label for="district"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">District</label>
                <select id="district" name="district"
                    class="border border-gray-300 hover:border-blue-500  focus:border-cyan-500 focus:ring-cyan-500 outline-none text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 dark:hover:border-blue-400 dark:focus:border-green-400 dark:focus:ring-green-400">
                    <option selected="">--Select District--</option>
                    <option value="1">Kolkata</option>
                    <option value="2">Howrah</option>
                </select>
            </div>
            <div>
                <label for="rural_urban"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Rural/Urban</label>
                <select id="rural_urban" name="rural_urban"
                    class="border border-gray-300 hover:border-blue-500  focus:border-cyan-500 focus:ring-cyan-500 outline-none text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 dark:hover:border-blue-400 dark:focus:border-green-400 dark:focus:ring-green-400">
                    <option selected="">--Select Rural/Urban--</option>
                    <option value="1">Rural</option>
                    <option value="2">Urban</option>
                </select>
            </div>
            <div>
                <label for="block_municipality_crop"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Block/Municipality/Crop</label>
                <select id="block_municipality_crop" name="block_municipality_crop"
                    class="border border-gray-300 hover:border-blue-500  focus:border-cyan-500 focus:ring-cyan-500 outline-none text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 dark:hover:border-blue-400 dark:focus:border-green-400 dark:focus:ring-green-400">
                    <option selected="">--Select Block/Municipality--</option>
                    <option value="1">Kolkata</option>
                    <option value="2">Howrah</option>
                </select>
            </div>
            <div>
                <label for="gp_ward"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Gp/Ward</label>
                <select id="gp_ward" name="gp_ward"
                    class="border border-gray-300 hover:border-blue-500  focus:border-cyan-500 focus:ring-cyan-500 outline-none text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 dark:hover:border-blue-400 dark:focus:border-green-400 dark:focus:ring-green-400">
                    <option selected="">--Select Gp/Ward--</option>
                    <option value="1">Kolkata</option>
                    <option value="2">Howrah</option>
                </select>
            </div>
            <div>
                <div class="flex items-center mt-6 gap-3">
                    <!-- Search Button -->
                    <button class="flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition-colors duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" viewBox="0 0 640 640" fill="currentColor">
                            <path d="M480 272C480 317.9 465.1 360.3 440 394.7L566.6 521.4C579.1 533.9 579.1 554.2 566.6 566.7C554.1 579.2 533.8 579.2 521.3 566.7L394.7 440C360.3 465.1 317.9 480 272 480C157.1 480 64 386.9 64 272C64 157.1 157.1 64 272 64C386.9 64 480 157.1 480 272zM272 416C351.5 416 416 351.5 416 272C416 192.5 351.5 128 272 128C192.5 128 128 192.5 128 272C128 351.5 192.5 416 272 416z" />
                        </svg>
                        Search
                    </button>

                    <!-- Reset Button -->
                      <button class="flex items-center gap-2 bg-amber-400 text-white px-4 py-2 rounded hover:bg-amber-600 transition-colors duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" viewBox="0 0 640 640" fill="currentColor">
                            <path d="M129.9 292.5C143.2 199.5 223.3 128 320 128C373 128 421 149.5 455.8 184.2C456 184.4 456.2 184.6 456.4 184.8L464 192L416.1 192C398.4 192 384.1 206.3 384.1 224C384.1 241.7 398.4 256 416.1 256L544.1 256C561.8 256 576.1 241.7 576.1 224L576.1 96C576.1 78.3 561.8 64 544.1 64C526.4 64 512.1 78.3 512.1 96L512.1 149.4L500.8 138.7C454.5 92.6 390.5 64 320 64C191 64 84.3 159.4 66.6 283.5C64.1 301 76.2 317.2 93.7 319.7C111.2 322.2 127.4 310 129.9 292.6zM573.4 356.5C575.9 339 563.7 322.8 546.3 320.3C528.9 317.8 512.6 330 510.1 347.4C496.8 440.4 416.7 511.9 320 511.9C267 511.9 219 490.4 184.2 455.7C184 455.5 183.8 455.3 183.6 455.1L176 447.9L223.9 447.9C241.6 447.9 255.9 433.6 255.9 415.9C255.9 398.2 241.6 383.9 223.9 383.9L96 384C87.5 384 79.3 387.4 73.3 393.5C67.3 399.6 63.9 407.7 64 416.3L65 543.3C65.1 561 79.6 575.2 97.3 575C115 574.8 129.2 560.4 129 542.7L128.6 491.2L139.3 501.3C185.6 547.4 249.5 576 320 576C449 576 555.7 480.6 573.4 356.5z"/>
                        </svg>
                        Reset
                    </button>
                </div>

            </div>
        </div>
    </div>
    <!-- <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl p-4"></div>
        <div>
            <label for="entry_type"
                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Select
                Entry Type</label>
            <select id="entry_type"
                class="border border-gray-300 hover:border-blue-500  focus:border-cyan-500 focus:ring-cyan-500 outline-none text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 dark:hover:border-blue-400 dark:focus:border-green-400 dark:focus:ring-green-400">
                <option selected="">--Select Entry Type--</option>
                <option value="1">Normal Entry</option>
                <option value="2">Duare Sarkar Entry</option>
            </select>
        </div>

    </div> -->
</x-layouts.app>