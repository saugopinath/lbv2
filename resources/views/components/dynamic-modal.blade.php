@props([
    'showVar' => 'showModal',
    'title' => 'Add Details',
    'fields' => [],
    'targetVar' => 'newLand',
    'submitAction' => 'addLand()',
])

<div x-show="{{ $showVar }}" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
    <div class="flex items-center justify-center min-h-screen w-full p-4 text-center">
        <!-- Backdrop -->
        <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="{{ $showVar }} = false">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <!-- Modal Content -->
        <div class="inline-block bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full z-10">
            <div class="bg-blue-600 px-6 py-4 text-white">
                <h3 class="text-lg font-bold">{{ $title }}</h3>
            </div>
            <div class="bg-white px-6 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="space-y-4">
                    @foreach($fields as $field)
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">
                                {{ $field['label'] }}
                                @if($field['is_required'] ?? false)
                                    <span class="text-red-500">*</span>
                                @endif
                            </label>
                            @if(($field['type'] ?? 'text') === 'select')
                                <select x-model="{{ $targetVar }}.{{ $field['name'] }}" class="w-full py-2 px-3 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                    <option value="">-- Select --</option>
                                    @foreach($field['options'] ?? [] as $val => $lbl)
                                        <option value="{{ $val }}">{{ $lbl }}</option>
                                    @endforeach
                                </select>
                            @else
                                <input type="{{ $field['type'] ?? 'text' }}" x-model="{{ $targetVar }}.{{ $field['name'] }}" class="w-full py-2 px-3 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-sm" placeholder="{{ $field['placeholder'] ?? '' }}" />
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <div class="text-sm font-semibold text-red-600 w-full sm:w-auto" x-show="modalError" x-html="modalError" style="display: none;"></div>
                <div class="flex justify-end gap-2 w-full sm:w-auto">
                    <button type="button" @click="{{ $showVar }} = false; modalError = ''" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-100 text-sm font-semibold">Cancel</button>
                    <button type="button" @click="{{ $submitAction }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm font-semibold">Add</button>
                </div>
            </div>
        </div>
    </div>
</div>