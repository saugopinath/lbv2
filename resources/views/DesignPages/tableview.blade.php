<x-layouts.app>
  <div class="bg-white dark:bg-gray-800 shadow-md rounded p-4 space-y-4">
    <!-- Top Bar -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
      <!-- <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3"> -->
      <div class="flex items-center gap-2 pl-4">
         <div class="flex items-center gap-2 w-full md:w-auto justify-start md:justify-start pl-0 md:pl-4"></div>
        <button
          class="flex items-center justify-center gap-2 px-1.5 py-1 bg-red-500 text-white text-sm font-medium rounded-md hover:bg-red-700 transition">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" class="w-5 h-5" fill="currentColor">
            <path
              d="M128 64C92.7 64 64 92.7 64 128L64 512C64 547.3 92.7 576 128 576L208 576L208 464C208 428.7 236.7 400 272 400L448 400L448 234.5C448 217.5 441.3 201.2 429.3 189.2L322.7 82.7C310.7 70.7 294.5 64 277.5 64L128 64zM389.5 240L296 240C282.7 240 272 229.3 272 216L272 122.5L389.5 240zM272 444C261 444 252 453 252 464L252 592C252 603 261 612 272 612C283 612 292 603 292 592L292 564L304 564C337.1 564 364 537.1 364 504C364 470.9 337.1 444 304 444L272 444zM304 524L292 524L292 484L304 484C315 484 324 493 324 504C324 515 315 524 304 524zM400 444C389 444 380 453 380 464L380 592C380 603 389 612 400 612L432 612C460.7 612 484 588.7 484 560L484 496C484 467.3 460.7 444 432 444L400 444zM420 572L420 484L432 484C438.6 484 444 489.4 444 496L444 560C444 566.6 438.6 572 432 572L420 572zM508 464L508 592C508 603 517 612 528 612C539 612 548 603 548 592L548 548L576 548C587 548 596 539 596 528C596 517 587 508 576 508L548 508L548 484L576 484C587 484 596 475 596 464C596 453 587 444 576 444L528 444C517 444 508 453 508 464z" />
          </svg>
        </button>

        <button
          class="flex items-center gap-2 px-1.5 py-1 bg-green-500 text-white text-sm font-medium rounded-md hover:bg-green-700 transition">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" class="w-5 h-5" fill="currentColor">
            <path d="M128 128C128 92.7 156.7 64 192 64L341.5 64C358.5 64 374.8 70.7 386.8 82.7L493.3 189.3C505.3 201.3 512 217.6 512 234.6L512 512C512 547.3 483.3 576 448 576L192 576C156.7 576 128 547.3 128 512L128 128zM336 122.5L336 216C336 229.3 346.7 240 360 240L453.5 240L336 122.5zM292 330.7C284.6 319.7 269.7 316.7 258.7 324C247.7 331.3 244.7 346.3 252 357.3L291.2 416L252 474.7C244.6 485.7 247.6 500.6 258.7 508C269.8 515.4 284.6 512.4 292 501.3L320 459.3L348 501.3C355.4 512.3 370.3 515.3 381.3 508C392.3 500.7 395.3 485.7 388 474.7L348.8 416L388 357.3C395.4 346.3 392.4 331.4 381.3 324C370.2 316.6 355.4 319.6 348 330.7L320 372.7L292 330.7z" />
          </svg>
        </button>
      </div>
      <div class="flex justify-center">
        <select
          class="w-48 px-3 py-2 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
          <option>Show 10 entries</option>
          <option>Show 25 entries</option>
          <option>Show 50 entries</option>
        </select>
      </div>
      <div class="flex justify-end w-full md:w-auto">
        <div class="relative w-full md:w-64">
          <!-- Search Icon -->
          <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-500" viewBox="0 0 640 640" fill="currentColor">
              <path d="M310.6 194.3L243.4 222.5L243.4 107.2L188.7 297.5L243.4 273.3L243.4 403.6L310.6 194.3zM227.4 97.6L226.1 102.3L210.9 155.2C170.6 170.7 142 209.8 142 255.5C142 307.8 176.3 351.4 225.4 361L225.4 414.6C147.5 404.1 90 336.4 90 255.6C90 175.1 149.8 108.4 227.4 97.6zM538.8 544.8C527.6 556 515.7 557.1 510.2 555.3C504.8 553.5 483.1 535.4 449.8 510.9C416.5 486.3 416.2 475.2 406.8 454.2C397.4 433.3 376.4 411.6 349.3 401.8L339.6 387.1C314.9 404 286.6 414 258.3 415.8L260.4 409.2L276.3 359.7C322.8 347.8 357.2 305.7 357.2 255.5C357.2 201 318.8 153.4 261.2 148.4L261.2 96.3C344.4 101.4 410 170.8 410 255.6C410 289.2 398.8 320.3 381 346L395.6 355.6C405.4 382.7 427.1 403.6 448 413C468.9 422.4 480.2 422.7 504.8 456C529.4 489.2 547.5 510.9 549.3 516.3C551.1 521.7 550 533.6 538.8 544.8zM528.9 526.9C528.9 522.5 525.3 518.9 520.9 518.9C516.5 518.9 512.9 522.5 512.9 526.9C512.9 531.3 516.5 534.9 520.9 534.9C525.3 534.9 528.9 531.3 528.9 526.9z" />
            </svg>
          </div>

          <!-- Input Field -->
          <input
            type="text"
            placeholder="Search..."
            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" />
        </div>
      </div>

    </div>

    <!-- Table -->
    <div class="overflow-x-auto border rounded-lg shadow-sm">
      <table class="min-w-full text-sm text-gray-700 text-center">
        <thead class="bg-violet-800 text-xs uppercase py-3 text-white">
          <tr>
            <th class="py-3">ID</th>
            <th class="py-3">Name</th>
            <th class="py-3">Email</th>
            <th class="py-3">Role</th>
            <th class="py-3">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 bg-white y-overflow-y-auto">

          <tr class="hover:bg-gray-50">
            <td class="py-3">1</td>
            <td class="py-3 font-medium text-gray-900">Alice Johnson</td>
            <td class="py-3">alice@example.com</td>
            <td class="py-3 text-indigo-600">Admin</td>
            <td class="py-3 space-x-4">


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
            </td>
          </tr>


          <tr class="hover:bg-gray-50">
            <td class="py-3">2</td>
            <td class="py-3 font-medium text-gray-900">Bob Smith</td>
            <td class="py-3">bob@example.com</td>
            <td class="py-3 text-green-600">User</td>
            <td class="py-3 space-x-4">

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

            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="flex flex-col md:flex-row items-center justify-between mt-4 space-y-2 md:space-y-0">
      <div class="text-sm text-gray-600">Showing 1 to 10 of 50 entries</div>
      <div class="flex items-center space-x-1">
        <button
          class="flex items-center justify-center px-2.5 py-1.5 bg-gray-100 text-gray-600 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300 transition">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" class="w-4 h-4" fill="currentColor">
            <path d="M105.4 297.4C92.9 309.9 92.9 330.2 105.4 342.7L265.4 502.7C277.9 515.2 298.2 515.2 310.7 502.7C323.2 490.2 323.2 469.9 310.7 457.4L173.3 320L310.6 182.6C323.1 170.1 323.1 149.8 310.6 137.3C298.1 124.8 277.8 124.8 265.3 137.3L105.3 297.3zM457.4 137.4L297.4 297.4C284.9 309.9 284.9 330.2 297.4 342.7L457.4 502.7C469.9 515.2 490.2 515.2 502.7 502.7C515.2 490.2 515.2 469.9 502.7 457.4L365.3 320L502.6 182.6C515.1 170.1 515.1 149.8 502.6 137.3C490.1 124.8 469.8 124.8 457.3 137.3z"/>
          </svg>
        </button>
        <button class="px-3 py-1 text-sm text-white bg-blue-600 rounded">1</button>
        <button class="px-3 py-1 text-sm text-gray-700 bg-gray-100 rounded hover:bg-gray-200">2</button>
        <button class="px-3 py-1 text-sm text-gray-700 bg-gray-100 rounded hover:bg-gray-200">3</button>
        <span class="px-2 text-sm text-gray-500">…</span>
        <button class="px-3 py-1 text-sm text-gray-700 bg-gray-100 rounded hover:bg-gray-200">5</button>
        <button
          class="flex items-center justify-center px-2.5 py-1.5 bg-gray-100 text-gray-600 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300 transition">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" class="w-4 h-4" fill="currentColor">
            <path
              d="M535.1 342.6C547.6 330.1 547.6 309.8 535.1 297.3L375.1 137.3C362.6 124.8 342.3 124.8 329.8 137.3C317.3 149.8 317.3 170.1 329.8 182.6L467.2 320L329.9 457.4C317.4 469.9 317.4 490.2 329.9 502.7C342.4 515.2 362.7 515.2 375.2 502.7L535.2 342.7zM183.1 502.6L343.1 342.6C355.6 330.1 355.6 309.8 343.1 297.3L183.1 137.3C170.6 124.8 150.3 124.8 137.8 137.3C125.3 149.8 125.3 170.1 137.8 182.6L275.2 320L137.9 457.4C125.4 469.9 125.4 490.2 137.9 502.7C150.4 515.2 170.7 515.2 183.2 502.7z" />
          </svg>
        </button>

      </div>
    </div>
  </div>

</x-layouts.app>