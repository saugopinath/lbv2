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
                    @if(!in_array($tab->tab_code, [104, 105]))
                    <a href="{{ route('create-dynamicformfield', [
                    'scheme_id' => Crypt::encryptString($tab->scheme_id), 'tab_code' => Crypt::encryptString($tab->tab_code)
                ]) }}">

                        <x-button.primary class="bg-indigo-400 hover:bg-indigo-500 text-sm">
                            Add Another Fields
                        </x-button.primary>
                    </a>
                    @endif


                    @if(!$isFinalSubmitted)
                    <x-button.primary wire:click="openManageModal({{ $tab->tab_code }})"
                        class="bg-green-600 hover:bg-green-700 rounded-xl text-sm">
                        Manage Fields
                    </x-button.primary>
                    @endif
                    @if(!$isFinalSubmitted)
                    <x-button.primary wire:click="openLayoutModal({{ $tab->tab_code }})"
                        class="bg-green-600 hover:bg-green-700 rounded-xl text-sm">
                        Fix Form Layout
                    </x-button.primary>
                    @endif

                    @if($tab->showValidationButton())
                    <a href="{{ route('edit-validation', [
        'ref' => Crypt::encryptString($tab->scheme_id.'|'.$tab->tab_code)
    ]) }}">
                        <x-button.primary
                            type="button"
                            class="bg-yellow-400 hover:bg-yellow-500 text-sm">
                            Reset Validation
                        </x-button.primary>
                    </a>
                    @endif

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
                @if($tab->tab_code == 104)
                @if(count($attachedDocuments))
                <div class="grid grid-cols-2 gap-3 p-4" x-data x-init="
                            new Sortable($el, {
                                animation: 150,
                                handle: '.drag-handle',
                                onEnd() {
                                    let ordered = Array.from($el.children)
                                        .map(el => el.dataset.id);
                                    $wire.updateDocumentOrder(ordered);
                                }
                            })
                            ">
                    @foreach($attachedDocuments as $doc)
                    <div data-id="{{ $doc->id }}"
                        class="flex items-center justify-between bg-gray-50 border border-gray-200 rounded p-3">
                        {{-- LEFT --}}
                        <div class="flex items-center gap-3">
                            <span class="drag-handle cursor-move text-gray-400 text-lg">☰</span>
                            <div>
                                <div class="font-medium">
                                    {{ $loop->iteration }}. {{ $doc->docType->name }}
                                    @if($doc->is_required)
                                    <span class="text-red-600 font-bold ml-1">*</span>
                                    @endif
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{-- Size: {{ $doc->max_file_size }} |
                                    Required: {{ $doc->is_required ? 'Yes' : 'No' }} |
                                    Ext: {{ $doc->extension_type }} --}}
                                </div>
                            </div>
                        </div>
                        {{-- REMOVE --}}
                        <button wire:click="removeDocument({{ $doc->id }})"
                            class="text-red-500 font-bold text-lg hover:text-red-600">
                            ✕
                        </button>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="p-4 text-gray-400 text-sm text-center">
                    No documents attached
                </div>
                @endif
                @elseif($tab->tab_code == 105)
                @if(!empty($selfDeclarationDisplay))
                <div class="grid grid-cols-2 gap-3 p-4"
                    x-data
                    x-init="
                            new Sortable($el, {
                                animation: 150,
                                handle: '.drag-handle',
                                ghostClass: 'bg-indigo-100',
                                onEnd() {

                                    let rows = Array.from(
                                        $el.querySelectorAll('[data-field-id]')
                                    ).map(el => {

                                        let section = null;
                                        let prev = el.previousElementSibling;

                                        while (prev) {

                                            if (prev.dataset.sectionBreak) break;

                                            if (prev.dataset.sectionKey) {
                                                section = prev.dataset.sectionKey;
                                                break;
                                            }

                                            prev = prev.previousElementSibling;
                                        }

                                        return {
                                            id: el.dataset.fieldId,
                                            section: section
                                        };
                                    });

                                    $wire.updateSelfDeclarationOrderAndSection(rows);
                                }
                            })
                        ">

                    @foreach($selfDeclarationDisplay as $row)

                    {{-- SECTION HEADER --}}
                    @if($row['show_section_start'])
                    <div
                        data-section-key="{{ $row['field']->section_level_type }}-{{ $row['field']->section_level_id }}"
                        class="col-span-2 mt-4 mb-2 px-3 py-2 bg-indigo-50 border-l-4 border-indigo-600 rounded">
                        <span class="font-semibold text-indigo-700">
                            {{ $row['section_title'] }}
                        </span>
                    </div>
                    @endif

                    {{-- FIELD --}}
                    <div
                        data-field-id="{{ $row['field']->id }}"
                        class="pl-6 py-2 flex items-center gap-3 border rounded bg-white
                                    {{ $row['field']->section_level_id ? 'bg-gray-50' : '' }}">

                        <span class="drag-handle cursor-move text-gray-400 text-lg">☰</span>

                        <span class="flex-1 text-gray-700 font-medium">
                            {{ $loop->iteration }}. {{ $row['field']->level_name }}
                        </span>

                        <button
                            wire:click="editSelfDeclarationField({{ $row['field']->id }})"
                            class="text-indigo-600 font-bold text-lg hover:text-indigo-800 mr-2">
                            ✎
                        </button>

                        <button
                            wire:click="removeSelfDeclarationField({{ $row['field']->id }})"
                            class="text-red-500 font-bold text-lg hover:text-red-600 mr-2">
                            ✕
                        </button>
                    </div>

                    {{-- SECTION END --}}
                    @if($row['show_section_end'])
                    <div
                        data-section-break="true"
                        class="col-span-2 border-b border-dashed border-indigo-300 my-3">
                    </div>
                    @endif

                    @endforeach
                </div>
                @else
                <div class="p-4 text-gray-400 text-sm text-center">
                    No self declaration fields added
                </div>
                @endif

                @else
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
                        <div class="flex items-center gap-2">
                            <span class="drag-handle cursor-move text-gray-400">☰</span>
                            <span class="font-semibold text-gray-600">
                                {{ $loop->iteration }}. {{ $fname }}
                            </span>
                        </div>
                        <button wire:click.stop="removeField({{ $tab->tab_code }}, '{{ $fid }}')" class="text-red-500 font-bold text-lg
                                @if($this->isFieldMandatory($fid))
                                    opacity-50 cursor-not-allowed
                                @else
                                    hover:text-red-600
                                @endif" @if($this->isFieldMandatory($fid)) disabled @endif>
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

                @endif
            </div>

        </div>
        @endforeach
    </div>

    {{-- FINAL ACTION BUTTONS --}}
    <div class="flex justify-center gap-4 pt-6">
         <x-button.primary wire:click="openDigitalPreview" class="bg-blue-600 hover:bg-blue-700">
            Digital Preview
        </x-button.primary>

        <x-button.primary wire:click="openFinalPreview" class="bg-blue-600 hover:bg-blue-700">
            Form Preview
        </x-button.primary>

        <x-button.primary wire:click="finalSubmit" class="bg-green-600 hover:bg-green-700">
            Final Submit
        </x-button.primary>
    </div>

    @endif

    @if($showManageModal)
    <div class="fixed inset-0 z-50 bg-black/75 flex items-center justify-center p-4">

        <div class="bg-white rounded-xl shadow-lg w-full max-w-3xl max-h-[90vh]
                        flex flex-col overflow-hidden">

            {{-- HEADER (fixed) --}}
            <div class="bg-indigo-100 px-6 py-4 font-semibold shrink-0 border-b
                     flex items-center justify-between">
                <span>Manage Fields</span>
                @if($activeTabCode == 105)
                <x-button.primary
                    wire:click="$dispatch('openSectionLevelModal')"
                    class="bg-indigo-600 text-sm">
                    Add New Section / Level
                </x-button.primary>
                @endif
            </div>
            <livewire:section-level-modal />

            {{-- BODY (scrollable) --}}
            <div class="p-6 flex-1 overflow-y-auto">
                @if($activeTabCode == 104)
                {{-- DOCUMENT FORM --}}
                <div class="grid grid-cols-2 gap-4 mb-6">

                    <x-form.select name="selectedDocType" label="Document Type" wire:model="selectedDocType">
                        <option value="">-- Select Document --</option>
                        @foreach($docTypes as $doc)
                        <option value="{{ $doc->id }}">{{ $doc->name }}</option>
                        @endforeach
                    </x-form.select>

                    <x-form.select name="isRequired" label="Is Required" wire:model="isRequired">
                        <option value="0">No</option>
                        <option value="1">Yes</option>
                    </x-form.select>

                    <x-form.input
                        name="maxFileSize"
                        label="Max File Size"
                        wire:model.live="maxFileSize"
                        x-data
                        x-on:input="$el.value = $el.value.replace(/[^0-9]/g,'')" />

                    <div class="col-span-2">
                        <label class="font-semibold">Allowed Extensions</label>
                        <div class="flex gap-4 mt-2 flex-wrap">
                            @foreach(['jpg', 'jpeg', 'png', 'pdf'] as $ext)
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="extensionTypes" wire:model="extensionTypes" value="{{ $ext }}">
                                {{ strtoupper($ext) }}
                            </label>
                            @endforeach
                        </div>
                        @error('extensionTypes')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                {{-- ATTACHED DOCUMENT LIST --}}
                @if($attachedDocuments->count())
                <div class="border-t pt-4">
                    <h3 class="font-semibold mb-3">
                        Attached Documents
                    </h3>

                    <div class="grid grid-cols-2 gap-3 p-4">
                        @foreach($attachedDocuments as $doc)
                        <div class="flex justify-between items-center p-3 rounded border border-green-400 bg-green-100">
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
                            <button wire:click="removeDocument({{ $doc->id }})"
                                class="text-red-500 font-bold text-lg hover:text-red-600">
                                ✕
                            </button>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
                @elseif($activeTabCode == 105)
                <livewire:add-self-declerationfield
                    :scheme_id="$schemeId"
                    :tab_code="$activeTabCode" />
                @else
                {{-- OTHER TABS --}}
                <div class="grid grid-cols-2 gap-3">
                    @foreach($modalFields as $field)
                    <label class="flex gap-3 items-center p-3 rounded border
                                                            {{ $field['is_mandatory']
                                ? 'border-green-300 bg-green-50'
                                : 'bg-gray-50 border-gray-200' }}">

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
            {{-- FOOTER (fixed) --}}
            <div class="flex justify-end gap-3 px-6 py-4 border-t shrink-0">

                <x-button.primary wire:click="closeManageModal" class="bg-gray-600">
                    Close
                </x-button.primary>

                @if($activeTabCode == 104)
                <x-button.primary wire:click="saveDocumentMapping" class="bg-indigo-600">
                    Add Document
                </x-button.primary>
                @elseif($activeTabCode == 105)
                <x-button.primary
                    wire:click="$dispatch('submit-self-declaration')"
                    class="bg-indigo-600">
                    Add New
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
                @if ($previewTabCode == 105)
                <div>
                    @foreach($selfDeclarationDisplay as $row)
                    {{-- SECTION START --}}
                    @if($row['show_section_start'])
                    <div class="mt-4 mb-2 px-3 py-2 bg-indigo-50 border-l-4 border-indigo-600 rounded">
                        <span class="font-semibold text-indigo-700">
                            {{ $row['section_title'] }}
                        </span>
                    </div>
                    @endif

                    {{-- FIELD --}}
                    <div class="py-2 bg-white {{ $row['field']->section_level_id ? 'pl-6 bg-gray-50' : 'pl-0' }}">
                        {{-- FIELD TYPE RENDER --}}
                        @switch($row['field']->field_type)
                        {{-- TEXT --}}
                        @case('text')
                        <x-form.input name="{{ $row['field']->level_name }}"
                            placeholder="Enter {{ $row['field']->level_name }}"
                            disabled />
                        @break

                        {{-- DATE --}}
                        @case('date')
                        <x-form.input type="date" name="{{ $row['field']->level_name }}" disabled />
                        @break
                        @case('number')
                        <x-form.input type="number" name="{{ $row['field']->level_name }}"
                            placeholder="Enter {{ $row['field']->level_name }}"
                            disabled />
                        @break

                        {{-- TEXTAREA --}}
                        @case('textarea')
                        <x-form.textarea name="{{ $row['field']->level_name }}"
                            placeholder="Enter {{ $row['field']->level_name }}"
                            disabled />
                        @break
                        {{-- SELECT --}}
                        @case('select')
                        <x-form.select name="{{ $row['field']->level_name }}" disabled>
                            <option value="">
                                -- Select {{ $row['field']->level_name }} --
                            </option>
                            @foreach($row['field']->options ?? [] as $opt)
                            <option>{{ $opt }}</option>
                            @endforeach
                        </x-form.select>
                        @break
                        {{-- RADIO --}}
                        @case('radio')
                        <div class="flex flex-wrap gap-4 mt-1">
                            @foreach($row['field']->options ?? [] as $opt)
                            <label class="flex items-center gap-2 text-gray-700">
                                <input type="radio" disabled />
                                {{ $opt }}
                            </label>
                            @endforeach
                        </div>
                        @break
                        {{-- CHECKBOX --}}
                        @case('checkbox')
                        <label class="flex items-center gap-2 text-gray-700">
                            <input type="checkbox" disabled />
                            {{ $row['field']->level_name }}
                        </label>
                        @break
                        {{-- FALLBACK --}}
                        @default
                        <div class="text-sm text-red-500">
                            Unsupported field type: {{ $row['field']->field_type }}
                        </div>
                        @endswitch
                    </div>
                    {{-- SECTION END --}}
                    @if($row['show_section_end'])
                    <div class="my-3"></div>
                    @endif
                    @endforeach
                </div>
                @endif
                @if($previewTabCode == 104)
                @if($attachedDocuments->count())
                <div class="space-y-3">
                    {{-- <livewire:enclosure-list :form_preview="1" />  --}}
                    <livewire:enclosure-list :scheme_id="$schemeId" :form_preview="1" :tabCode="$previewTabCode" />
                    @endif
                    @endif
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
                        <livewire:filter-lgd-master-entry :login_type="'state_office'" :preview="1" />
                    </div>
                    @endif
                    <div class="grid gap-2 md:grid-cols-2 pl-4 pr-4">
                        @foreach($this->previewFields as $index => $field)
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

                {{-- HEADER --}}
                <div class="flex items-center justify-between px-6 py-4 border-b shrink-0">
                    <h3 class="text-lg font-semibold text-gray-800">
                        Final Preview
                    </h3>
                    <button wire:click="closeFinalPreview"
                        class="text-gray-400 hover:text-gray-600 text-xl">
                        ✕
                    </button>
                </div>

                {{-- TAB NAV --}}
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

                {{-- CONTENT --}}
                <div class="p-6 max-h-[70vh] overflow-y-auto">

                    {{-- ================= TAB 104 : ENCLOSURE ================= --}}
                    @if($finalActiveTabCode == 104)
                    <livewire:enclosure-list
                        :scheme_id="$schemeId"
                        :form_preview="1"
                        :tabCode="$finalActiveTabCode" />

                    {{-- ================= TAB 105 : SELF DECLARATION ================= --}}
                    @elseif($finalActiveTabCode == 105)
                    @if(empty($selfDeclarationDisplay))
                    <div class="text-center text-gray-400">
                        No self declaration fields configured
                    </div>
                    @else
                    <div class="space-y-2">
                        @foreach($selfDeclarationDisplay as $row)

                        {{-- SECTION START --}}
                        @if($row['show_section_start'])
                        <div class="mt-4 mb-2 px-3 py-2 bg-indigo-50 border-l-4 border-indigo-600 rounded">
                            <span class="font-semibold text-indigo-700">
                                {{ $row['section_title'] }}
                            </span>
                        </div>
                        @endif

                        {{-- FIELD --}}
                        <div class="py-2 bg-white {{ $row['field']->section_level_id ? 'pl-6 bg-gray-50' : 'pl-0' }}">
                            {{-- FIELD TYPE RENDER --}}
                            @switch($row['field']->field_type)
                            {{-- TEXT --}}
                            @case('text')
                            <x-form.input name="{{ $row['field']->level_name }}"
                                placeholder="Enter {{ $row['field']->level_name }}"
                                disabled />
                            @break
                            @case('number')
                            <x-form.input type="number" name="{{ $row['field']->level_name }}"
                                placeholder="Enter {{ $row['field']->level_name }}"
                                disabled />
                            @break
                            {{-- DATE --}}
                            @case('date')
                            <x-form.input type="date" name="{{ $row['field']->level_name }}" disabled />
                            @break

                            {{-- TEXTAREA --}}
                            @case('textarea')
                            <x-form.textarea name="{{ $row['field']->level_name }}"
                                placeholder="Enter {{ $row['field']->level_name }}"
                                disabled />
                            @break
                            {{-- SELECT --}}
                            @case('select')
                            <x-form.select name="{{ $row['field']->level_name }}" disabled>
                                <option value="">
                                    -- Select {{ $row['field']->level_name }} --
                                </option>
                                @foreach($row['field']->options ?? [] as $opt)
                                <option>{{ $opt }}</option>
                                @endforeach
                            </x-form.select>
                            @break
                            {{-- RADIO --}}
                            @case('radio')
                            <div class="flex flex-wrap gap-4 mt-1">
                                @foreach($row['field']->options ?? [] as $opt)
                                <label class="flex items-center gap-2 text-gray-700">
                                    <input type="radio" disabled />
                                    {{ $opt }}
                                </label>
                                @endforeach
                            </div>
                            @break
                            {{-- CHECKBOX --}}
                            @case('checkbox')
                            <label class="flex items-center gap-2 text-gray-700">
                                <input type="checkbox" disabled />
                                {{ $row['field']->level_name }}
                            </label>
                            @break
                            {{-- FALLBACK --}}
                            @default
                            <div class="text-sm text-red-500">
                                Unsupported field type: {{ $row['field']->field_type }}
                            </div>
                            @endswitch
                        </div>

                        {{-- SECTION END --}}
                        @if($row['show_section_end'])
                        <div class="my-3"></div>
                        @endif
                        @endforeach
                    </div>
                    @endif

                   {{-- ================= OTHER TABS (LAYOUT AWARE) ================= --}}
                    @else
                    @php
                    $layoutJson = $this->getTabLayout($finalActiveTabCode);
                    $layout = $layoutJson ? json_decode($layoutJson, true) : [];
                    $orderedFields = $finalPreviewFields->values();
                    $cursor = 0;
                    $total = $orderedFields->count();
                    @endphp

                    @if(!empty($layout))

                    {{-- ===== APPLY SAVED LAYOUT ===== --}}
                    @foreach($layout as $row)
                    @php
                    if ($cursor >= $total) break;

                    $cols = max(1, min(3, (int) $row['columns']));
                    $rowFields = $orderedFields->slice($cursor, $cols);
                    $cursor += $rowFields->count();
                    @endphp

                    @if($rowFields->isNotEmpty())
                    <div class="grid md:grid-cols-{{ $cols }} gap-4 mb-4">
                        @foreach($rowFields as $field)
                        <div>
                            @switch($field->field_type)
                            @case('text')
                            <x-form.input name="{{ $field->field_name }}" label="{!! $field->level_name !!}" disabled />
                            @break
                            @case('number')
                            <x-form.input type="number" name="{{ $field->field_name }}" label="{{ $field->level_name }}" disabled />
                            @break
                            @case('date')
                            <x-form.input type="date" name="{{ $field->field_name }}" label="{{ $field->level_name }}" disabled />
                            @break
                            @case('textarea')
                            <x-form.textarea name="{{ $field->field_name }}" label="{{ $field->level_name }}" disabled />
                            @break
                            @case('select')
                            <x-form.select name="{{ $field->field_name }}" label="{{ $field->level_name }}" disabled>
                                <option value="">-- Select {{ $field->level_name }} --</option>
                                @foreach($field->options ?? [] as $opt)
                                <option>{{ $opt }}</option>
                                @endforeach
                            </x-form.select>
                            @break
                            @endswitch
                        </div>
                        @endforeach
                    </div>
                    @endif
                    @endforeach

                    {{-- ===== REMAINING FIELDS (AUTO ROWS) ===== --}}
                    @if($cursor < $total)
                        @foreach($orderedFields->slice($cursor) as $field)
                        <div class="grid md:grid-cols-1 gap-4 mb-4">
                            <div>
                                @switch($field->field_type)
                                @case('text')
                                <x-form.input name="{{ $field->field_name }}" label="{!! $field->level_name !!}" disabled />
                                @break
                                @case('number')
                                <x-form.input type="number" name="{{ $field->field_name }}" label="{{ $field->level_name }}" disabled />
                                @break
                                @case('date')
                                <x-form.input type="date" name="{{ $field->field_name }}" label="{{ $field->level_name }}" disabled />
                                @break
                                @case('textarea')
                                <x-form.textarea name="{{ $field->field_name }}" label="{{ $field->level_name }}" disabled />
                                @break
                                @case('select')
                                <x-form.select name="{{ $field->field_name }}" label="{{ $field->level_name }}" disabled>
                                    <option value="">-- Select {{ $field->level_name }} --</option>
                                    @foreach($field->options ?? [] as $opt)
                                    <option>{{ $opt }}</option>
                                    @endforeach
                                </x-form.select>
                                @break
                                @endswitch
                            </div>
                        </div>
                        @endforeach
                        @endif

                        @else
                        {{-- ===== FALLBACK : NO LAYOUT SAVED ===== --}}
                        @if($finalPreviewFields->isEmpty())
                        <div class="text-center text-gray-400">
                            No fields configured for this tab
                        </div>
                        @else
                        <div class="grid md:grid-cols-2 gap-4">
                            @foreach($finalPreviewFields as $field)
                            <div>
                                @switch($field->field_type)
                                @case('text')
                                <x-form.input name="{{ $field->field_name }}" label="{!! $field->level_name !!}" disabled />
                                @break
                                @case('number')
                                <x-form.input type="number" name="{{ $field->field_name }}" label="{{ $field->level_name }}" disabled />
                                @break
                                @case('date')
                                <x-form.input type="date" name="{{ $field->field_name }}" label="{{ $field->level_name }}" disabled />
                                @break
                                @case('textarea')
                                <x-form.textarea name="{{ $field->field_name }}" label="{{ $field->level_name }}" disabled />
                                @break
                                @case('select')
                                <x-form.select name="{{ $field->field_name }}" label="{{ $field->level_name }}" disabled>
                                    <option value="">-- Select {{ $field->level_name }} --</option>
                                    @foreach($field->options ?? [] as $opt)
                                    <option>{{ $opt }}</option>
                                    @endforeach
                                </x-form.select>
                                @break
                                @endswitch
                            </div>
                            @endforeach
                        </div>
                        @endif
                        @endif
                        @endif

                </div>

                {{-- FOOTER --}}
                <div class="flex justify-end gap-3 px-6 py-4 border-t">
                    <x-button.primary wire:click="closeFinalPreview">
                        Close
                    </x-button.primary>
                </div>

            </div>
        </div>
        @endif

        @if($showLayoutModal)
        <div class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center">
            <div class="bg-white w-full max-w-lg rounded-xl p-6 space-y-4">

                <h3 class="font-semibold text-lg">
                    Form Layout Settings
                </h3>

                <x-form.select label="Layout Mode" wire:model.live="layoutMode">
                    <option value="1">1 field per row</option>
                    <option value="2">2 fields per row</option>
                    <option value="3">3 fields per row</option>
                    <option value="custom">Custom</option>
                </x-form.select>

                @if($layoutMode === 'custom')
                <div class="space-y-2">
                    <p class="text-sm text-gray-600">
                        Total: {{ $totalFields }} |
                        Remaining: {{ $remainingFixFields }}
                    </p>

                    @foreach($rowConfig as $i => $cnt)
                    <div class="flex items-center gap-3">
                        <span class="w-16">Row {{ $i+1 }}</span>
                        <select wire:model.live="rowConfig.{{ $i }}"
                            class="border rounded px-2 py-1">
                            <option selected value="">select number of Field</option>
                            <option value="1">1 field</option>
                            <option value="2">2 fields</option>
                            <option value="3">3 fields</option>
                        </select>
                    </div>
                    @endforeach
                </div>
                @endif

                <div class="flex justify-end gap-3 pt-4">
                    <x-button.primary
                        wire:click="applyLayout"
                        class="bg-green-600">
                        Apply
                    </x-button.primary>

                    <x-button.primary
                        wire:click="$set('showLayoutModal', false)"
                        class="bg-gray-600">
                        Cancel
                    </x-button.primary>
                </div>

            </div>
        </div>
        @endif

        @if($showEditSelfDeclModal)
        <div class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center">
            <div class="bg-white rounded-xl shadow-lg w-full max-w-lg p-6">

                <h3 class="text-lg font-semibold mb-4">
                    Edit Self Declaration Label
                </h3>

                <x-form.textarea
                    name="editingLevelName"
                    label="Field Label"
                    wire:model.defer="editingLevelName"
                    required />

                <div class="flex justify-end gap-3 mt-6">
                    <x-button.primary
                        wire:click="$set('showEditSelfDeclModal', false)"
                        class="bg-gray-600">
                        Cancel
                    </x-button.primary>

                    <x-button.primary
                        wire:click="updateSelfDeclarationField"
                        class="bg-indigo-600">
                        Update
                    </x-button.primary>
                </div>

            </div>
        </div>
        @endif
        @if($showDigitalPreview)
        <div class="fixed inset-0 z-50 bg-black/75 flex items-center justify-center">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-5xl max-h-[90vh] flex flex-col overflow-hidden">

        {{-- HEADER --}}
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h3 class="text-lg font-semibold text-gray-800">
                Digital Preview
            </h3>
            <button wire:click="closeDigitalPreview"
                class="text-gray-400 hover:text-gray-600 text-xl">✕</button>
        </div>

        {{-- BODY --}}
        <div class="p-6 overflow-y-auto space-y-10">

            @foreach($tabs as $tab)

                {{-- TAB TITLE --}}
                <div>
                    <h2 class="text-xl font-semibold text-indigo-700 border-b pb-2">
                        {{ $tab->masterTab->tab_name }}
                    </h2>
                </div>

                {{-- TAB CONTENT --}}
                @if($tab->tab_code == 104)
                    {{-- ENCLOSURE --}}
                    <livewire:enclosure-list
                        :scheme_id="$schemeId"
                        :form_preview="1"
                        :tabCode="$tab->tab_code" />

                @elseif($tab->tab_code == 105)
                    {{-- SELF DECLARATION --}}
                    @foreach($selfDeclarationDisplay as $row)

                        @if($row['show_section_start'])
                            <div class="mt-4 px-3 py-2 bg-indigo-50 border-l-4 border-indigo-600 rounded">
                                <span class="font-semibold text-indigo-700">
                                    {{ $row['section_title'] }}
                                </span>
                            </div>
                        @endif

                        <div class="py-2 {{ $row['field']->section_level_id ? 'pl-6 bg-gray-50' : '' }}">
                            @include('partials.preview-field', [
                                'field' => $row['field']
                            ])
                        </div>

                    @endforeach

                @else
                    {{-- NORMAL TABS --}}
                    <div class="grid md:grid-cols-2 gap-4 mt-4">
                        @foreach($digitalPreviewFields[$tab->tab_code] ?? [] as $field)
                            @include('partials.preview-field', ['field' => $field])
                        @endforeach
                    </div>
                @endif
                @endforeach
                </div>

                <div class="flex justify-end gap-3 px-6 py-4 border-t">
                <x-button.primary
                    wire:click="downloadDigitalPreviewPdf"
                    class="bg-green-600 hover:bg-green-700">
                    Download PDF
                </x-button.primary>

                <x-button.primary
                    wire:click="closeDigitalPreview">
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