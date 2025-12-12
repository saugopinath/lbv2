<x-layouts.app>
    <form action="{{ route('mis.report.redirect') }}" method="POST">
        @csrf

        <div class="flex-1 p-2 col-end-5 overflow-auto">
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl p-4">

                <x-form.select name="mis_route" label="MIS Report" required>
                    <option value="">-- Select MIS Report --</option>

                    @foreach ($reportTypes as $type)
                        <option value="{{ $type['route'] }}">
                            {{ $type['name'] }}
                        </option>
                    @endforeach
                </x-form.select>

                <div class="flex justify-end mt-4">
                    <x-button.primary type="submit" class="bg-blue-500 text-white">
                        GO
                    </x-button.primary>
                </div>

            </div>
        </div>
    </form>
</x-layouts.app>
