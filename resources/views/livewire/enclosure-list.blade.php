<div>
    <form wire:submit.prevent="save" enctype="multipart/form-data">
        @foreach ($doc_lists as $doc)
        @php
        $isRequired = $doc->is_required;
        $existingDoc = $existingDocuments[$doc->doc_type_id] ?? null;
        $required = $isRequired; // Always show asterisk if required
        $enforceRequired = $isRequired && !$existingDoc; // Only enforce required input if no file exists
        $label = $doc->codemaster->name;
        @endphp

        <x-form.inputfile
            name="documents[{{ $doc->codemaster->id }}]"
            label="{{ $label }}"
            :required="$required"
            wire:model="documents.{{ $doc->codemaster->id }}"
            accept="{{ str_replace([';', '|', ' '], ',', $doc->extension_type) }}"
            maxSize="{{ $doc->max_file_size }}"
            :disabled="false" />
        @if ($existingDoc)
        <div>
            <x-button.danger wire:click="downloadDocument({{ $existingDoc->id }})">
                Download
            </x-button.danger>
        </div>
        @endif
        @endforeach
        @if ($mode != '0')
        <x-button.danger type="button">
            Previous
        </x-button.danger>
        @endif
        <x-button.danger type="submit">
            {{ $mode == '0' ? 'Save' : 'Save & Next' }}
        </x-button.danger>
    </form>
</div>