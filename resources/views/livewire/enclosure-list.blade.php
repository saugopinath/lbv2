<div>
    <form wire:submit.prevent="save" enctype="multipart/form-data">
        @foreach ($doc_lists as $doc)
        <x-form.inputfile
            type="file"
            name="documents[{{ $doc->codemaster->short_name }}]"
            label="{{ $doc->codemaster->name }}"
            wire:model="documents.{{ $doc->codemaster->short_name }}"
            :required="$doc->is_required" accept="{{ $doc->extension_type }}"
            maxSize="{{ $doc->max_file_size }}" />
        @endforeach
        @if ($mode != '0')
        <x-button.danger>Previous</x-button.danger>
        @endif
        <x-button.danger type="submit">
            {{ $mode == '0' ? 'Save' : 'Save & Next' }}
        </x-button.danger>
    </form>
</div>