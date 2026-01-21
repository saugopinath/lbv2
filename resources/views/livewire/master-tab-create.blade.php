<div class="bg-white p-6 rounded-xl shadow">

    <div class="space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Database Fields</h1>
                <p class="text-sm text-gray-600 mt-1">Manage database columns and form fields for {{ $model_name ?? 'your model' }}</p>
            </div>
            <div class="flex items-center space-x-3">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" placeholder="Search fields..."
                        class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-full sm:w-64">
                </div>
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
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Description</th>
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
                                        <div class="text-sm font-medium text-gray-900">{{ $field['db_column'] }}</div>
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
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $field['description'] ?? '—' }}</div>
                                @if($field['nullable'])
                                <span class="text-xs text-gray-500">Nullable</span>
                                @endif
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
            <div class="relative bg-white rounded-xl shadow-xl w-full max-w-4xl max-h-[90vh] flex flex-col overflow-hidden">
                <!-- Modal Header -->
                <div class="px-6 pt-6 pb-4 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900">Add / Edit Field</h2>
                    <p class="text-sm text-gray-600 mt-1">Configure database column and form field properties</p>
                </div>

                <!-- Scrollable Content Area -->
                <div class="flex-1 overflow-y-auto p-6">
                    <!-- Read-only Info Section -->
                    <div class="mb-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Tab Name
                                </label>
                                <input type="text"
                                    wire:model.live="tab_name"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Tab Short Name
                                </label>
                                <input type="text"
                                    readonly wire:model="tab_short_name"
                                    class="w-full border border-gray-300 bg-gray-50 rounded-lg px-3 py-2 text-gray-600 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Tab Code
                                </label>
                                <input type="text"
                                    readonly wire:model="tab_code"
                                    class="w-full border border-gray-300 bg-gray-50 rounded-lg px-3 py-2 text-gray-600 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Model Name</label>
                                <input readonly wire:model="model_name"
                                    class="w-full border border-gray-300 bg-gray-50 rounded-lg px-3 py-2 text-gray-600 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Table Name</label>
                                <input readonly wire:model="table_name"
                                    class="w-full border border-gray-300 bg-gray-50 rounded-lg px-3 py-2 text-gray-600 text-sm">
                            </div>
                        </div>
                    </div>

                    <!-- Column Info Section -->
                    <div class="mb-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Column Information</h3>
                            <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">Database</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Column Name *</label>
                                <input wire:model.live="column_name" placeholder="e.g., user_name"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Data Type *</label>
                                <select wire:model="column_type"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                    <option value="string">String</option>
                                    <option value="integer">Integer</option>
                                    <option value="text">Text</option>
                                    <option value="date">Date</option>
                                    <option value="boolean">Boolean</option>
                                    <option value="decimal">Decimal</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Length</label>
                                <input wire:model="length" placeholder="255"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Key Type</label>
                                <select wire:model="key_type"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                    <option value="none">No Key</option>
                                    <option value="primary">Primary Key</option>
                                    <option value="unique">Unique Key</option>
                                    <option value="foreign">Foreign Key</option>
                                    <option value="index">Index</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div class="flex items-center space-x-3">
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" wire:model.live="nullable"
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="text-sm font-medium text-gray-700">Nullable</span>
                                </label>
                            </div>

                            <div class="flex items-center space-x-3">
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" wire:model.live="default_enabled"
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="text-sm font-medium text-gray-700">Default Value</span>
                                </label>
                            </div>

                            <div>
                                @if($default_enabled)
                                <label class="block text-sm font-medium text-gray-700 mb-1">Default</label>
                                <input wire:model="default_value" placeholder="Enter default value"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                @endif
                            </div>


                        </div>
                    </div>

                    <!-- Form Field Section -->
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Form Field Information</h3>
                            <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">UI</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Field Name *</label>
                                <input wire:model.live="field_name" placeholder="e.g., User Name"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Field ID</label>
                                <input wire:model="field_id" readonly placeholder="e.g., user-name"
                                    class="w-full border border-gray-300 bg-gray-50 rounded-lg px-3 py-2 text-gray-600">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Field Label</label>
                                <input wire:model="field_label" placeholder="e.g., Enter your name"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Field Type</label>
                                <select wire:model="field_type"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                    <option value="text">Text Input</option>
                                    <option value="textarea">Textarea</option>
                                    <option value="select">Select Dropdown</option>
                                    <option value="date">Date Picker</option>
                                    <option value="checkbox">Checkbox</option>
                                    <option value="radio">Radio Buttons</option>
                                    <option value="file">File Upload</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">DB Column</label>
                                <input wire:model="db_column" readonly
                                    class="w-full border border-gray-300 bg-gray-50 rounded-lg px-3 py-2 text-gray-600">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Validation Rules</label>
                            <div class="relative">
                                <input wire:model="validation_rule"
                                    placeholder="required|string|max:255|min:3"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 pl-10 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Separate rules with | character. Leave empty for no validation.</p>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    <div class="flex justify-end space-x-3">
                        <button type="button" wire:click="closeModal"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
                            Cancel
                        </button>
                        <button type="button" wire:click="saveField"
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