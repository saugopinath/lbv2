<div class="max-w-7xl mx-auto bg-white rounded-xl shadow p-6 space-y-6">

    {{-- Scheme --}}
    <x-form.select label="Select Scheme" wire:model.live="schemeId" :disabled="$lockScheme">
        <option value="">-- Select --</option>
        @foreach($schemes as $scheme)
        <option value="{{ $scheme->id }}">{{ $scheme->name }}</option>
        @endforeach
    </x-form.select>

    @if($schemeId)
    <div class="space-y-4">
        @foreach($tabs as $tab)
        <div x-data="{ open:false }" class="border border-b-cyan-300 rounded-lg overflow-hidden">
            <div class="flex justify-between items-center bg-gray-100 px-4 py-3">
                <span class="font-semibold">
                    {{ $tab->position }}. {{ $tab->masterTab->tab_name }}
                </span>
                <div class="flex gap-2 items-center">
                    <a href="{{ route('create-dynamicformfield', [
                    'ref' => encrypt($tab->scheme_id . '|' . $tab->tab_code)]) }}">
                        <x-button.primary class="bg-indigo-400 hover:bg-indigo-500 text-sm">
                            Add Another Fields
                        </x-button.primary>
                    </a>
                    <x-button.primary wire:click="openManageModal({{ $tab->tab_code }})"
                        class="bg-green-600 hover:bg-green-700 text-sm">
                        Manage Fields
                    </x-button.primary>
                    <x-button.primary wire:click="openPreview({{ $tab->tab_code }})"
                        class="bg-gray-500 hover:bg-gray-600 text-sm">
                        Preview
                    </x-button.primary>

                    <button @click="open=!open" class="text-sm">
                        ▼
                    </button>
                </div>
            </div>
            {{-- Body --}}
            <div x-show="open" x-collapse class="bg-white">
                @if(isset($tabFields[$tab->tab_code]) && count($tabFields[$tab->tab_code]))
                <div class="grid grid-cols-2 gap-3 p-4" x-data x-init="
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
                        <button wire:click.stop="removeField({{ $tab->tab_code }}, '{{ $fid }}')" class="
                        text-red-500 font-bold text-lg
                        @if($this->isFieldMandatory($fid))
                            opacity-50 cursor-not-allowed
                        @else
                            hover:text-red-600
                        @endif
                    " @if($this->isFieldMandatory($fid)) disabled @endif>
                            ✕
                        </button>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="p-4 text-gray-400 text-sm text-center">
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
            <div class="p-6 max-h-[70vh] overflow-y-auto">
                @if($activeTabCode == 104)
                {{-- DOCUMENT FORM --}}
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <x-form.select name="doc_type_id" label="Document Type" wire:model="selectedDocType">
                        <option value="">-- Select Document --</option>
                        @foreach($docTypes as $doc)
                        <option value="{{ $doc->id }}">{{ $doc->name }}</option>
                        @endforeach
                    </x-form.select>

                    <x-form.select name="is_required" label="Is Required" wire:model="isRequired">
                        <option value="0">No</option>
                        <option value="1">Yes</option>
                    </x-form.select>
                    <x-form.input name="max_file_size" label="Max File Size" wire:model="maxFileSize" />
                    <div class="col-span-2">
                        <label class="font-semibold">Allowed Extensions</label>
                        <div class="flex gap-3 mt-1">
                            @foreach(['jpg', 'jpeg', 'png', 'pdf'] as $ext)
                            <label class="flex items-center gap-2">
                                <input type="checkbox" wire:model="extensionTypes" value="{{ $ext }}">
                                {{ strtoupper($ext) }}
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                @if($attachedDocuments->count())
                <div class="border-t pt-4 mt-4">
                    <h3 class="font-semibold mb-3">
                        Attached Documents
                    </h3>

                    <div class="space-y-2">
                        @foreach($attachedDocuments as $doc)
                        <div class="flex justify-between items-center bg-gray-50 p-3 rounded border">
                            <div>
                                <div class="font-medium">
                                    {{ $doc->docType->name }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    Size: {{ $doc->max_file_size }} |
                                    Required: {{ $doc->is_required ? 'Yes' : 'No' }} |
                                    Ext: {{ $doc->extension_type }}
                                </div>
                            </div>

                            <button wire:click="removeDocument({{ $doc->id }})" class="text-red-500 font-bold">
                                ✕
                            </button>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                @else
                <div class="grid grid-cols-2 gap-3">
                    @foreach($modalFields as $field)
                    <label
                        class="flex gap-3 items-center p-3 rounded border border-gray-200
                                                                {{ $field['is_mandatory'] ? 'border-green-300 bg-green-50' : 'bg-gray-50' }}">
                        <input type="checkbox" wire:model="modalSelected" value="{{ $field['field_id'] }}"
                            @if($field['is_mandatory'] && $field['tab_code'] !=0) disabled @endif>

                        <span>
                            {{ $field['field_name'] }}
                            @if($field['is_mandatory'])
                            <span class="text-red-500 font-bold">*</span>
                            @endif
                        </span>
                    </label>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- FOOTER --}}
            <div class="flex justify-end gap-3 px-6 py-4 border-t">
                <x-button.primary wire:click="closeManageModal" class="bg-gray-600">
                    Cancel
                </x-button.primary>

                @if($activeTabCode == 104)
                <x-button.primary wire:click="saveDocumentMapping" class="bg-indigo-600">
                    Add Document
                </x-button.primary>
                @else
                <x-button.primary wire:click="saveManageFields" class="bg-indigo-600">
                    Add
                </x-button.primary>
                @endif
            </div>

        </div>
    </div>
    @endif

    @if($showPreviewModal)
    <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-3xl rounded-lg shadow-lg overflow-hidden">


            <div class="bg-green-100 px-6 py-4 text-center font-semibold">
                {{ $previewTabName }}
            </div>

            {{-- Body --}}
            <div class="p-6 space-y-4 max-h-[80vh] overflow-y-auto">

                @if($previewTabCode == 102 &&
                collect($this->previewFields)
                ->pluck('field_name')
                ->intersect([
                'district_id',
                'rural_urban',
                'blockurban',
                'gpWard'
                ])
                ->isNotEmpty()
                )
                {{-- LOCATION COMPONENT --}}
                <div>
                    <livewire:filter-lgd-master-entry :login_type="'state_office'" />
                </div>
                @endif

                @if($previewTabCode == 104 &&
                collect($this->previewFields)
                ->pluck('field_id')
                ->isNotEmpty()
                )
                <div>
                    <livewire:enclosure-list :is_page="1" />
                </div>

                @endif


                <div class="grid gap-2 md:grid-cols-2 pl-4 pr-4">

                    {{-- ===============================
                        NORMAL FIELD RENDERING
                        =============================== --}}
                    @foreach($this->previewFields as $index => $field)
                    {{-- Skip location fields for TAB 102 --}}
                    @if(
                    $activeTabCode == 102 &&
                    in_array($field->field_name, [
                    'district_id',
                    'rural_urban',
                    'blockurban',
                    'gpWard'
                    ])
                    )
                    @continue
                    @endif
                    @if(
                    $activeTabCode == 104 &&
                    in_array($field->id, [
                    '107',
                    '111'
                    ])
                    )
                    @continue
                    @endif
                    {{-- TEXT --}}
                    @if($field->field_type === 'text')
                    <div>
                        <x-form.input name="{{ $field->field_name }}" label="{!! $field->level_name !!}"
                            placeholder="Enter {{ $field->level_name }}" disabled />
                    </div>
                    {{-- DATE --}}
                    @elseif($field->field_type === 'date')
                    <div>
                        <x-form.input type="date" name="{{ $field->field_name }}" label="{{ $field->level_name }}"
                            disabled />
                    </div>
                    {{-- SELECT --}}
                    @elseif($field->field_type === 'select')
                    <div>
                        <x-form.select name="{{ $field->field_name }}" label="{{ $field->level_name }}" disabled>
                            <option value="">
                                -- Select {{ $field->level_name }} --
                            </option>
                            @foreach($field->options ?? [] as $opt)
                            <option>{{ $opt }}</option>
                            @endforeach
                        </x-form.select>
                    </div>
                    {{-- TEXTAREA --}}
                    @elseif($field->field_type === 'textarea')
                    <div>
                        <x-form.textarea name="{{ $field->field_name }}" label="{{ $field->level_name }}"
                            placeholder="Enter {{ $field->level_name }}" disabled />
                    </div>
                    {{-- FALLBACK --}}
                    @else
                    <div class="md:col-span-2 text-red-500 text-sm">
                        Unsupported field type: {{ $field->field_type }}
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>
            {{-- Footer --}}
            <div class="flex justify-end px-6 py-4 border-t">
                <x-button.primary wire:click="closePreview" class="px-6 py-2 bg-indigo-600 text-white rounded">
                    Close
                </x-button.primary>
            </div>

        </div>
    </div>
    @endif

    @if($showFinalPreview)
    <div class="fixed inset-0 z-50 bg-black/75 flex items-center justify-center">
        <div class="bg-white rounded-xl shadow-lg w-auto max-w-auto max-h-[90vh] flex flex-col overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b shrink-0">
                <h3 class="text-lg font-semibold text-gray-800">
                    Final Preview
                </h3>
                <button wire:click="closeFinalPreview" class="text-gray-400 hover:text-gray-600 text-xl">
                    ✕
                </button>
            </div>
            <div class="px-6 pt-4 border-b shrink-0">
                <nav class="flex space-x-6">
                    @foreach($tabs as $tab)
                    <button wire:click="setFinalPreviewTab({{ $tab->tab_code }})" class="flex items-center gap-2 pb-2 text-sm font-medium border-b-2 transition
                            {{ $finalActiveTabCode == $tab->tab_code ? 'border-indigo-600 text-indigo-600'
                            : 'border-transparent text-gray-500 hover:text-gray-700'}}">
                        <x-entrytab-nav-link :active="$tab === 0" :icon="$tab->masterTab?->tab_icon">
                            {{ $tab->masterTab?->tab_name }}
                        </x-entrytab-nav-link>
                    </button>
                    @endforeach
                </nav>
            </div>
            {{-- Content --}}
            <div class="p-6 max-h-[70vh] overflow-y-auto">
                @if($finalPreviewFields->isEmpty())
                <div class="text-center text-gray-400">
                    No fields configured for this tab
                </div>
                @else
                <div class="grid md:grid-cols-2 gap-4">
                    @foreach($finalPreviewFields as $field)

                    @if($field->field_type === 'text')
                    <div>
                        <x-form.input name="{{ $field->field_name }}" label="{!! $field->level_name !!}"
                            placeholder="Enter {{ $field->level_name }}" disabled />
                    </div>

                    {{-- DATE --}}
                    @elseif($field->field_type === 'date')
                    <div>
                        <x-form.input type="date" name="{{ $field->field_name }}" label="{{ $field->level_name }}"
                            disabled />
                    </div>

                    {{-- SELECT --}}
                    @elseif($field->field_type === 'select')
                    <div>
                        <x-form.select name="{{ $field->field_name }}" label="{{ $field->level_name }}" disabled>
                            <option value="">
                                -- Select {{ $field->level_name }} --
                            </option>
                            @foreach($field->options ?? [] as $opt)
                            <option>{{ $opt }}</option>
                            @endforeach
                        </x-form.select>
                    </div>
                    {{-- TEXTAREA --}}
                    @elseif($field->field_type === 'textarea')
                    <div>
                        <x-form.textarea name="{{ $field->field_name }}" label="{{ $field->level_name }}"
                            placeholder="Enter {{ $field->level_name }}" disabled />
                    </div>
                    @endif

                    @endforeach
                </div>
                @endif
            </div>

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