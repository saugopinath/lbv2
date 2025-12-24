<div class="p-6 max-w-7xl mx-auto">
    <h1 class="text-3xl font-bold mb-8">Menu Tab Manager</h1>

    <select wire:model.live="selectedSchemeId" wire:change="loadAssignedTabs"
            class="border rounded-lg px-4 py-3 text-lg mb-8 w-full md:w-96">
        <option value="">-- Select a Scheme --</option>
        @foreach($schemes as $scheme)
            <option value="{{ $scheme->id }}">{{ $scheme->name }}</option>
        @endforeach
    </select>

    @if(session('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('message') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            {{ session('error') }}
        </div>
    @endif

    @if($assignedTabs->isNotEmpty())
        @foreach($assignedTabs as $mapping)
            <div class="mb-10 bg-white shadow-lg rounded-xl p-8 border">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold">
                        {{ $mapping->masterTab->tab_name }}
                        <span class="text-gray-500 text-lg">(Position: {{ $mapping->position }})</span>
                    </h2>

                    <div class="flex gap-3">
                        @if(!$mapping->is_finally_submitted)
                            <button wire:click="openAddModal('{{ $mapping->tab_code }}')"
                                    class="bg-green-600 hover:bg-green-700 text-white font-medium px-5 py-3 rounded-lg">
                                Add Extra Field
                            </button>
                        @endif

                        <button wire:click="openPreview('{{ $mapping->tab_code }}')"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-3 rounded-lg">
                            Preview
                        </button>

                        @if(!$mapping->is_finally_submitted)
                            <button wire:click="finalSubmit({{ $mapping->id }})"
                                    class="bg-red-600 hover:bg-red-700 text-white font-medium px-5 py-3 rounded-lg">
                                Final Submit
                            </button>
                        @else
                            <span class="bg-gray-300 text-gray-700 font-medium px-5 py-3 rounded-lg">
                                Finalized
                            </span>
                        @endif
                    </div>
                </div>

                <table class="w-full border border-gray-300 rounded-lg overflow-hidden">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border border-gray-300 px-4 py-3 text-left">Field Name</th>
                            <th class="border border-gray-300 px-4 py-3 text-left">Type</th>
                            <th class="border border-gray-300 px-4 py-3 text-left">Position</th>
                            <th class="border border-gray-300 px-4 py-3 text-left">Active</th>
                            <th class="border border-gray-300 px-4 py-3 text-left">Kind</th>
                            <th class="border border-gray-300 px-4 py-3 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($fieldsByTab[$mapping->tab_code] ?? [] as $field)
                            <tr class="hover:bg-gray-50">
                                <td class="border border-gray-300 px-4 py-3">{{ $field->field_name }}</td>
                                <td class="border border-gray-300 px-4 py-3">{{ $field->field_type }}</td>
                                <td class="border border-gray-300 px-4 py-3">{{ $field->field_position }}</td>
                                <td class="border border-gray-300 px-4 py-3">{{ $field->is_active ? 'Yes' : 'No' }}</td>
                                <td class="border border-gray-300 px-4 py-3">
                                    <span class="px-3 py-1 text-xs font-medium rounded-full
                                        {{ $field->is_common ? 'bg-purple-100 text-purple-800' : 'bg-orange-100 text-orange-800' }}">
                                        {{ $field->is_common ? 'Common' : 'Extra' }}
                                    </span>
                                </td>
                                <td class="border border-gray-300 px-4 py-3">
                                    @if(!$field->is_common && !$mapping->is_finally_submitted)
                                        <button wire:click="openEditModal('{{ $mapping->tab_code }}', {{ $field->id }})"
                                                class="text-blue-600 hover:underline mr-4">Edit</button>
                                        <button x-on:click="if(confirm('Delete {{ addslashes($field->field_name) }}? This cannot be undone.')) $wire.deleteField({{ $field->id }})"
                                                class="text-red-600 hover:underline">Delete</button>
                                    @else
                                        <span class="text-gray-500 italic">Read Only</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-gray-500 py-6">
                                    No fields defined for this tab yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endforeach
    @elseif($selectedSchemeId)
        <div class="text-center text-gray-600 py-10">
            No tabs assigned to this scheme yet. Please assign tabs first in <strong>Master Tab Manager</strong>.
        </div>
    @endif

    <!-- Add/Edit Modal -->
    <div x-data="{ open: @entangle('showModal') }"
         x-show="open"
         x-transition.opacity
         @keydown.escape.window="open = false"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 overflow-y-auto py-8"
         style="display: none;">
        <div @click.away="open = false"
             class="bg-white rounded-xl shadow-2xl p-8 w-full max-w-2xl mx-4 my-8 max-h-full overflow-y-auto">
            <h2 class="text-2xl font-bold mb-6">
                {{ $editingFieldId ? 'Edit' : 'Add New' }} Extra Field — {{ $currentTabCode }}
            </h2>

            <form wire:submit="saveField" class="space-y-5">
                <!-- (All form fields same as before - level_name, field_name, field_id, field_type, options, validation_rule, regex, is_active) -->
                <div>
                    <label class="block font-medium mb-1">Level Name</label>
                    <input type="text" wire:model="form.level_name" class="w-full border rounded-lg px-4 py-2">
                </div>
                <div>
                    <label class="block font-medium mb-1">Field Name <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="form.field_name" class="w-full border rounded-lg px-4 py-2">
                    @error('form.field_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block font-medium mb-1">Field ID <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="form.field_id" class="w-full border rounded-lg px-4 py-2">
                    @error('form.field_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block font-medium mb-1">Field Type <span class="text-red-500">*</span></label>
                    <select wire:model="form.field_type" class="w-full border rounded-lg px-4 py-2">
                        <option value="">Select Type</option>
                        <option value="text">Text</option>
                        <option value="number">Number</option>
                        <option value="email">Email</option>
                        <option value="date">Date</option>
                        <option value="select">Select/Dropdown</option>
                        <option value="checkbox">Checkbox</option>
                        <option value="textarea">Textarea</option>
                        <option value="file">File Upload</option>
                    </select>
                </div>
                <div>
                    <label class="block font-medium mb-1">Options (JSON or comma-separated)</label>
                    <textarea wire:model="form.options" class="w-full border rounded-lg px-4 py-2 h-28"></textarea>
                </div>
                <div>
                    <label class="block font-medium mb-1">Validation Rule</label>
                    <input type="text" wire:model="form.validation_rule" class="w-full border rounded-lg px-4 py-2">
                </div>
                <div>
                    <label class="block font-medium mb-1">Regex Pattern</label>
                    <input type="text" wire:model="form.regex" class="w-full border rounded-lg px-4 py-2">
                </div>
                <div class="flex items-center">
                    <input type="checkbox" wire:model="form.is_active" class="mr-3 h-5 w-5">
                    <label class="font-medium">Is Active</label>
                </div>

                <div class="flex justify-end gap-4 mt-8">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-8 py-3 rounded-lg">
                        {{ $editingFieldId ? 'Update Field' : 'Add Field' }}
                    </button>
                    <button type="button" @click="open = false" class="bg-gray-500 hover:bg-gray-600 text-white font-bold px-8 py-3 rounded-lg">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Preview Modal -->
    <div x-data="{ open: @entangle('showPreview') }"
         x-show="open"
         x-transition
         @keydown.escape.window="open = false"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60 overflow-y-auto py-8"
         style="display: none;">
        <div @click.away="open = false"
             class="bg-white rounded-xl shadow-2xl p-8 w-full max-w-4xl mx-4 my-8 max-h-full overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">
                    Preview: {{ $assignedTabs->firstWhere('tab_code', $previewTabCode)?->masterTab->tab_name ?? 'Tab' }}
                </h2>
                <button @click="open = false" class="text-gray-500 hover:text-gray-700 text-3xl">&times;</button>
            </div>

            <div class="bg-gray-50 rounded-lg p-8 border-2 border-dashed border-gray-300">
                <h3 class="text-xl font-semibold mb-8 text-center text-gray-700">Form Preview</h3>

                @if(isset($fieldsByTab[$previewTabCode]) && $fieldsByTab[$previewTabCode]->isNotEmpty())
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($fieldsByTab[$previewTabCode] as $field)
                            <div class="bg-white p-6 rounded-lg shadow border">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ $field->field_name }}
                                    @if(str_contains($field->validation_rule ?? '', 'required'))
                                        <span class="text-red-500">*</span>
                                    @endif
                                    <span class="text-xs text-gray-500 ml-2">
                                        ({{ ucfirst($field->field_type) }} {{ $field->is_common ? '• Common' : '• Extra' }})
                                    </span>
                                </label>

                                @switch($field->field_type)
                                    @case('text')
                                    @case('email')
                                    @case('number')
                                    @case('date')
                                        <input type="{{ $field->field_type }}"
                                               class="w-full px-4 py-3 border rounded-lg bg-gray-50"
                                               placeholder="{{ $field->field_name }}" disabled>
                                        @break
                                    @case('textarea')
                                        <textarea class="w-full px-4 py-3 border rounded-lg bg-gray-50" rows="4" placeholder="{{ $field->field_name }}" disabled></textarea>
                                        @break
                                    @case('select')
                                        <select class="w-full px-4 py-3 border rounded-lg bg-gray-50" disabled>
                                            <option>Select...</option>
                                            @php
                                                $options = $field->options ? (is_json($field->options)
                                                    ? json_decode($field->options, true)
                                                    : array_map('trim', explode(',', $field->options))) : [];
                                            @endphp
                                            @foreach($options as $key => $value)
                                                <option value="{{ is_numeric($key) ? $value : $key }}">{{ is_numeric($key) ? $value : $value }}</option>
                                            @endforeach
                                        </select>
                                        @break
                                    @case('checkbox')
                                        <div class="flex items-center mt-2">
                                            <input type="checkbox" class="mr-3 h-5 w-5" disabled>
                                            <span class="text-gray-700">{{ $field->field_name }}</span>
                                        </div>
                                        @break
                                    @case('file')
                                        <input type="file" class="w-full px-4 py-3 border rounded-lg bg-gray-50" disabled>
                                        @break
                                    @default
                                        <input type="text" class="w-full px-4 py-3 border rounded-lg bg-gray-50" placeholder="{{ $field->field_name }}" disabled>
                                @endswitch
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-center text-gray-500 py-10 text-lg">No fields defined yet.</p>
                @endif
            </div>

            <div class="mt-8 text-center">
                <button @click="open = false" class="bg-gray-600 hover:bg-gray-700 text-white font-bold px-8 py-3 rounded-lg">
                    Close Preview
                </button>
            </div>
        </div>
    </div>
</div>
