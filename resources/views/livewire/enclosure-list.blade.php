<div>
    @foreach ($doc_lists as $doc)
    <div>
        <x-form.inputfile
            name="documents{{ $doc->codemaster->short_name }}"
            label="{{ $doc->codemaster->name }}"
            required="{{ $doc->is_required }}"
            wire:model="documents.{{ $doc->codemaster->id }}"
            accept="{{ str_replace([';', '|', ' '], ',', $doc->extension_type) }}"
            maxSize="{{ $doc->max_file_size }}" />
        <x-button.danger type="button" wire:click="saveSingleDocument({{ $doc->codemaster->id }})">
            Upload
        </x-button.danger>
        @if (isset($existingDocuments[$doc->codemaster->id]))
        <x-button.danger type="button" wire:click="downloadDocument({{ $existingDocuments[$doc->codemaster->id]->id }})">
            Download
        </x-button.danger>
        @endif
    </div>
    @endforeach
</div>