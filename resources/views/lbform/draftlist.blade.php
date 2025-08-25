<x-layouts.app>
    <div class="bg-white dark:bg-gray-800 shadow-md rounded p-4 space-y-4">
        <div class="overflow-x-auto border rounded-lg shadow-sm">
            <table class="min-w-full text-sm text-gray-700 text-center">
                <thead class="bg-violet-800 text-xs uppercase py-2 text-white">
                    <tr>
                        <th class="py-3">Application Id</th>
                        <th class="py-3">Name</th>
                        <th class="py-3">Phone No</th>
                        <th class="py-3">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white y-overflow-y-auto">
                    @foreach($lists as $list)
                    <tr class="hover:bg-gray-50">
                        <td class="py-2">{{ $list->application_id }}</td>
                        <td class="py-2">{{ $list->full_name }}</td>
                        <td class="py-2">{{ $list->mobile_no }}</td>
                        <td class="py-3 space-x-4">
                            <div x-data="{ show: false }" class="relative inline-block">
                                <a href="{{route('draftedit', $list->application_id)}}" @mouseenter="show = true" @mouseleave="show = false"
                                    class="w-6 h-6 flex items-center justify-center bg-gray-200 rounded-md hover:bg-gray-300 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 576" class="w-4 h-4 text-blue-600"
                                        fill="currentColor">
                                        <path d="M535.6 85.7C513.7 63.8 478.3 63.8 456.4 85.7L432 110.1L529.9 208L554.3 183.6C576.2 161.7 576.2 126.3 554.3 104.4L535.6 85.7zM236.4 305.7C230.3 311.8 225.6 319.3 222.9 327.6L193.3 416.4C190.4 425 192.7 434.5 199.1 441C205.5 447.5 215 449.7 223.7 446.8L312.5 417.2C320.7 414.5 328.2 409.8 334.4 403.7L496 241.9L398.1 144L236.4 305.7zM160 128C107 128 64 171 64 224L64 480C64 533 107 576 160 576L416 576C469 576 512 533 512 480L512 384C512 366.3 497.7 352 480 352C462.3 352 448 366.3 448 384L448 480C448 497.7 433.7 512 416 512L160 512C142.3 512 128 497.7 128 480L128 224C128 206.3 142.3 192 160 192L256 192C273.7 192 288 177.7 288 160C288 142.3 273.7 128 256 128L160 128z"/>
                                </a>

                                <div x-show="show" x-transition
                                    class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1 px-2 py-1 text-xs text-white bg-gray-800 rounded shadow z-10">
                                    Edit
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div>
            {{ $lists->links() }}
        </div>
    </div>

</x-layouts.app>