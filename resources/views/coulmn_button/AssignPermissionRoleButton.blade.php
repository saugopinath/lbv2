<button type="button"
    class="btn btn-primary"
    wire:click="openPermissionModal({{ $itemId }})">
    
    Assign Permission and Role
</button>

@if($showPermissionModal)
    <br>

   <button
    type="button"
    class="btn btn-warning"
    wire:click="openEditModal"
>
    Edit
</button>
@endif

@if($showEditModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">

        <!-- Background -->
        <div
    class="fixed inset-0 bg-black/10 backdrop-blur-sm"
    wire:click.self="$set('showEditModal', false)"
></div>

        <!-- Modal -->
{{-- <div class="relative z-10 w-full max-w-5xl rounded-xl bg-white p-6 shadow-2xl"> --}}
<div class="fixed inset-0 z-50 flex items-center justify-center bg-black/10 backdrop-blur-sm p-6">
    
<div class="relative w-full max-w-5xl max-h-[calc(100vh-3rem)] rounded-xl bg-white p-6 shadow-2xl overflow-y-auto mb-8">
    
    <button
    type="button"
    class="absolute right-4 top-4 z-50 text-3xl text-gray-400 hover:text-gray-700"
    wire:click="$set('showEditModal', false)"
    aria-label="Close"
    >
    &times;
    </button>
            <h2 class="mb-5 text-xl font-semibold text-gray-800">
                Edit User
            </h2>
            <hr class="mb-5 border-gray-200">
            <div class="mb-4 text-left">
    <h3 class="text-lg font-medium text-slate-800">
        Basic Information
    </h3>
    <hr class="mb-5 border-gray-200">
</div>
<div class="grid grid-cols-2 gap-4 mb-5">

    <div>
    <label class="block text-sm font-semibold text-gray-700 leading-none">
    FULL NAME <span class="text-red-500">*</span>
</label>

{{-- <span class="block text-xs text-red-500 mt-1">
    This field is required.
</span> --}}
    <input
        type="text"
        wire:model="fullName"
        required
        class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-blue-500"
        placeholder="Enter full name"
    >
    @error('fullName')
    <span class="mt-1 block text-xs text-red-500">
        This field is required.
    </span>
@enderror
</div>

    <div>
       <label class="block text-sm font-semibold text-gray-700 leading-none">
    EMAIL ADDRESS <span class="text-red-500">*</span>
</label>

        <input
    type="email"
    wire:model="email"
    required
    class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-blue-500"
    placeholder="Enter email address"
>

@error('email')
    <span class="mt-1 block text-xs text-red-500">
        This field is required.
    </span>
@enderror
    </div>
    <div class="mt-4">
    <label class="block text-sm font-semibold text-gray-700 leading-none">
        MOBILE NUMBER <span class="text-red-500">*</span>
    </label>

    <input
        type="text"
        wire:model="mobileNumber"
        required
        class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-blue-500"
        placeholder="Enter mobile number"
    >

    @error('mobileNumber')
        <span class="mt-1 block text-xs text-red-500">
            This field is required.
        </span>
    @enderror
</div>
{{-- <div class="mt-4">
    <label class="block mb-2 text-sm font-semibold text-gray-700">
        DESIGNATION
    </label>

    <input
        type="text"
        wire:model="designation"
        class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-blue-500"
        placeholder="Enter designation"
    >
</div> --}}

<div class="mt-4">
    <label class="block text-sm font-semibold text-gray-700 leading-none">
        DESIGNATION <span class="text-red-500">*</span>
    </label>

    <input
    type="text"
    wire:model.defer="designation"
    required
    class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-blue-500"
    placeholder="Enter designation"
>

    @error('designation')
    <span class="mt-1 block text-xs text-red-500">
        This field is required.
    </span>
@enderror
</div>


<div class="mt-3 flex items-center gap-2">
    <input 
        type="checkbox" 
        id="active_account" 
        class="h-4 w-4 accent-orange-600" 
        checked
    >
    <label for="active_account" class="text-sm text-gray-600">
        Active Account
    </label>
</div>

</div>
<div class="mb-4 text-left">
    <div class="flex items-center">
        <h3 class="text-lg font-medium text-slate-800">
            Role & Scheme Assignments
        </h3>

        <button
            type="button"
            class="ml-auto cursor-pointer text-sm font-medium text-orange-600 hover:text-orange-700"
            wire:click="addAssignment"
        >
            + Add Assignment
        </button>
    </div>

    <hr class="w-full border-0 border-t border-gray-300 my-2">
</div>

            <div class="space-y-4">

    <!-- Existing assignment -->
    <div class="rounded-lg border border-gray-200 bg-gray-100 p-4">

        <div class="flex items-center gap-3">

            <!-- Scheme -->
            <div class="flex-1">
                <label class="mb-1 block text-xs font-bold text-gray-600">
                    SCHEME
                </label>

                <select
                   wire:model="modalScheme"
                    class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2"
                >
                    <option value="">Select Scheme</option>

                    @foreach($schemes as $schemeId => $schemeName)
                        <option value="{{ $schemeId }}">
                            {{ $schemeName }}
                        </option>
                    @endforeach
                </select>
               @error('modalScheme')
    <span class="mt-1 block text-xs text-red-500">
        This field is required.
    </span>
@enderror
            </div>

            <!-- Role -->
            <div class="flex-1">
    <label class="mb-1 block text-xs font-bold text-gray-600">
        ROLE
    </label>

    <select
        wire:model="role"
        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2"
    >
        <option value="">Select Role</option>
        <option value="1">HOD</option>
        <option value="2">Approver</option>
        <option value="3">Verifier</option>
        <option value="4">DDO</option>
        <option value="5">Super Admin</option>
        <option value="6">Delegated Approver</option>
        <option value="7">Delegated HOD</option>
    </select>
    @error('role')
    <span class="mt-1 block text-xs text-red-500">
        This field is required.
    </span>
@enderror
</div>
<!-- Office -->
<div class="flex-1">
    <label class="mb-1 block text-xs font-bold text-gray-600">
        OFFICE
    </label>

    <select
        {{-- wire:model="office" --}}
        wire:model="modalOffice"
        wire:change="setOfficeAddress($event.target.options[$event.target.selectedIndex].text)"
        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2"
    >
        <option value="">Select Office Type</option>
        <option value="2">DASPUR-II BLOCK OFFICE</option>
        <option value="4">GHATAL SUB DIVISION OFFICE</option>
    </select>

    @error('modalOffice')
        <span class="mt-1 block text-xs text-red-500">
            This field is required.
        </span>
    @enderror
</div>

           <!-- Office Address -->
<div class="flex-[1.2]">
    <label class="mb-1 block text-xs font-bold text-gray-600">
        OFFICE ADDRESS
    </label>

    <input
    type="text"
    wire:model="officeAddress"
    class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2"
    placeholder="Office Address"
    readonly
>
</div>
            <!-- Delete -->
            <button
                type="button"
                class="flex h-5 w-7 items-center justify-center text-gray-400 hover:text-gray-600"
                title="Remove assignment"
            >
                <svg xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="1.8">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M6 7h12M9 7V5h6v2m-7 0l1 12h6l1-12m-4 4v5m4-5v5"/>
                </svg>
            </button>

        </div>
    </div>


    <!-- ADDITIONAL ASSIGNMENTS APPEAR HERE -->
    @foreach($assignments as $index => $assignment)

        <div
    class="rounded-lg border border-gray-200 bg-gray-100 p-4"
    wire:key="assignment-{{ $assignment['id'] ?? 'new-'.$index }}"
>

            <div class="flex items-center gap-3">

                <!-- Scheme -->
                <div class="flex-1">
                    <label class="mb-1 block text-xs font-bold text-gray-600">
                        SCHEME
                    </label>

                    <select
                        wire:model="assignments.{{ $index }}.scheme"
                        class="w-full min-w-[360px] rounded-lg border border-gray-300 bg-white px-3 py-2"
                    >
                        <option value="">Select Scheme</option>

                        @foreach($schemes as $schemeId => $schemeName)
                            <option value="{{ $schemeId }}">
                                {{ $schemeName }}
                            </option>
                        @endforeach
                    </select>
                   @error("assignments.$index.scheme")
    <span class="mt-1 block text-xs text-red-500">
        This field is required.
    </span>
@enderror
                </div>

                <!-- Role -->
               <div class="flex-1">
    <label class="mb-1 block text-xs font-bold text-gray-600">
        ROLE
    </label>

    <select
        wire:model="assignments.{{ $index }}.role"
        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2"
    >
        <option value="">Select Role</option>
        <option value="1">HOD</option>
        <option value="2">Approver</option>
        <option value="3">Verifier</option>
        <option value="4">DDO</option>
        <option value="5">Super Admin</option>
        <option value="6">Delegated Approver</option>
        <option value="7">Delegated HOD</option>
    </select>
   @error("assignments.$index.role")
    <span class="mt-1 block text-xs text-red-500">
        This field is required.
    </span>
@enderror
</div>
                <!-- Office -->
               <div class="flex-[1.2]">
    <label class="mb-1 block text-xs font-bold text-gray-600">
        OFFICE
    </label>

    <select
        wire:model="assignments.{{ $index }}.office"
        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2"
    >
        <option value="">Select Office Type</option>
        <option value="DASPUR-II BLOCK OFFICE">DASPUR-II BLOCK OFFICE</option>
        <option value="GHATAL SUB DIVISION OFFICE">GHATAL SUB DIVISION OFFICE</option>
    </select>
   @error("assignments.$index.office")
    <span class="mt-1 block text-xs text-red-500">
        This field is required.
    </span>
@enderror
</div>

                <!-- Office Address -->
                <div class="flex-1">
                    <label class="mb-1 block text-xs font-bold text-gray-600">
                        OFFICE ADDRESS
                    </label>

                    <input
                        type="text"
                        wire:model="assignments.{{ $index }}.address"
                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2"
                        placeholder="Office Address"
                    >
                </div>

                <!-- Delete -->
                <button
                    type="button"
                    class="flex h-5 w-7 items-center justify-center text-gray-400 hover:text-gray-600"
                    title="Remove assignment"
                    wire:click="removeAssignment({{ $index }})"
                >
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M6 7h12M9 7V5h6v2m-7 0l1 12h6l1-12m-4 4v5m4-5v5"/>
                    </svg>
                </button>

            </div>
        </div>

    @endforeach

</div>
@if (session()->has('success'))
    <div class="mb-4 rounded-lg bg-green-100 px-4 py-3 text-sm text-green-700">
        {{ session('success') }}
    </div>
@endif

<!-- SAVE IS OUTSIDE THE GREY CONTAINER -->
<div class="mt-4 flex justify-end">
    <button
        type="button"
        class="btn btn-primary"
        wire:click="saveSchemes"
    >
        Save
    </button>
</div>

</div>   <!-- MODAL BACKGROUND/WRAPPER ENDS -->

</div>
@endif