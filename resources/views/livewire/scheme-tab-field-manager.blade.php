<div>
    <div class="max-w-7xl mx-auto bg-white rounded-xl shadow p-6 space-y-6 mb-2">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-bold text-indigo-800 dark:text-white">
                Scheme Tab Field Manager
            </h1>
            <x-form.back-button :url="route('master-tab')" />
        </div>
    </div>

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
            <div x-data="{ open:false }" class="border border-cyan-300 rounded-lg">
                <div class="flex justify-between items-center bg-gray-100 px-4 py-3 rounded-t-lg">
                    <span class="font-semibold text-slate-800">
                        {{ $tab->position }}. {{ $tab->masterTab->tab_name }}
                    </span>
                    <div class="flex gap-2 items-center">
                        <x-dropdown>
                            <x-slot name="trigger">
                                <button type="button"
                                    class="inline-flex items-center px-4 py-2.5 
                                            bg-gradient-to-r from-indigo-50 to-indigo-100 
                                            hover:from-indigo-100 hover:to-indigo-200 
                                            dark:from-indigo-900/40 dark:to-indigo-800/40 
                                            dark:hover:from-indigo-800/60 dark:hover:to-indigo-700/60
                                            text-indigo-700 dark:text-indigo-300 
                                            text-sm font-semibold 
                                            rounded-xl transition-all duration-200 
                                            shadow-sm hover:shadow-md 
                                            border border-indigo-200 dark:border-indigo-700
                                            focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                                    <span>Actions</span>
                                    <svg class="ml-2 -mr-1 h-4 w-4 transition-transform duration-200"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="2.5"
                                        stroke="currentColor"
                                        x-bind:class="{ 'rotate-180': open }">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </button>
                            </x-slot>

                            <x-slot name="content">

                                @if(!$isFinalSubmitted)
                                <x-dropdown-link wire:click="openManageModal({{ $tab->tab_code }})">
                                    <div class="flex items-center">
                                        <div class="w-7 h-7 rounded-lg bg-green-100 dark:bg-green-900/40 flex items-center justify-center mr-3 group-hover:bg-green-200 dark:group-hover:bg-green-800/60 transition-colors">
                                            <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </div>
                                        <span class="font-medium">Clone Fields</span>
                                    </div>
                                </x-dropdown-link>
                                @endif
                                @if(!in_array($tab->tab_code, [104, 105]) && !$isFinalSubmitted)
                                <x-dropdown-link
                                    :href="route('create-dynamicformfield', [
        'scheme_id' => Crypt::encryptString($tab->scheme_id),
        'tab_code' => Crypt::encryptString($tab->tab_code)
    ])">
                                    <div class="flex items-center">
                                        <div class="w-7 h-7 rounded-lg bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center mr-3 group-hover:bg-indigo-200 dark:group-hover:bg-indigo-800/60 transition-colors">
                                            <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                            </svg>
                                        </div>
                                        <span class="font-medium">Add More Fields</span>
                                    </div>
                                </x-dropdown-link>
                                @endif

                                @if(!in_array($tab->tab_code, [104]) && !$isFinalSubmitted)
                                <x-dropdown-link wire:click="openLayoutModal({{ $tab->tab_code }})">
                                    <div class="flex items-center">
                                        <div class="w-7 h-7 rounded-lg bg-amber-100 dark:bg-amber-900/40 flex items-center justify-center mr-3 group-hover:bg-amber-200 dark:group-hover:bg-amber-800/60 transition-colors">
                                            <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                                            </svg>
                                        </div>
                                        <span class="font-medium">Modify Form Layout</span>
                                    </div>
                                </x-dropdown-link>
                                @endif

                                @if($tab->showValidationButton() && !$isFinalSubmitted)
                                <x-dropdown-link
                                    :href="route('edit-validation', ['ref' => Crypt::encryptString($tab->scheme_id.'|'.$tab->tab_code)])">
                                    <div class="flex items-center">
                                        <div class="w-7 h-7 rounded-lg bg-cyan-100 dark:bg-cyan-900/40 flex items-center justify-center mr-3 group-hover:bg-cyan-200 dark:group-hover:bg-cyan-800/60 transition-colors">
                                            <svg class="w-4 h-4 text-cyan-600 dark:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <span class="font-medium">Modify Default Validation</span>
                                    </div>
                                </x-dropdown-link>
                                @endif

                                <x-dropdown-link wire:click="openPreview({{ $tab->tab_code }})">
                                    <div class="flex items-center">
                                        <div class="w-7 h-7 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center mr-3 group-hover:bg-gray-200 dark:group-hover:bg-gray-600 transition-colors">
                                            <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </div>
                                        <span class="font-medium">Preview</span>
                                    </div>
                                </x-dropdown-link>
                            </x-slot>
                        </x-dropdown>

                        <button @click="open = !open"
                            class="p-2.5 rounded-xl bg-gradient-to-br from-gray-50 to-gray-100 hover:from-indigo-50 hover:to-indigo-100
                            dark:from-gray-800 dark:to-gray-900 
                            dark:hover:from-indigo-900/40 dark:hover:to-indigo-800/40
                            text-gray-600 hover:text-indigo-600 
                            dark:text-gray-400 dark:hover:text-indigo-400
                            transition-all duration-200 
                            border border-gray-200 dark:border-gray-700
                            shadow-sm hover:shadow-md">

                            <svg class="w-4 h-4 transition-transform duration-200"
                                :class="open ? 'rotate-180' : ''"
                                fill="currentColor"
                                viewBox="0 0 24 24">
                                <path d="M6 12a2 2 0 11-4 0 2 2 0 014 0zM14 12a2 2 0 11-4 0 2 2 0 014 0zM22 12a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </button>
                    </div>
                </div>
                {{-- Body --}}
                <div x-show="open" x-collapse class="bg-white rounded-b-lg overflow-hidden">
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
                     @if(!$isFinalSubmitted)
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
                        @endif
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

                            <span
                                class="drag-handle text-gray-400 text-lg
                                 {{ $isFinalSubmitted ? 'opacity-40 cursor-not-allowed' : 'cursor-move' }}">
                                ☰
                            </span>
                            <span class="flex-1 text-gray-700 font-medium">
                                {{ $loop->iteration }}. {{ $row['field']->level_name }}
                            </span>

                            <button
                                wire:click="editSelfDeclarationField({{ $row['field']->id }})"
                                @if($isFinalSubmitted) disabled @endif
                                class="text-indigo-600 font-bold text-lg mr-2
                                    {{ $isFinalSubmitted ? 'opacity-50 cursor-not-allowed' : 'hover:text-indigo-800' }}">
                                ✎
                            </button>

                            <button
                                wire:click="removeSelfDeclarationField({{ $row['field']->id }})"
                                @if($isFinalSubmitted) disabled @endif
                                class="text-red-500 font-bold text-lg mr-2
                                {{ $isFinalSubmitted ? 'opacity-50 cursor-not-allowed' : 'hover:text-red-600' }}">
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
                    <div class="grid grid-cols-3 gap-3 p-4" x-data x-init="
                                        @if(!$isFinalSubmitted)
                                            new Sortable($el, {
                                                animation: 150,
                                                handle: '.drag-handle',
                                                onEnd() {
                                                    let ordered = Array.from($el.children)
                                                        .map(el => el.dataset.fid);
                                                    $wire.updateFieldOrder({{ $tab->tab_code }}, ordered);
                                                }
                                            })
                                        @endif
                                        ">
                        @foreach($tabFields[$tab->tab_code] as $fid => $fname)
                        <div data-fid="{{ $fid }}"
                            class="flex items-center justify-between bg-gray-50 border border-gray-200 rounded p-3">
                            <div class="flex items-center gap-2">
                                <span
                                    class="drag-handle text-gray-400 text-lg
                                 {{ $isFinalSubmitted ? 'opacity-40 cursor-not-allowed' : 'cursor-move' }}">
                                    ☰
                                </span>
                                <span class="font-semibold text-gray-600">
                                    {{ $loop->iteration }}. {{ $fname }}
                                </span>
                            </div>
                            <button
                                wire:click.stop="removeField({{ $tab->tab_code }}, '{{ $fid }}')"
                                @if($this->isFinalSubmitted || $this->isFieldMandatory($fid)) disabled @endif
                                class="text-red-500 font-bold text-lg
                                {{ ($this->isFinalSubmitted || $this->isFieldMandatory($fid))
                                    ? 'opacity-50 cursor-not-allowed'
                                    : 'hover:text-red-600' }}">
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
            <x-button.primary wire:click="openDigitalPreview" class="bg-blue-600 hover:bg-blue-700 rounded-xl">
                Digital Preview
            </x-button.primary>
            <x-button.primary wire:click="openFinalPreview" class="bg-blue-600 hover:bg-blue-700 rounded-xl">
                Form Preview
            </x-button.primary>
            @if(!$isFinalSubmitted)
            <x-form.confirm-action
                action="finalSubmit"
                title="Final Submit"
                message="Once submitted, this scheme cannot be edited."
                confirmLabel="Yes, Submit">
                Final Submit
            </x-form.confirm-action>
            @else
            <div class="inline-flex items-center gap-2.5 px-4 py-2 bg-white rounded-lg border border-green-200 shadow-sm">
                <!-- Circular icon background -->
                <div class="w-6 h-6 rounded-full bg-green-100 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                </div>

                <span class="text-green-700 font-semibold text-sm">
                    Finaly Submitted
                </span>
            </div>
            @endif

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
                <livewire:section-level-modal :schemeId="$schemeId" />

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

        <div class="fixed inset-0 z-50 bg-black/75 flex items-center justify-center">
            <div class="bg-white rounded-xl shadow-lg w-3xl max-w-auto max-h-[90vh] flex flex-col overflow-hidden">

                {{-- HEADER --}}
                <div class="flex items-center justify-between px-6 py-4 border-b bg-green-100 shrink-0">
                    <div class="bg-green-100 px-6 py-4 text-center font-semibold">
                        {{ $previewTabName }}
                    </div>
                    <button wire:click="closePreview"
                        class="text-gray-400 hover:text-gray-600 text-xl">
                        ✕
                    </button>
                </div>

                {{-- CONTENT --}}
                <div class="p-6 max-h-[70vh] overflow-y-auto">
                    {{-- ================= TAB 104 : ENCLOSURE ================= --}}
                    @if($previewTabCode == 104)
                    <livewire:enclosure-list
                        :scheme_id="$schemeId"
                        :form_preview="1"
                        :tabCode="$previewTabCode" />

                    {{-- ================= TAB 105 : SELF DECLARATION ================= --}}
                    @elseif($previewTabCode == 105)

                    @if(empty($selfDeclarationPreviewRows))
                    <div class="text-center text-gray-400 py-10">
                        No self declaration fields configured
                    </div>
                    @else
                    <div class="space-y-6">

                        @foreach($selfDeclarationPreviewRows as $row)

                        {{-- SECTION HEADER --}}
                        @if($row['type'] === 'header')
                        <div class="mt-6 mb-2 px-3 py-2 bg-indigo-50
                                            border-l-4 border-indigo-600 rounded">
                            <span class="font-semibold text-indigo-700">
                                {{ $row['title'] }}
                            </span>
                        </div>

                        {{-- SINGLE FIELD --}}
                        @elseif($row['type'] === 'field')
                        <div class="{{ $row['width_class'] }}">
                            @include('partials.preview-field', [
                            'field' => $row['field']
                            ])
                        </div>

                        {{-- GRID ROW --}}
                        @elseif($row['type'] === 'grid')
                        <div class="grid grid-cols-1 md:grid-cols-{{ $row['cols'] }} gap-5">
                            @foreach($row['fields'] as $field)
                            <div>
                                @include('partials.preview-field', [
                                'field' => $field
                                ])
                            </div>
                            @endforeach
                        </div>
                        @endif

                        @endforeach

                    </div>
                    @endif

                    @else
                    @php
                    $layoutJson = $this->getTabLayout($previewTabCode);
                    $layout = $layoutJson ? json_decode($layoutJson, true) : [];
                    $orderedFields = $PreviewFields->values();
                    // dd($orderedFields);
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
                            @case('email')
                            <x-form.input type="{{ $field->field_type }}" name="{{ $field->field_name }}" label="{!! $field->level_name !!}" placeholder="Enter {{ $field->level_name }}" disabled />
                            @break
                            @case('number')
                            <x-form.input type="number" name="{{ $field->field_name }}" label="{{ $field->level_name }}" placeholder="Enter {{ $field->level_name }}" disabled />
                            @break
                            @case('date')
                            <x-form.input type="date" name="{{ $field->field_name }}" label="{{ $field->level_name }}" placeholder="Enter {{ $field->level_name }}" disabled />
                            @break
                            @case('textarea')
                            <x-form.textarea name="{{ $field->field_name }}" label="{{ $field->level_name }}" placeholder="Enter {{ $field->level_name }}" disabled />
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
                    @if($cursor < $total)
                        @foreach($orderedFields->slice($cursor) as $field)
                        <div class="grid md:grid-cols-1 gap-4 mb-4">
                            <div>
                                @switch($field->field_type)
                                @case('text')
                                @case('email')
                                <x-form.input type="{{ $field->field_type }}" name="{{ $field->field_name }}" label="{!! $field->level_name !!}" placeholder="Enter {{ $field->level_name }}" disabled />
                                @break
                                @case('number')
                                <x-form.input type="number" name="{{ $field->field_name }}" label="{{ $field->level_name }}" placeholder="Enter {{ $field->level_name }}" disabled />
                                @break
                                @case('date')
                                <x-form.input type="date" name="{{ $field->field_name }}" label="{{ $field->level_name }}" placeholder="Enter {{ $field->level_name }}" disabled />
                                @break
                                @case('textarea')
                                <x-form.textarea name="{{ $field->field_name }}" label="{{ $field->level_name }}" placeholder="Enter {{ $field->level_name }}" disabled />
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
                        @if($PreviewFields->isEmpty())
                        <div class="text-center text-gray-400">
                            No fields configured for this tab
                        </div>
                        @else
                        <div class="grid md:grid-cols-2 gap-4">
                            @foreach($PreviewFields as $field)
                            <div>
                                @switch($field->field_type)
                                @case('text')
                                @case('email')
                                <x-form.input type="{{ $field->field_type }}" name="{{ $field->field_name }}" label="{!! $field->level_name !!}" disabled />
                                @break
                                @case('number')
                                <x-form.input type="number" name="{{ $field->field_name }}" label="{{ $field->level_name }}" placeholder="Enter {{ $field->level_name }}" disabled />
                                @break
                                @case('date')
                                <x-form.input type="date" name="{{ $field->field_name }}" label="{{ $field->level_name }}" placeholder="Enter {{ $field->level_name }}" disabled />
                                @break
                                @case('textarea')
                                <x-form.textarea name="{{ $field->field_name }}" label="{{ $field->level_name }}" placeholder="Enter {{ $field->level_name }}" disabled />
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
                <div class="flex justify-end gap-3 px-6 py-4 border-t">
                    <x-button.primary wire:click="closePreview">
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
                    <button wire:click="closeFinalPreview"
                        class="text-gray-400 hover:text-gray-600 text-xl">
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
                <div class="p-6 max-h-[70vh] overflow-y-auto">

                    @if($finalActiveTabCode == 104)
                    <livewire:enclosure-list
                        :scheme_id="$schemeId"
                        :form_preview="1"
                        :tabCode="$finalActiveTabCode" />

                    @elseif($finalActiveTabCode == 105)
                    @if(empty($selfDeclarationPreviewRows))
                    <div class="text-center text-gray-400">
                        No self declaration fields configured
                    </div>
                    @else
                    <div class="space-y-6">
                        @foreach($selfDeclarationPreviewRows as $row)

                        @if($row['type'] === 'header')
                        <div class="mt-6 mb-2 px-3 py-2 bg-indigo-50 border-l-4 border-indigo-600 rounded">
                            <span class="font-semibold text-indigo-700">
                                {{ $row['title'] }}
                            </span>
                        </div>

                        @elseif($row['type'] === 'field')
                        <div class="{{ $row['width_class'] }}">
                            @include('partials.preview-field', ['field' => $row['field']])
                        </div>
                        @elseif($row['type'] === 'grid')
                        <div class="grid grid-cols-1 md:grid-cols-{{ $row['cols'] }} gap-5">
                            @foreach($row['fields'] as $field)
                            <div>
                                @include('partials.preview-field', ['field' => $field])
                            </div>
                            @endforeach
                        </div>
                        @endif
                        @endforeach
                    </div>
                    @endif
                    @else
                    @php
                    $layoutJson = $this->getTabLayout($finalActiveTabCode);
                    $layout = $layoutJson ? json_decode($layoutJson, true) : [];
                    $orderedFields = $finalPreviewFields->values();
                    $cursor = 0;
                    $total = $orderedFields->count();
                    @endphp
                    @if(!empty($layout))
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
                            @case('email')
                            <x-form.input type="{{ $field->field_type }}" name="{{ $field->field_name }}" label="{!! $field->level_name !!}" placeholder="Enter {{ $field->level_name }}" disabled />
                            @break
                            @case('number')
                            <x-form.input type="number" name="{{ $field->field_name }}" label="{{ $field->level_name }}" placeholder="Enter {{ $field->level_name }}" disabled />
                            @break
                            @case('date')
                            <x-form.input type="date" name="{{ $field->field_name }}" label="{{ $field->level_name }}" placeholder="Enter {{ $field->level_name }}" disabled />
                            @break
                            @case('textarea')
                            <x-form.textarea name="{{ $field->field_name }}" label="{{ $field->level_name }}" placeholder="Enter {{ $field->level_name }}" disabled />
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
                    @if($cursor < $total)
                        @foreach($orderedFields->slice($cursor) as $field)
                        <div class="grid md:grid-cols-1 gap-4 mb-4">
                            <div>
                                @switch($field->field_type)
                                @case('text')
                                @case('email')
                                <x-form.input type="{{ $field->field_type }}" name="{{ $field->field_name }}" label="{!! $field->level_name !!}" placeholder="Enter {{ $field->level_name }}" disabled />
                                @break
                                @case('number')
                                <x-form.input type="number" name="{{ $field->field_name }}" label="{{ $field->level_name }}" placeholder="Enter {{ $field->level_name }}" disabled />
                                @break
                                @case('date')
                                <x-form.input type="date" name="{{ $field->field_name }}" label="{{ $field->level_name }}" placeholder="Enter {{ $field->level_name }}" disabled />
                                @break
                                @case('textarea')
                                <x-form.textarea name="{{ $field->field_name }}" label="{{ $field->level_name }}" placeholder="Enter {{ $field->level_name }}" disabled />
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
                                @case('email')
                                <x-form.input type="{{ $field->field_type }}" name="{{ $field->field_name }}" label="{!! $field->level_name !!}" placeholder="Enter {{ $field->level_name }}" disabled />
                                @break
                                @case('number')
                                <x-form.input type="number" name="{{ $field->field_name }}" label="{{ $field->level_name }}" placeholder="Enter {{ $field->level_name }}" disabled />
                                @break
                                @case('date')
                                <x-form.input type="date" name="{{ $field->field_name }}" label="{{ $field->level_name }}" placeholder="Enter {{ $field->level_name }}" disabled />
                                @break
                                @case('textarea')
                                <x-form.textarea name="{{ $field->field_name }}" label="{{ $field->level_name }}" placeholder="Enter {{ $field->level_name }}" disabled />
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
                <div class="flex justify-end gap-3 px-6 py-4 border-t">
                    <x-button.primary wire:click="closeFinalPreview">
                        Close
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

        @if($showLayoutModal)
        <div class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
            <div class="bg-white w-full max-w-lg rounded-xl flex flex-col max-h-[90vh] overflow-hidden shadow-2xl">
                <!-- Header -->
                <div class="px-6 py-4 border-b shrink-0 bg-gray-50/50">
                    <h3 class="font-bold text-lg text-gray-800">Form Layout Settings</h3>
                </div>

                <!-- Body -->
                <div class="p-6 overflow-y-auto space-y-5 flex-1 custom-scrollbar">
                    <x-form.select label="Layout Mode" wire:model.live="layoutMode">
                        <option value="1">1 field per row</option>
                        <option value="2">2 fields per row</option>
                        <option value="3">3 fields per row</option>
                        <option value="custom">Custom</option>
                    </x-form.select>

                    @if($layoutMode === 'custom')
                    @php $visibleRows = $this->visibleRowCount(); @endphp
                    <div class="space-y-3 bg-indigo-50/30 p-4 rounded-xl border border-indigo-100">
                        <h4 class="text-sm font-semibold text-indigo-700 mb-2">Configure Rows</h4>
                        @foreach($rowConfig as $i => $cnt)
                        @if($i < $visibleRows)
                            <div class="flex items-center justify-between bg-white p-3 rounded-lg border border-gray-100 shadow-sm">
                            <span class="text-sm font-medium text-gray-600">Row {{ $i + 1 }}</span>
                            <div class="flex items-center gap-2">
                                <select wire:model.live="rowConfig.{{ $i }}"
                                    class="border-gray-200 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500 py-1 pl-3 pr-8">
                                    <option value="1">1 field</option>
                                    <option value="2">2 fields</option>
                                    <option value="3">3 fields</option>
                                </select>
                            </div>
                    </div>
                    @endif
                    @endforeach
                </div>
                @endif
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 border-t flex justify-end gap-3 bg-gray-50/50 shrink-0">
                <x-button.primary
                    wire:click="$set('showLayoutModal', false)"
                    class="bg-gray-100 !text-gray-700 hover:bg-gray-200 border-none shadow-none">
                    Cancel
                </x-button.primary>
                <x-button.primary
                    wire:click="applyLayout"
                    class="bg-indigo-600 hover:bg-indigo-700 shadow-indigo-200">
                    Apply Layout
                </x-button.primary>
            </div>
        </div>
    </div>
    @endif

    @if($showDigitalPreview)
    <div class="fixed inset-0 z-50 bg-black/75 flex items-center justify-center">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-5xl max-h-[90vh] flex flex-col overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b">
                <h3 class="text-lg font-semibold text-gray-800">
                    Digital Preview
                </h3>
                <button wire:click="closeDigitalPreview"
                    class="text-gray-400 hover:text-gray-600 text-xl">✕</button>
            </div>
            <div class="p-6 overflow-y-auto space-y-10">
                @foreach($tabs as $tab)
                <div>
                    <h2 class="text-xl font-semibold text-indigo-700 border-b pb-2">
                        {{ $tab->masterTab->tab_name }}
                    </h2>
                </div>
                @if($tab->tab_code == 104)
                <livewire:enclosure-list
                    :scheme_id="$schemeId"
                    :form_preview="1"
                    :tabCode="$tab->tab_code" />

                @elseif($tab->tab_code == 105)
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
</div>