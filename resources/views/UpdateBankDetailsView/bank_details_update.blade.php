<x-layouts.app>
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-4 mb-4">
        <div class="flex items-center space-x-3">
            <h1 class="text-xl font-bold text-indigo-800 dark:text-white">
                Update Bank Details For Approved Beneficiary
            </h1>
            <span
                class="px-4 py-1.5 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300 shadow-sm">
                Application Id {{ $application_id }}
            </span>

        </div>
    </div>   
    <!-- Accordion Section -->
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-4 mb-6">
        <livewire:application-details.tab-wise-application-view :id="$application_id" :schemeId="$scheme_id"
            :allowedTabCodes="[101]" />
    </div>
    @if ($errors->has('duplicate_check'))
        <div class="mt-2 mb-0 p-3 border border-red-400 bg-red-100 text-red-700 rounded-md shadow-sm">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->get('duplicate_check') as $message)
                    <li>{{ $message }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div x-data="{ openSection: 'update-bank' }"
        class="space-y-4 bg-white dark:bg-gray-800 shadow-md rounded-xl p-4 mb-6">

        <!-- Accordion Header -->
        <div class="rounded overflow-hidden">
            <button @click="openSection = openSection === 'update-bank' ? '' : 'update-bank'"
                class="w-full flex justify-between items-center text-left p-3 bg-gray-200 font-semibold rounded-md">
                <div class="flex items-center space-x-3">
                    <span class="h-6 w-1 bg-blue-500 rounded-full"></span>
                    <span>Update Bank Details</span>
                </div>
                <!-- Plus Icon -->
                <svg x-show="openSection !== 'update-bank'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"
                    class="h-6 w-6 text-gray-600 transition-transform duration-300">
                    <path
                        d="M320 576C461.4 576 576 461.4 576 320C576 178.6 461.4 64 320 64C178.6 64 64 178.6 64 320C64 461.4 178.6 576 320 576zM296 408L296 344L232 344C218.7 344 208 333.3 208 320C208 306.7 218.7 296 232 296L296 296L296 232C296 218.7 306.7 208 320 208C333.3 208 344 218.7 344 232L344 296L408 296C421.3 296 432 306.7 432 320C432 333.3 421.3 344 408 344L344 344L344 408C344 421.3 333.3 432 320 432C306.7 432 296 421.3 296 408z" />
                </svg>

                <!-- Minus Icon -->
                <svg x-show="openSection === 'update-bank'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"
                    class="h-6 w-6 text-gray-600 transition-transform duration-300">
                    <path
                        d="M320 576C461.4 576 576 461.4 576 320C576 178.6 461.4 64 320 64C178.6 64 64 178.6 64 320C64 461.4 178.6 576 320 576zM232 344C218.7 344 208 333.3 208 320C208 306.7 218.7 296 232 296L408 296C421.3 296 432 306.7 432 320C432 333.3 421.3 344 408 344L232 344z" />
                </svg>
            </button>

            <!-- Accordion Content -->
            <div x-show="openSection === 'update-bank'" x-transition.opacity.duration.400ms
                class="transition duration-500 p-6 bg-green-50 shadow border-l-4 border-blue-500 rounded-b-md space-y-4 mt-2">
                <form x-ref="form" method="POST" action="{{ route('update-bank') }}" x-data @submit.prevent="
        if ($refs.revert_reason_remarks.value.trim() === '') {
            alert('Please fill all required fields before submitting.');
            return;
        }
        if (confirm('Are you sure you want to update bank details?')) {
            $refs.form.submit();
        }
    ">
                    @csrf
                    <input type="hidden" name="application_id" value="{{ Crypt::encryptString($application_id) }}">
                    <input type="hidden" name="scheme_id" value="{{ Crypt::encryptString($scheme_id) }}">

                    {{-- Livewire Bank Update --}}
                    <livewire:bank-update.bank-update :application_id="$application_id" :scheme_id="$scheme_id"/>

                    {{-- Remarks --}}
                    <div class="col-span-2 mt-4">
                        <x-form.textarea id="revert_reason_remarks" name="revert_reason_remarks" label="Remarks"
                            required x-ref="revert_reason_remarks" />
                    </div>

                    {{-- Submit --}}
                    <div class="flex justify-center mt-6">
                        <button type="submit"
                            class="px-8 py-2.5 text-white bg-blue-600 hover:bg-blue-700 rounded-lg font-semibold shadow-sm transition duration-200 ease-in-out cursor-pointer">
                            Update
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

</x-layouts.app>