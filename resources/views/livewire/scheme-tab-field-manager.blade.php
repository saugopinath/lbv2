<div class="max-w-7xl mx-auto bg-white rounded-xl shadow p-6 space-y-6">

    {{-- Scheme --}}
    <x-form.select label="Select Scheme" wire:model.live="schemeId" :disabled="$lockScheme">
        <option value="">-- Select --</option>
        @foreach($schemes as $scheme)
            <option value="{{ $scheme->id }}">{{ $scheme->name }}</option>
        @endforeach
    </x-form.select>

    @if($schemeId)

        {{-- ACCORDION TABS --}}
        <div class="space-y-4">
            @foreach($tabs as $tab)
                <div x-data="{ open:false }" class="border rounded-lg overflow-hidden">

                    {{-- Header --}}
                    <div class="flex justify-between items-center bg-gray-100 px-4 py-3">
                        <span class="font-semibold">
                            {{ $tab->position }}. {{ $tab->masterTab->tab_name }}
                        </span>

                        <div class="flex gap-2 items-center">
                            <button wire:click="openManageModal({{ $tab->tab_code }})"
                                class="px-3 py-1 bg-indigo-600 text-white rounded text-sm">
                                Manage Fields
                            </button>

                            <button wire:click="openPreview({{ $tab->tab_code }})"
                                class="px-3 py-1 bg-gray-600 text-white rounded text-sm">
                                Preview
                            </button>

                            <button @click="open=!open" class="text-sm">
                                ▼
                            </button>
                        </div>
                    </div>

                    {{-- Body --}}
                    <div x-show="open" x-collapse class="bg-white">
                        @if(isset($tabFields[$tab->tab_code]) && count($tabFields[$tab->tab_code]))

                            {{-- FIELD GRID (Drag & Drop) --}}
                            <div class="grid grid-cols-2 gap-3 p-4" x-data
                                x-init="
                                    new Sortable($el, {
                                        animation: 150,
                                        handle: '.drag-handle',
                                        onEnd() {
                                            let ordered = Array.from($el.children)
                                                .map(el => el.dataset.fid);
                                            $wire.updateFieldOrder({{ $tab->tab_code }}, ordered);
                                        }
                                    })
                                ">
                                @foreach($tabFields[$tab->tab_code] as $fid => $fname)
                                    <div data-fid="{{ $fid }}"
                                        class="flex items-center justify-between bg-gray-50 border border-gray-200 rounded p-3">
                                        {{-- SERIAL NUMBER --}}

                                        <div class="flex items-center gap-2">
                                            <span class="drag-handle cursor-move text-gray-400">☰</span>
                                            <span class="font-semibold text-gray-600">
                                                {{ $loop->iteration }}.{{ $fname }}
                                            </span>

                                        </div>

                                        {{-- IMPORTANT FIX --}}
                                        <button wire:click.stop="removeField({{ $tab->tab_code }}, '{{ $fid }}')"
                                            class="text-red-600 font-bold">
                                            ✕
                                        </button>

                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="p-4 text-gray-400 text-sm">
                                No fields added
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        {{-- FINAL ACTION BUTTONS --}}
        <div class="flex justify-center gap-4 pt-6">
            <x-button.primary wire:click="openFinalPreview" class="bg-blue-600 hover:bg-blue-700">
                Form Preview
            </x-button.primary>

            <x-button.primary wire:click="finalSubmit" class="bg-green-600 hover:bg-green-700">
                Final Submit
            </x-button.primary>
        </div>

    @endif

    {{-- MANAGE MODAL --}}
    @if($showManageModal)
        <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
            <div class="bg-white w-full max-w-3xl rounded-lg shadow-lg overflow-hidden">

                <div class="bg-indigo-100 px-6 py-4 font-semibold">
                    Manage Fields
                </div>

                <div class="p-6 grid grid-cols-2 gap-3 max-h-[70vh] overflow-y-auto">
                    @foreach($modalFields as $field)
                        <label class="flex gap-3 items-center bg-gray-50 p-3 rounded">
                            <input type="checkbox" wire:model="modalSelected" value="{{ $field['field_id'] }}">
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

    @if($showPreviewModal)
        <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
            <div class="bg-white w-full max-w-3xl rounded-lg shadow-lg overflow-hidden">

                {{-- Header --}}
                <div class="bg-green-100 px-6 py-4 text-center font-semibold">
                    {{ $previewTabName }}
                </div>

                {{-- Body --}}
                <div class="p-6 space-y-4 max-h-[80vh] overflow-y-auto">

                    <div class="grid gap-2 md:grid-cols-2 pl-4 pr-4">
                        @foreach($this->previewFields as $index => $field)
                            {{-- FIELD RENDER --}}
                            @if($field->field_type === 'text')
                                <div>
                                    <x-form.input name="{{ $field->field_name }}" label="{!! $field->level_name !!}" disabled />
                                </div>


                            @elseif($field->field_type === 'date')
                                <div>

                                    <x-form.input type="date" name="{{ $field->field_name }}" label="{{ $field->level_name }}"
                                        disabled />
                                </div>

                            @elseif($field->field_type === 'select')
                                <div>

                                    <x-form.select name="{{ $field->field_name }}" label="{{ $field->level_name }}" disabled>
                                        <option value="">-- Select --</option>
                                        @foreach($field->options ?? [] as $opt)
                                            <option>{{ $opt }}</option>
                                        @endforeach
                                    </x-form.select>
                                </div>

                            @elseif($field->field_type === 'textarea')
                                <div>

                                    <x-form.textarea name="{{ $field->field_name }}" label="{{ $field->level_name }}" disabled />
                                </div>

                            @else
                                <div class="text-red-500 text-sm">
                                    Unsupported field type: {{ $field->field_type }}
                                </div>
                            @endif


                        @endforeach
                    </div>

                </div>

                {{-- Footer --}}
                <div class="flex justify-end px-6 py-4 border-t">
                    <button wire:click="closePreview" class="px-6 py-2 bg-indigo-600 text-white rounded">
                        Close
                    </button>
                </div>

            </div>
        </div>
    @endif

    @if($showFinalPreview)
        <div class="fixed inset-0 z-50 bg-black/75 flex items-center justify-center">
            <div class="bg-white rounded-xl shadow-lg w-auto max-w-auto">

                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b">
                    <h3 class="text-lg font-semibold text-gray-800">
                        Final Preview
                    </h3>
                    <button wire:click="closeFinalPreview" class="text-gray-400 hover:text-gray-600 text-xl">
                        ✕
                    </button>
                </div>

                {{-- TAB NAV --}}
                <div class="px-6 pt-4">
                    <nav class="flex space-x-6 border-b">
                        @foreach($tabs as $index => $tab)
                            <x-entrytab-nav-link :active="$index === 0" :icon="$tab->masterTab?->tab_icon">
                                {{ $tab->masterTab?->tab_name }}
                            </x-entrytab-nav-link>
                        @endforeach
                    </nav>
                </div>

                {{-- TAB CONTENT --}}
                <div class="px-6 py-8 space-y-6 max-h-[60vh] overflow-y-auto">

                    {{-- @foreach($tabs as $tab)
                    <div class="border rounded-lg p-4">
                        <h4 class="font-semibold mb-3">
                            {{ $tab->masterTab?->tab_name }}
                        </h4>

                        @if(isset($tabFields[$tab->tab_code]) && count($tabFields[$tab->tab_code]))
                        <ul class="list-disc ml-6 text-sm text-gray-700 space-y-1">
                            @foreach($tabFields[$tab->tab_code] as $field)
                            <li>{{ $field }}</li>
                            @endforeach
                        </ul>
                        @else
                        <p class="text-gray-400 text-sm">
                            No fields added
                        </p>
                        @endif
                    </div>
                    @endforeach --}}

                </div>

                {{-- Footer --}}
                <div class="flex justify-end gap-3 px-6 py-4 border-t">
                    <x-button.primary wire:click="closeFinalPreview" type="button">
                        Close
                    </x-button.primary>
                </div>

            </div>
        </div>
    @endif

    {{-- SUCCESS MESSAGE --}}
    @if(session()->has('message'))
        <div class="rounded-lg bg-green-50 border border-green-200 p-3 text-green-700 font-medium">
            {{ session('message') }}
        </div>
    @endif

</div>