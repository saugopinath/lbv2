<x-layouts.app>
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl p-4 max-w-md mx-auto">

        <form action="{{ route('select-scheme') }}" method="POST">
            @csrf

            <x-form.select name="scheme_id" label="Select Scheme" required>
                <option value="">-- Select --</option>

                @foreach ($scheme_ids as $scheme)
                    <option value="{{ $scheme->id }}">
                        {{ $scheme->name }}
                    </option>
                @endforeach
            </x-form.select>

            <div class="mt-4 text-right">
                <button type="submit"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    Continue
                </button>
            </div>
        </form>

    </div>
</x-layouts.app>
