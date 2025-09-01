<div class="p-6">
    <!-- Dropdown -->
    <div class="mb-4">
        <x-form.select name="searchType" label="Search By" wire:model.live="searchType" required>
            <option value="">-- Select Type --</option>
            <option value="application_id">Application ID</option>
            <option value="beneficiary_id">Beneficiary ID</option>
        </x-form.select>
    </div>

    <!-- Dynamic Input Field -->
    @if ($searchType === 'application_id')
        <div class="mb-4">
            <x-form.input id="application_id" name="application_id" label="Application ID"
                placeholder="Enter Application ID" required wire:model="searchValue"
                x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0,6)" />
        </div>
    @elseif ($searchType === 'beneficiary_id')
        <div class="mb-4">
            <x-form.input id="beneficiary_id" name="beneficiary_id" label="Beneficiary ID"
                placeholder="Enter Beneficiary ID" required wire:model="searchValue"
                x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0,6)" />
        </div>
    @endif

    <!-- Search Button -->

    <x-button.primary wire:click="search" class="bg-blue-500 text-white whitespace-nowrap cursor-pointer">
        Search
    </x-button.primary>

    <!-- Success Message -->
    @if (session()->has('success'))
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded mt-4">
            {{ session('success') }}
        </div>
    @endif

    <!-- Results -->
    @if ($results && count($results) > 0)
        <div class="mt-6">
            <table class="min-w-full border">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="border px-4 py-2">Application ID</th>
                        <th class="border px-4 py-2">Incomplete Type</th>
                        <th class="border px-4 py-2">Update</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($results as $item)
                        <tr>
                            <td class="border px-4 py-2">{{ $item->application_id }}</td>
                            <td class="border px-4 py-2">{{ $item->incompletType?->name ?? 'N/A' }}</td>
                            <td class="border px-4 py-2">
                                <button wire:click="openModal({{ $item->id }})"
                                    class="bg-green-500 text-white px-3 py-1 rounded">
                                    Update
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <!-- Modal -->
    @if ($showModal && $selectedRecord)
        <div class="fixed inset-0 flex items-center justify-center text-gray-500 bg-opacity-50 z-50">
            <div class="bg-white p-8 rounded-2xl shadow-2xl w-2/3 max-w-3xl">
                <h2 class="text-xl font-bold mb-6 text-gray-800">Update Incomplete Type</h2>

                <!-- Old Value -->
                <div class="mb-6">
                    <x-form.input id="old_value" name="old_value" label="Old Value" :value="$selectedRecord->old_value ?? 'N/A'" disabled
                        class="bg-gray-100 text-gray-600" />
                </div>

                <!-- New Value -->
                <div class="mb-6">
                    <x-form.input id="new_value" name="new_value" label="New Value" placeholder="Enter New Value"
                        required wire:model="newValue" x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '')" />
                </div>

                <!-- Buttons -->
                <div class="flex justify-end space-x-3">
                    <button wire:click="closeModal"
                        class="bg-red-500 hover:bg-red-600 text-white px-5 py-2 rounded-lg shadow">
                        Cancel
                    </button>
                    <button wire:click="saveUpdate"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg shadow">
                        Save
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
