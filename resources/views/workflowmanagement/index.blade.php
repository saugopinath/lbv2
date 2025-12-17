<x-layouts.app>
    <form action="{{ route('create-steps') }}" method="POST">
        @csrf
        <div
            x-data="{
        schemeSelected: '',
        noofSteps: '',
        showLabels: false
    }"
            class="bg-white dark:bg-gray-800 shadow-md rounded-2xl p-4">
            <div class="grid gap-6 md:grid-cols-2 mb-2 pl-4 pr-4">
                <div>
                    <x-form.select
                        name="scheme"
                        id="scheme"
                        label="Schemes:"
                        required
                        x-model="schemeSelected"
                        @change="
                    noofSteps = '';
                    showLabels = false
                ">
                        <option value="">Select</option>
                        @foreach ($schemes as $scheme)
                        <option value="{{ $scheme->id }}">
                            {{ $scheme->name }}
                        </option>
                        @endforeach
                    </x-form.select>
                </div>
            </div>
            <div
                x-show="schemeSelected !== ''"
                x-transition
                x-cloak
                class="grid gap-6 md:grid-cols-2 mb-2 pl-4 pr-4">
                <div>
                    <x-form.input
                        name="noofSteps"
                        label="No of Steps"
                        required
                        x-model="noofSteps"
                        x-on:input="
        noofSteps = noofSteps.replace(/[^0-9]/g, '').slice(0,2);
        showLabels = false;
                " />
                </div>
                <div class="mt-6">
                    <x-button.primary
                        type="button"
                        class="flex items-center gap-2"
                        @click="noofSteps ? showLabels = true : alert('Please enter number of steps')">
                        Go
                    </x-button.primary>
                </div>
            </div>
            <div
                x-show="showLabels"
                x-transition
                x-cloak
                class="mt-4">
                <template x-for="(step, index) in Number(noofSteps)" :key="index">
                    <div class="grid gap-6 md:grid-cols-2 mb-2 pl-4 pr-4">
                        <div>
                            <div class="flex items-center gap-1">
                                <label x-text="'Label Name ' + (index + 1)" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"></label>
                                <span class="text-red-700 font-bold">*</span>
                            </div>
                            <input class="border border-gray-300 hover:border-blue-500 focus:border-cyan-500 focus:ring-cyan-500 outline-none text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 dark:hover:border-blue-400 dark:focus:border-green-400 dark:focus:ring-green-400"
                                autocomplete="off" :name="'labelName' + (index + 1)"
                                :id="'labelName' + (index + 1)"
                                :placeholder="'Enter Label Name ' + (index + 1)"
                                required
                                x-on:input="$el.value = $el.value.replace(/[^A-Za-z\s]/g, '')" />
                        </div>
                    </div>
                </template>
                <div class="mt-6 pl-4">
                    <x-button.primary
                        type="submit"
                        class="flex items-center gap-2">
                        Submit
                    </x-button.primary>
                </div>
            </div>
        </div>
    </form>
</x-layouts.app>