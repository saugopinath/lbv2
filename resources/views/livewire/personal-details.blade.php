<div x-data="{ applicationType: '{{ $application_type }}',caste: '{{ $caste }}',MaritalStatus: '{{ $marital_status }}'}">
    <form  x-on:submit.prevent="
            Livewire.dispatch('showLoader');
            $wire.save();
        ">
        @if($this->hideAppTypeSection)
        <div class="grid gap-6 md:grid-cols-2 mb-2 pl-4 pr-4">
            <div>
                <x-form.select name="application_type" id="application_type" label="Application Type:" required x-model="applicationType" wire:model="application_type">
                    <option value="">Select</option>
                    @foreach ($app_types as $application_type)
                    <option value="{{ $application_type->id }}">{{ $application_type->name }}</option>
                    @endforeach
                </x-form.select>
            </div>
            <div>
                <x-form.input type="date" name="application_date" id="application_date" label="Application Date:" required wire:model="application_date" :max="$currentDate" :min="$previouesDate" />
            </div>
        </div>
        @endif
        <template x-if="applicationType == {{ \App\Models\Codemaster::getIdByCode(42) }}">
            <div class="grid gap-6 md:grid-cols-2 mb-2 pl-4 pr-4">
                <div>
                    <x-form.input
                        name="ds_registration_no"
                        label="Duare Sakar Registration Number"
                        placeholder="Enter Duare Sakar Registration Number"
                        required wire:model="ds_registration_no" />
                </div>
                <div>
                    <x-form.input type="date" name="duaresarkarDate" id="duaresarkarDate" label="Duare Sakar Date" required wire:model="duaresarkarDate" :max="$currentDate" :min="$previouesDate" />
                </div>
            </div>
        </template>
        <div class="grid gap-6 md:grid-cols-2 mb-2 pl-4 pr-4">
            <div>
                <x-form.input
                    id="beneficiary_name"
                    name="beneficiary_name"
                    label="Applicant Name"
                    placeholder="Enter Applicant Name"
                    required wire:model="beneficiary_name" x-on:input="$el.value = $el.value.replace(/[^A-Za-z\s]/g, '')" />
            </div>
        </div>
        <div class="grid gap-6 md:grid-cols-2 mb-2 pl-4 pr-4">
            <div>
                <x-form.input
                    id="mobile"
                    name="mobile"
                    label="Mobile number"
                    required wire:model.defer="mobile"
                    x-on:input="
        $el.value = $el.value.replace(/[^0-9]/g, '').slice(0,10);
        $wire.set('mobile', $el.value);
    " />
            </div>
            <div>
                <x-form.input
                    id="email"
                    name="email"
                    type="email"
                    label="Email address"
                    wire:model="email" />
            </div>
        </div>
        <div class="grid gap-6 md:grid-cols-2 mb-2 pl-4 pr-4">
            <div>
                <x-form.input type="date"
                    id="dob"
                    name="dob"
                    label="Date of Birth"
                    required wire:model.lazy="dob" :min="$minDOB"
                    :max="$maxDOB" />
            </div>
            <div class="relative">
                <x-form.input
                    id="age"
                    name="age"
                    label="Age (as on {{ $currentDateDMY }})"
                    wire:model="age" disabled />
                <x-loading-spinner wire:target="dob" />
            </div>
        </div>
        <div class="grid gap-6 md:grid-cols-2 mb-2 pl-4 pr-4">
            <div>
                <x-form.input
                    id="ben_father_name"
                    name="ben_father_name"
                    label="Father's Name"
                    required wire:model="ben_father_name" placeholder="Enter Father's Name" x-on:input="$el.value = $el.value.replace(/[^A-Za-z\s]/g, '')" />
            </div>
            <div>
                <x-form.input
                    id="ben_mother_name"
                    name="ben_mother_name"
                    label="Mother's Name"
                    wire:model="ben_mother_name" required placeholder="Enter Mother's Name" x-on:input="$el.value = $el.value.replace(/[^A-Za-z\s]/g, '')" />
            </div>
        </div>
        <div class="grid gap-6 md:grid-cols-2 mb-2 pl-4 pr-4">
            <div>
                <x-form.select name="marital_status" id="marital_status" label="Marital Status" required x-model="MaritalStatus" wire:model="marital_status">
                    <option value="">Select</option>
                    @foreach ($marital_statuses as $marital_status)
                    <option value="{{ $marital_status->id }}">{{ $marital_status->name }}</option>
                    @endforeach
                </x-form.select>
            </div>
            <template x-if="MaritalStatus && MaritalStatus != {{ \App\Models\Codemaster::getIdByCode(31) }} && MaritalStatus != {{ \App\Models\Codemaster::getIdByCode(33) }}">
                <div>
                    <x-form.input
                        id="ben_spouse_name"
                        name="ben_spouse_name"
                        label="Spouse's Name"
                        wire:model="ben_spouse_name" required placeholder="Enter Spouse's Name" x-on:input="$el.value = $el.value.replace(/[^A-Za-z\s]/g, '')" />
                </div>
            </template>
        </div>
        <div class="grid gap-6 md:grid-cols-2 mb-2 pl-4 pr-4">
            <div>
                <x-form.select name="caste" id="caste" label="Select Caste" required x-model="caste" wire:model="caste" required>
                    <option value="">Select</option>
                    @foreach ($castes as $caste)
                    <option value="{{ $caste->id }}">{{ $caste->name }}</option>
                    @endforeach
                </x-form.select>
            </div>
            <template x-if="caste && caste != {{ \App\Models\Codemaster::getIdByCode(173) }}">
                <div>
                    <x-form.input
                        id="caste_cer_no"
                        name="caste_cer_no"
                        label="Caste Certificate Number"
                        wire:model="caste_cer_no" placeholder="Enter Caste Certificate Number" required x-on:input="$el.value = $el.value.replace(/[^A-Za-z0-9\/-]/g, '')" />
                </div>
            </template>
        </div>
        <div class="flex justify-end mt-4 pr-4">
            <x-button.primary type="submit" wire:loading.attr="disabled" wire:target="save" class="flex items-center gap-2">
                <span wire:loading.remove wire:target="save">
                    {{ $mode == '0' ? 'Save' : 'Save & Next' }}
                </span>
                <span wire:loading wire:target="save" class="flex items-center">
                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10"
                            stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z">
                        </path>
                    </svg>
                </span>
            </x-button.primary>
        </div>

    </form>
</div>
