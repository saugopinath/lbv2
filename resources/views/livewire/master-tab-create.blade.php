<div class="bg-white p-6 rounded-xl shadow">

    <div class="space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Database Fields</h1>
                <p class="text-sm text-gray-600 mt-1">Manage database columns and form fields for {{ $model_name ?? 'your model' }}</p>
            </div>
            <div class="flex items-center space-x-3">
                <!-- <button type="button"
                    wire:click="openTabUpdateModal"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Update Tab Details
                </button> -->
                <button type="button"
                    wire:click="openFieldModal"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Create New Tab Details
                </button>
            </div>
        </div>
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Fields</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ count($fields) }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Required Fields</p>
                        <p class="text-2xl font-semibold text-gray-900">
                            {{ collect($fields)->filter(fn($f) => str_contains($f['validation_rule'] ?? '', 'required'))->count() }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-purple-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Unique Fields</p>
                        <p class="text-2xl font-semibold text-gray-900">
                            {{ collect($fields)->where('key_type', 'unique')->count() }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-amber-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Editable</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ count($fields) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Section -->
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center space-x-1">
                                    <span>DB Column</span>
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                    </svg>
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Type</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Field Name</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Field Type</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Validation</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($fields as $i => $field)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                        <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $field['column_name'] }}</div>
                                        @if($field['key_type'] == 'primary')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">PK</span>
                                        @elseif($field['key_type'] == 'unique')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Unique</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $field['column_type'] == 'string' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $field['column_type'] == 'integer' ? 'bg-purple-100 text-purple-800' : '' }}
                                {{ $field['column_type'] == 'text' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $field['column_type'] == 'date' ? 'bg-green-100 text-green-800' : '' }}">
                                    {{ $field['column_type'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $field['field_name'] }}</div>
                                <div class="text-xs text-gray-500">{{ $field['field_id'] ?? '—' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    {{ $field['field_type'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($field['validation_rule'])
                                <div class="flex flex-wrap gap-1">
                                    @foreach(explode('|', $field['validation_rule']) as $rule)
                                    <span class="px-2 py-1 text-xs font-medium bg-orange-50 text-orange-700 rounded">
                                        {{ trim($rule) }}
                                    </span>
                                    @endforeach
                                </div>
                                @else
                                <span class="text-sm text-gray-500">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <button wire:click="editField({{ $i }})"
                                        class="inline-flex items-center px-3 py-1.5 border border-blue-300 rounded-md text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-blue-500 transition-colors">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Edit
                                    </button>
                                    <button wire:click="removeField({{ $i }})"
                                        onclick="return confirm('Are you sure you want to delete this field?')"
                                        class="inline-flex items-center px-3 py-1.5 border border-red-300 rounded-md text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-red-500 transition-colors">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No fields added yet</h3>
                                    <p class="text-gray-500 mb-4">Get started by adding your first database field</p>
                                    <button wire:click="openFieldModal"
                                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        Add First Field
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Table Footer -->
            @if(count($fields) > 0)
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="text-sm text-gray-700 mb-4 sm:mb-0">
                    Showing <span class="font-medium">{{ count($fields) }}</span> field{{ count($fields) !== 1 ? 's' : '' }}
                </div>
                <div class="flex items-center space-x-3">
                    <!-- <button wire:click="exportFields"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                        </svg>
                        Export
                    </button> -->
                    <button wire:click="finalSubmit"
                        class="inline-flex items-center px-6 py-2 bg-green-600 border border-transparent rounded-lg font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Final Submit
                    </button>
                </div>
            </div>
            @endif
        </div>

        <!-- Info Panel -->
        @if(count($fields) > 0)
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">Ready to generate</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <p>Click "Final Submit" to generate:</p>
                        <ul class="list-disc list-inside mt-1 space-y-1">
                            <li>Migration file</li>
                            <li>Model with fillable attributes</li>
                            <li>Form validation rules</li>
                            <li>Database schema</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    @if ($showModal)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <!-- Overlay -->
        <div class="fixed inset-0 bg-black/75 transition-opacity" wire:click="closeModal"></div>

        <!-- Modal Container -->
        <div class="flex min-h-full items-center justify-center p-4">
            <!-- Modal Content -->
            <div class="relative bg-white rounded-xl shadow-xl w-full max-w-6xl max-h-[90vh] flex flex-col overflow-hidden">
                <!-- Modal Header -->
                <div class="px-6 pt-6 pb-4 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900">Add / Edit Field</h2>
                    <p class="text-sm text-gray-600 mt-1">Configure database column and form field properties</p>
                </div>

                <!-- Scrollable Content Area -->
                <div class="flex-1 overflow-y-auto p-6">
                    <!-- Basic Information Section -->
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div>
                                <x-form.input
                                    name="tab_name"
                                    label="Tab Name"
                                    placeholder="Enter Tab Name"
                                    wire:model.live="tab_name"
                                    required />
                            </div>
                            <div>
                                <x-form.input
                                    name="table_name"
                                    label="Table Name"
                                    placeholder="Enter Table Name"
                                    wire:model.live="table_name"
                                    required readonly />
                            </div>
                        </div>
                    </div>


                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Form Field</h3>
                            <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">UI</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                            <x-form.input
                                name="level_name"
                                label="Level Name"
                                placeholder="Enter Level Name"
                                wire:model="level_name"
                                required />

                            <x-form.input
                                name="field_name"
                                label="Field Name"
                                placeholder="e.g., User Name"
                                wire:model.live="field_name"
                                required />

                            <x-form.input
                                name="field_id"
                                label="Field ID"
                                placeholder="e.g., user-name"
                                wire:model="field_id"
                                readonly />

                            <x-form.select
                                name="field_type"
                                label="Field Type"
                                wire:model.live="field_type"
                                required>
                                <option value="">-- Select Field Type --</option>
                                @foreach ($fieldTypes as $type)
                                <option value="{{ $type->name }}">
                                    {{ $type->name }}
                                </option>
                                @endforeach
                            </x-form.select>
                            <x-form.multiselect
                                label="Validation Rules"
                                wire:model="validation_rule"
                                :options="$validationRuleOptions"
                                placeholder="Select validation rules"
                                required />

                            @if($field_type === 'select')
                            <div class="md:col-span-2">
                                <label class="font-semibold block mb-2">
                                    Is Multiple Select Allowed?
                                </label>
                                <div class="flex gap-6">
                                    <x-form.radio
                                        name="is_multiple"
                                        value="yes"
                                        label="Yes"
                                        wire:model.live="is_multiple" />

                                    <x-form.radio
                                        name="is_multiple"
                                        value="no"
                                        label="No"
                                        wire:model.live="is_multiple" />
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- Section and Dependencies -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <!-- Section Selection -->
                            <div>
                                <label class="font-semibold block mb-2">
                                    Is under any section?
                                </label>
                                <div class="flex gap-6 mb-4">
                                    <x-form.radio
                                        name="is_under_section"
                                        value="yes"
                                        label="Yes"
                                        wire:model.live="is_under_section" />

                                    <x-form.radio
                                        name="is_under_section"
                                        value="no"
                                        label="No"
                                        wire:model.live="is_under_section" />
                                </div>

                                @if ($is_under_section === 'yes')
                                <div class="max-w-md">
                                    <x-form.select
                                        name="section_id"
                                        label="Select Section"
                                        wire:model.live="section_id"
                                        required>
                                        <option value="">-- Select Section --</option>

                                        @forelse ($sections as $section)
                                        <option value="{{ $section->id }}">
                                            {{ $section->section_level_name }}
                                        </option>
                                        @empty
                                        <option value="">No sections found</option>
                                        @endforelse
                                    </x-form.select>
                                </div>
                                @endif
                            </div>

                            <!-- Default Value Selection -->
                            <div>
                                <label class="font-semibold block mb-2">
                                    Is choose from default?
                                </label>
                                <div class="flex gap-6 mb-4">
                                    <x-form.radio
                                        name="is_choose_default"
                                        value="yes"
                                        label="Yes"
                                        wire:model.live="is_choose_default" />

                                    <x-form.radio
                                        name="is_choose_default"
                                        value="no"
                                        label="No"
                                        wire:model.live="is_choose_default" />
                                </div>

                                @if ($is_choose_default === 'yes')
                                <div class="max-w-md">
                                    <x-form.select
                                        name="default_value"
                                        label="Default Value"
                                        wire:model.live="default_value">
                                        <option value="">-- Select --</option>
                                        @foreach ($default_values as $key => $value)
                                        <option value="{{ $key }}">
                                            {{ $key }}
                                        </option>
                                        @endforeach
                                    </x-form.select>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Conditional Fields -->
                        @if($isdepenentsec)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4 ">
                            <!-- Confirm Field -->
                            <div>
                                <label class="font-semibold block mb-2">
                                    Is Confirm Field?
                                </label>
                                <div class="flex gap-6 mb-4">
                                    <x-form.radio
                                        name="isconfirm"
                                        value="yes"
                                        label="Yes"
                                        wire:model.live="isconfirm" />

                                    <x-form.radio
                                        name="isconfirm"
                                        value="no"
                                        label="No"
                                        wire:model.live="isconfirm" />
                                </div>

                                @if ($isconfirm === 'yes')
                                <div class="max-w-md">
                                    <x-form.select
                                        name="confirm_of"
                                        label="Confirm Of"
                                        wire:model.live="confirm_of">
                                        <option value="">-- Select --</option>
                                        @foreach ($confirmOptions as $option)
                                        <option value="{{ $option->id }}">
                                            {{ $option->level_name }}
                                        </option>
                                        @endforeach
                                    </x-form.select>
                                </div>
                                @endif
                            </div>

                            @if($isconfirm !== 'yes')
                            <!-- Dependent Field -->
                            <div>
                                <label class="font-semibold block mb-2">
                                    Is Dependent Field?
                                </label>
                                <div class="flex gap-6 mb-4">
                                    <x-form.radio
                                        name="isdependent"
                                        value="yes"
                                        label="Yes"
                                        wire:model.live="isdependent" />

                                    <x-form.radio
                                        name="isdependent"
                                        value="no"
                                        label="No"
                                        wire:model.live="isdependent" />
                                </div>

                                @if ($isdependent === 'yes')
                                <div class="grid grid-cols-1 md:grid-cols-1 gap-4">
                                    <div>
                                        <x-form.select
                                            name="depenent_on"
                                            label="Dependent On"
                                            wire:model.live="depenent_on">
                                            <option value="">-- Select --</option>
                                            @foreach ($depenentOptions as $option)
                                            <option value="{{ $option->id }}">
                                                {{ $option->level_name }}
                                            </option>
                                            @endforeach
                                        </x-form.select>
                                    </div>

                                    @if ($depvalueradio)
                                    <div>
                                        <label class="font-semibold block mb-2">
                                            Dependent on Specific Values?
                                        </label>
                                        <div class="flex gap-6">
                                            <x-form.radio
                                                name="isdependentvalue"
                                                value="yes"
                                                label="Yes"
                                                wire:model.live="isdependentvalue" />

                                            <x-form.radio
                                                name="isdependentvalue"
                                                value="no"
                                                label="No"
                                                wire:model.live="isdependentvalue" />
                                        </div>
                                    </div>
                                    @endif
                                </div>

                                @if ($isdependentvalue === 'yes' && $depvaluesopt)
                                <div class="mt-4 max-w-md" wire:key="container-{{ $depenent_on }}">
                                    <x-form.multiselect
                                        label="Dependent on Values"
                                        wire:model="depvalues"
                                        :options="$depvaluesopt"
                                        placeholder="Select dependent values" />
                                </div>
                                @endif
                                @endif
                            </div>
                            @endif
                        </div>
                        @endif
                    </div>

                    <div class="mb-4 border-t border-gray-200 pt-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
                            <x-form.input
                                name="column_name"
                                label="Column Name"
                                placeholder="e.g., user_name"
                                wire:model.live="column_name"
                                required />

                            <x-form.select
                                name="column_type"
                                label="Data Type"
                                placeholder="-- Select --"
                                wire:model.live="column_type">
                                <option value="">-- Select --</option>
                                <option value="string">String</option>
                                <option value="integer">Integer</option>
                                <option value="text">Text</option>
                                <option value="date">Date</option>
                                <option value="boolean">Boolean</option>
                                <option value="decimal">Decimal</option>
                            </x-form.select>

                            @if(in_array($column_type, ['string', 'integer']))
                            <x-form.input
                                name="length"
                                label="Length"
                                placeholder="Enter the length"
                                wire:model="length" />
                            @endif


                            <x-form.select
                                name="key_type"
                                label="Key Type"
                                placeholder="-- Select --"
                                wire:model.live="key_type">
                                <option value="none">No Key</option>
                                <option value="primary">Primary Key</option>
                                <option value="unique">Unique Key</option>
                                <option value="foreign">Foreign Key</option>
                                <option value="index">Index</option>
                            </x-form.select>
                            @if(in_array($key_type, ['foreign', 'index']))
                            <x-form.input
                                name="key_name"
                                label="Key Name"
                                placeholder="e.g., idx_user_name"
                                wire:model="key_name" />
                            @endif
                            @if($key_type === 'foreign')
                            <x-form.select wire:model.live="fk_table" label="Reference Table">
                                <option value="">-- Select Table --</option>
                                @foreach($fkTables as $t)
                                <option value="{{ $t->table_name }}">
                                    {{ $t->table_name }}
                                </option>
                                @endforeach
                            </x-form.select>
                            @endif
                            @if($key_type === 'foreign')
                            <x-form.select wire:model.live="fk_column" label="Reference Column">
                                <option value="">-- Select Column --</option>
                                @foreach($fkColumns as $c)
                                <option value="{{ $c->column_name }}">
                                    {{ $c->column_name }}
                                </option>
                                @endforeach
                            </x-form.select>
                            @endif

                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="font-semibold block mb-2">
                                    Nullable?
                                </label>
                                <div class="flex gap-6 mb-4">
                                    <x-form.radio
                                        name="nullable"
                                        value="yes"
                                        label="Yes"
                                        wire:model="nullable" />

                                    <x-form.radio
                                        name="nullable"
                                        value="no"
                                        label="No"
                                        wire:model="nullable" />
                                </div>
                            </div>
                            <div>
                                <label class="font-semibold block mb-2">
                                    Has Default Value?
                                </label>
                                <div class="flex gap-6 mb-4">
                                    <x-form.radio
                                        name="default_enabled"
                                        value="yes"
                                        label="Yes"
                                        wire:model.live="default_enabled" />

                                    <x-form.radio
                                        name="default_enabled"
                                        value="no"
                                        label="No"
                                        wire:model.live="default_enabled" />
                                </div>
                            </div>
                        </div>

                        @if ($default_enabled === 'yes')
                        <div class="max-w-md">
                            <x-form.input
                                name="default_value"
                                label="Default Value"
                                placeholder="Enter default value"
                                wire:model.lazy="default_value" />
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    <div class="flex justify-end space-x-3">
                        <button type="button"
                            wire:click="closeModal"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
                            Cancel
                        </button>
                        <button type="button"
                            wire:click="saveField"
                            class="px-6 py-2 bg-blue-600 border border-transparent rounded-lg text-white font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            Save Field
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>