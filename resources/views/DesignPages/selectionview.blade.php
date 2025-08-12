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
                    <option selected="">--Select Block/Municipality/Crop--</option>
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
                <div class="flex justify-between items-center mt-6">
                    <button class="bg-gray-500 text-white px-4 py-2 rounded">Reset</button>
                    <button class="bg-blue-600 text-white px-4 py-2 rounded">Filter</button>
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