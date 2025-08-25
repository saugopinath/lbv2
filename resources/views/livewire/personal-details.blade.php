<div x-data="{ appType: '{{ $app_type }}',caste: '{{ $caste }}',MarStatu: '{{ $mar_statu }}'}">
    <form wire:submit.prevent="save">
        @if($this->hideAppTypeSection)
        <div class="grid gap-6 md:grid-cols-2 mb-2 pl-4 pr-4">
            <div>
                <x-form.select name="app_type" id="app_type" label="Application Type:" required x-model="appType" wire:model="app_type">
                    <option value="">Select</option>
                    @foreach ($app_types as $app_type)
                    <option value="{{ $app_type->id }}">{{ $app_type->name }}</option>
                    @endforeach
                </x-form.select>
            </div>
            <div>
                <x-form.input type="date" name="app_date" id="app_date"  label="Application Date:" required wire:model="app_date" :max="$cdate" :min="$pdate" />
            </div>
        </div>
        @endif
        <template x-if="appType == {{ \App\Models\Codemaster::getIdByCode(42) }}">
            <div class="grid gap-6 md:grid-cols-2 mb-2 pl-4 pr-4">
                <div>
                    <x-form.input
                        name="reg_no"
                        label="Duare Sakar Registration Number"
                        placeholder="Enter Duare Sakar Registration Number"
                        required wire:model="reg_no" />
                </div>
                <div>
                    <x-form.input type="date" name="ds_date" id="ds_date" label="Duare Sakar Date" required wire:model="ds_date" :max="$cdate" :min="$pdate" />
                </div>
            </div>
        </template>
        <div class="grid gap-6 md:grid-cols-2 mb-2 pl-4 pr-4">
            <div>
                <x-form.input
                    id="name"
                    name="name"
                    label="Applicant Name"
                    placeholder="Enter Applicant Name"
                    required wire:model="name" x-on:input="$el.value = $el.value.replace(/[^A-Za-z\s]/g, '')" />
            </div>
        </div>
        <div class="grid gap-6 md:grid-cols-2 mb-2 pl-4 pr-4">
            <div>
                <x-form.input
                    id="mobile"
                    name="mobile"
                    label="Mobile number"
                    required wire:model="mobile" placeholder="123-45-678" x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0,10)" />
            </div>
            <div>
                <x-form.input
                    id="email"
                    name="email"
                    type="email"
                    label="Email address"
                    wire:model="email" placeholder="example@example.com" />
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
                    label="Age (as on {{ $currentDate }})"
                    wire:model="age" disabled />
                <x-loading-spinner wire:target="dob" />
            </div>
        </div>
        <div class="grid gap-6 md:grid-cols-2 mb-2 pl-4 pr-4">
            <div>
                <x-form.input
                    id="ffname"
                    name="ffname"
                    label="Father's Name"
                    required wire:model="ffname" placeholder="Enter Father's Name" x-on:input="$el.value = $el.value.replace(/[^A-Za-z\s]/g, '')" />
            </div>
            <div>
                <x-form.input
                    id="mfname"
                    name="mfname"
                    label="Mother's Name"
                    wire:model="mfname" required placeholder="Enter Mother's Name" x-on:input="$el.value = $el.value.replace(/[^A-Za-z\s]/g, '')" />
            </div>
        </div>
        <div class="grid gap-6 md:grid-cols-2 mb-2 pl-4 pr-4">
            <div>
                <x-form.select name="mar_statu" id="mar_statu" label="Marital Status" required x-model="MarStatu" wire:model="mar_statu">
                    <option value="">Select</option>
                    @foreach ($mar_status as $mar_statu)
                    <option value="{{ $mar_statu->id }}">{{ $mar_statu->name }}</option>
                    @endforeach
                </x-form.select>
            </div>
            <template x-if="MarStatu && MarStatu != {{ \App\Models\Codemaster::getIdByCode(31) }} && MarStatu != {{ \App\Models\Codemaster::getIdByCode(33) }}">
                <div>
                    <x-form.input
                        id="sfname"
                        name="sfname"
                        label="Spouse's Name"
                        wire:model="sfname" required placeholder="Enter Spouse's Name" x-on:input="$el.value = $el.value.replace(/[^A-Za-z\s]/g, '')" />
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
                        id="cas_cer_no"
                        name="cas_cer_no"
                        label="Caste Certificate Number"
                        wire:model="cas_cer_no" placeholder="Enter Caste Certificate Number" required x-on:input="$el.value = $el.value.replace(/[^A-Za-z0-9\/-]/g, '')" />
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