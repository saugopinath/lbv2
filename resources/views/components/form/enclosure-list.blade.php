@props([
    'name',
    'isRequired' => false,
    'docId',
    'existingDocumentId' => null,
])

<div class="backdrop-blur-md bg-white/80 rounded-lg p-4 shadow-sm flex flex-col justify-between">
    <!-- Document Name -->
    <span class="text-gray-800 font-medium mb-2">
        {{ $name }}
        @if($isRequired)
            <span class="text-red-500">*</span>
        @endif
    </span>

    <!-- Actions -->
    <div class="flex space-x-2">
        <!-- Upload Button -->
        <button
            @click="showUploadModal = true; currentDocId = {{ $docId }}; currentDocName = '{{ $name }}'"
            class="bg-blue-600 text-white px-4 py-1 rounded hover:bg-blue-700 transition">
            Upload
        </button>

        <!-- Download Button -->
        @if($existingDocumentId)
            <button
                @click="$wire.downloadDocument({{ $existingDocumentId }})"
                class="bg-green-600 text-white px-4 py-1 rounded hover:bg-green-700 transition">
                Download
            </button>
        @endif
    </div>
</div>
