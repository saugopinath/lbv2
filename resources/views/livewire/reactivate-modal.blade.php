<div
    x-data="{ open: false }"
    x-show="open"
    x-cloak
    x-init="$watch('open', v => { if (!v) $wire.resetForm(); })"
    x-on:show-modal.window="open = true"
    x-on:hide-modal.window="open = false"
    @keydown.escape.window="open = false"
    wire:ignore.self
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
>
    <form x-on:submit.prevent="$wire.saveDsMark()">
        <div class="bg-white rounded-xl p-8 w-[90%] md:w-[60%] shadow-2xl max-h-[90vh] overflow-y-auto">

            <!-- HEADER -->
            <div class="flex justify-between items-center mb-6 border-b pb-3">
                <h2 class="text-2xl font-bold text-gray-800">
                    Beneficiary Details
                </h2>

                <button @click="open = false"
                        class="text-gray-500 hover:text-red-500 text-3xl leading-none">
                    &times;
                </button>
            </div>

            <!-- BENEFICIARY INFO SECTION (two columns in rows) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-gray-800 text-sm mb-6">

                <!-- Row 1 -->
                <div>
                    <h1 class="font-semibold">Beneficiary ID</h1>
                    <p class="mt-1 text-gray-600">{{ $beneficiary_id }}</p>
                </div>

                <div>
                    <h1 class="font-semibold">Name as JNMP Portal</h1>
                    <p class="mt-1 text-gray-600">{{ $jnmp_name ?? '—' }}</p>
                </div>

                <!-- Row 2 -->
                <div>
                    <h1 class="font-semibold">Date of Death</h1>
                    <p class="mt-1 text-gray-600">{{ $dob ?? '—' }}</p>
                </div>

                <div>
                    <h1 class="font-semibold">Name</h1>
                    <p class="mt-1 text-gray-600">{{ $name ?? '—' }}</p>
                </div>

                <!-- Row 3 -->
                <div>
                    <h1 class="font-semibold">Gender</h1>
                    <p class="mt-1 text-gray-600">{{ $gender ?? '—' }}</p>
                </div>

                <div>
                    <h1 class="font-semibold">Mobile No.</h1>
                    <p class="mt-1 text-gray-600">{{ $mobile ?? '—' }}</p>
                </div>

                <!-- Row 4 -->
                <div class="md:col-span-2">
                    <h1 class="font-semibold">Father's Name</h1>
                    <p class="mt-1 text-gray-600">{{ $father_name ?? '—' }}</p>
                </div>
            </div>

            <!-- REASON -->
            <div class="mb-4">
                <x-form.select
                    name="revert_reason_cause_id"
                    id="revert_reason_cause_id"
                    label="Reactivation Reason"
                    wire:model.live="revert_reason_cause_id"
                    required
                >
                    <option value="">-- Select Reason --</option>
                    @foreach ($reactive_reason as $reason)
                        <option value="{{ $reason->id }}">{{ $reason->name }}</option>
                    @endforeach
                </x-form.select>
            </div>

            <!-- REMARKS -->
            <div class="mb-4">
                <x-form.textarea
                    id="revert_reason_remarks"
                    name="revert_reason_remarks"
                    label="Remarks"
                    wire:model="revert_reason_remarks"
                    required
                />
            </div>
            
   <livewire:enclosure-list :application_id="$item->application_id" :doc_type_id_array_list="[111]" :is_page="1" enclosureSource="5" />

            <!-- BUTTON CENTERED -->
            <div class="mt-6 flex justify-center">
                <x-button.primary type="submit" class="px-8 py-2 text-lg">
                    Save as Alive
                </x-button.primary>
            </div>

        </div>
    </form>
</div>
