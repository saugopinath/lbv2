<div class="max-w-7xl mx-auto bg-white rounded-xl shadow p-6 space-y-6">

    {{-- Scheme --}}
    <x-form.select label="Select Scheme" wire:model.live="schemeId" :disabled="$lockScheme">
        <option value="">-- Select --</option>
        @foreach($schemes as $scheme)
            <option value="{{ $scheme->id }}">{{ $scheme->name }}</option>
        @endforeach
    </x-form.select>

    @if($schemeId)

        {{-- TABS --}}
        <div class="space-y-4">
            @foreach($tabs as $tab)
                <div x-data="{ open:false }" class="border rounded-lg overflow-hidden">

                    {{-- Header --}}
                    <div class="flex justify-between items-center bg-gray-100 px-4 py-3">
                        <span class="font-semibold">
                            {{ $tab->position }}. {{ $tab->masterTab->tab_name }}
                        </span>

                        <div class="flex gap-2">
                            <button wire:click="openManageModal({{ $tab->tab_code }})"
                                class="px-3 py-1 bg-indigo-600 text-white rounded text-sm">
                                Manage Fields
                            </button>

                            <button wire:click="openPreview({{ $tab->tab_code }})"
                                class="px-3 py-1 bg-gray-600 text-white rounded text-sm">
                                Preview
                            </button>

                            <button @click="open=!open">▼</button>
                        </div>
                    </div>

                    {{-- Body --}}
                    <div x-show="open" x-collapse class="bg-white">
                        @if(isset($tabFields[$tab->tab_code]))

                            <div class="grid grid-cols-2 gap-3 p-4"
                                 x-data
                                 wire:ignore
                                 x-init="
                                    new Sortable($el,{
                                        animation:150,
                                        handle:'.drag-handle',
                                        onEnd(){
                                            let ordered=[...$el.children].map(el=>el.dataset.fid);
                                            $wire.updateFieldOrder({{ $tab->tab_code }},ordered);
                                        }
                                    })
                                 ">

                                @foreach($tabFields[$tab->tab_code] as $fid => $field)
                                    <div data-fid="{{ $fid }}"
                                         class="flex justify-between items-center bg-gray-50 border rounded p-3">

                                        <div class="flex items-center gap-2">
                                            <span class="drag-handle cursor-move text-gray-400">☰</span>
                                            <span class="text-sm font-medium">
                                                {{ $tab->position }}.{{ $field['position'] }}
                                                {{ $field['name'] }}
                                            </span>
                                        </div>

                                        <button
                                            wire:click="removeField({{ $tab->tab_code }}, '{{ $fid }}')"
                                            class="text-red-600 font-bold">
                                            ✕
                                        </button>
                                    </div>
                                @endforeach
                            </div>

                        @else
                            <div class="p-4 text-gray-400 text-sm">No fields added</div>
                        @endif
                    </div>

                </div>
            @endforeach
        </div>
    @endif

    {{-- MANAGE MODAL --}}
    @if($showManageModal)
        <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center">
            <div class="bg-white w-full max-w-3xl rounded shadow">

                <div class="bg-indigo-100 px-6 py-4 font-semibold">
                    Manage Fields
                </div>

                <div class="p-6 grid grid-cols-2 gap-3 max-h-[70vh] overflow-y-auto">
                    @foreach($modalFields as $field)
                        <label class="flex gap-3 items-center bg-gray-50 p-3 rounded">
                            <input type="checkbox"
                                   wire:model="modalSelected"
                                   value="{{ $field['field_id'] }}">
                            {{ $field['field_name'] }}
                        </label>
                    @endforeach
                </div>

                <div class="flex justify-end gap-3 px-6 py-4 border-t">
                    <button wire:click="closeManageModal" class="px-5 py-2 bg-gray-300 rounded">
                        Cancel
                    </button>
                    <button wire:click="saveManageFields" class="px-5 py-2 bg-indigo-600 text-white rounded">
                        Save
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- PREVIEW --}}
    @if($showPreviewModal)
        <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center">
            <div class="bg-white w-full max-w-3xl rounded shadow">

                <div class="bg-green-100 px-6 py-4 font-semibold text-center">
                    Preview
                </div>

                <div class="p-6 space-y-3">
                    @foreach($tabFields[$activeTabCode] ?? [] as $field)
                        <div class="border rounded p-3">
                            {{ $tabs->firstWhere('tab_code',$activeTabCode)->position }}
                            .{{ $field['position'] }}
                            {{ $field['name'] }}
                        </div>
                    @endforeach
                </div>

                <div class="flex justify-end px-6 py-4 border-t">
                    <button wire:click="closePreview"
                            class="px-6 py-2 bg-indigo-600 text-white rounded">
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
