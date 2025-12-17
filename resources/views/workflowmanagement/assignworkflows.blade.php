<x-layouts.app>
    <div
        x-data="{
        schemeSelected: ''
    }"
        class="bg-white dark:bg-gray-800 shadow-md rounded-2xl p-4">
        <div class="grid gap-6 md:grid-cols-2 mb-2 pl-4 pr-4">
            <div>
                <x-form.select
                    name="scheme"
                    id="scheme"
                    label="Schemes:"
                    required
                    x-model="schemeSelected">
                    <option value="">Select</option>
                    @foreach ($schemes as $scheme)
                    <option value="{{ $scheme->id }}">
                        {{ $scheme->name }}
                    </option>
                    @endforeach
                </x-form.select>
            </div>
        </div>
    </div>
</x-layouts.app>