<x-layouts.app>
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-4 space-y-4">
        <div class="flex justify-between items-center text-center">
            <h1 class="text-xl font-bold text-indigo-800 dark:text-white">{{$header}}</h1>
        </div>
    </div>
    <div
        class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-4 space-y-4"
        x-data="{
        district: '{{ old('district_id', $selectedDistrict ?? '') }}',
        ruralUrban: '{{ old('rural_urban', $ruralUrban ?? '') }}',
        isApprover: {{ $isApprover ? 'true' : 'false' }}
    }"
        x-init="$watch('district', value => ruralUrban = '')">
        <form action="{{ route('cmo-mis-report') }}" method="POST">
            @csrf

            @if ($isHod)
            <x-form.select
                name="district_id"
                label="District"
                x-model="district">
                <option value="">--All--</option>
                @foreach ($districts as $district)
                <option value="{{ $district->id }}">
                    {{ $district->name }}
                </option>
                @endforeach
            </x-form.select>
            @endif

            <div x-show="isApprover || district !== ''" x-transition x-cloak>
                <x-form.select
                    name="rural_urban"
                    label="Rural / Urban"
                    x-model="ruralUrban">
                    <option value="">--All--</option>
                    @foreach (Config::get('constants.rural_urban') as $key => $val)
                    <option value="{{ $key }}">
                        {{ $val }}
                    </option>
                    @endforeach
                </x-form.select>
            </div>

            <div class="flex justify-center mt-2">
                <button
                    type="submit"
                    class="px-6 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg">
                    Search
                </button>
            </div>
        </form>
    </div>

    <x-dynamic-table-view
        :header="$header"
        :helper="$helper ?? []"
        :columns="$columns"
        :data="$data" />
</x-layouts.app>