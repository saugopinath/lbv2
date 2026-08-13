@props(['docId', 'docName' => null, 'buttonText' => null])

@php
    if (empty($docName) || empty($buttonText)) {
        $jsonPath = public_path('js/document-master.json');
        if (file_exists($jsonPath)) {
            $docsArray = json_decode(file_get_contents($jsonPath), true) ?: [];
            foreach ($docsArray as $doc) {
                if (isset($doc['doc_type_id']) && $doc['doc_type_id'] == $docId) {
                    if (empty($docName)) {
                        $docName = $doc['doc_name'];
                    }
                    if (empty($buttonText) && isset($doc['button_text'])) {
                        $buttonText = $doc['button_text'];
                    }
                    break;
                }
            }
        }
    }
    $docName = $docName ?? 'Document';
    $buttonText = $buttonText ?? 'Upload Document / ডকুমেন্ট আপলোড করুন';
@endphp

<div class="mt-2 flex items-center justify-between" x-data>
    <button type="button"
        @click="$dispatch('open-upload-modal', {docId: {{ $docId }}, docName: '{{ addslashes($docName) }}'})"
        class="hover:bg-amber-800 text-white font-bold px-6 py-2.5 rounded shadow transition text-sm flex items-center gap-1 uppercase tracking-wider bg-amber-700">
        {{ $buttonText }}
    </button>

    <div x-show="$wire.uploadedDocuments[{{ $docId }}]" class="flex items-center gap-2">
        <span class="text-xs text-green-600 font-bold">✓ Uploaded</span>
        <button type="button" wire:click="downloadDocument($wire.uploadedDocuments[{{ $docId }}])"
            class="text-blue-600 underline text-xs font-medium">Download</button>
    </div>
</div>
