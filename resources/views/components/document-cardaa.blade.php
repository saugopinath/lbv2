@props(['docName', 'isRequired', 'docTypeId', 'existingDoc'])
<div class="backdrop-blur-md bg-white/80 rounded-lg p-4 shadow-sm flex flex-col justify-between">
    <span class="text-gray-800 font-medium mb-2">
        {{ $docName }}
        @if($isRequired)
        <span class="text-red-500">*</span>
        @endif
    </span>
    <div class="flex space-x-2">
        <button @click="openModal({{ $docTypeId }}, '{{ $docName }}')"
            class="bg-blue-600 text-white px-4 py-1 rounded hover:bg-blue-700 transition">
            Upload
        </button>
        @if($existingDoc)
        <button wire:click="downloadDocument({{ $existingDoc->id }})"
            class="bg-green-600 text-white px-4 py-1 rounded hover:bg-green-700 transition">
            Download
        </button>
        @endif
    </div>
</div>