<div class="max-w-7xl mx-auto bg-white rounded-xl shadow p-6 space-y-6">

    {{-- Scheme --}}
    <x-form.select label="Select Scheme" wire:model.live="schemeId" :disabled="$lockScheme">
        <option value="">-- Select --</option>
        @foreach($schemes as $scheme)
            <option value="{{ $scheme->id }}">{{ $scheme->name }}</option>
        @endforeach
    </x-form.select>

    @if($schemeId)

        {{-- Tabs Table --}}
        <div class="border rounded-lg overflow-hidden">
            <table class="min-w-full text-sm">
                <tbody>
                    @foreach($tabs as $tab)
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ $tab->position }}</td>
                            <td class="px-4 py-2 font-semibold">
                                {{ $tab->masterTab->tab_name }}
                            </td>
                            <td class="px-4 py-2">
                                <div class="flex justify-end gap-2">
                                    <button wire:click="openManageModal({{ $tab->tab_code }})"
                                        class="px-3 py-1 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                                        Manage Fields
                                    </button>

                                    <button wire:click="openPreview({{ $tab->tab_code }})"
                                        class="px-3 py-1 bg-gray-600 text-white rounded hover:bg-gray-700">
                                        Preview
                                    </button>
                                </div>
                            </td>
                        </tr>

                        {{-- Selected Fields Table --}}
                        @if(isset($tabFields[$tab->tab_code]))
                            <tr class="bg-gray-50">
                                <td colspan="3" class="p-4">
                                    <table class="w-full text-sm border">
                                        <tbody>
                                            @foreach($tabFields[$tab->tab_code] as $fid => $fname)
                                                <tr class="border-t">
                                                    <td class="px-3 py-2">{{ $fname }}</td>
                                                    <td class="px-3 py-2 text-right">
                                                        <button wire:click="removeField({{ $tab->tab_code }}, '{{ $fid }}')"
                                                            class="text-red-600">
                                                            ✕
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        @endif

                    @endforeach
                </tbody>
            </table>
        </div>
    @endif


    {{-- MANAGE MODAL --}}
    @if($showManageModal)
        <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center">
            <div class="bg-white w-full max-w-lg rounded-xl shadow">
                <div class="px-6 py-4 border-b flex justify-between">
                    <h3 class="font-semibold">Manage Fields</h3>
                    <button wire:click="closeManageModal">✕</button>
                </div>

                <div class="p-6 max-h-96 overflow-y-auto space-y-2">
                    @foreach($modalFields as $field)
                        <label class="flex gap-3 items-center bg-gray-50 p-2 rounded">
                            <input type="checkbox" wire:model="modalSelected" value="{{ $field['field_id'] }}">
                            <span>{{ $field['field_name'] }}</span>
                        </label>
                    @endforeach
                </div>

                <div class="px-6 py-4 border-t flex justify-end gap-2">
                    <button wire:click="closeManageModal" class="px-4 py-2 bg-gray-300 rounded">
                        Cancel
                    </button>
                    <button wire:click="saveManageFields" class="px-4 py-2 bg-indigo-600 text-white rounded">
                        Save
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- PREVIEW MODAL --}}
    @if($showPreviewModal)
        <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center">
            <div class="bg-white w-full max-w-md rounded-xl shadow p-6">
                <h3 class="font-semibold mb-4">Preview</h3>

                <ul class="list-disc ml-6 text-sm">
                    @foreach($tabFields[$activeTabCode] ?? [] as $field)
                        <li>{{ $field }}</li>
                    @endforeach
                </ul>

                <div class="text-right mt-4">
                    <button wire:click="closePreview" class="px-4 py-2 bg-indigo-600 text-white rounded">
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>