
@props(['docName', 'isRequired', 'docTypeId', 'existingDoc', 'xIsDuplicate' => 0, 'showErrors' => false])

<div x-data="{
    modalOpen: false,
    modalSrc: '',
    modalDocName: '',
}" class="relative">

    <!-- Single card -->
    <div class="backdrop-blur-md bg-white/80 rounded-lg p-4 shadow-sm flex flex-col justify-between">
        <span class="text-gray-800 font-medium mb-2">
            {{ $docName }}
            @if ($isRequired)
                <span class="text-red-500">*</span>
            @endif
        </span>

        @php
            $mime = $existingDoc->document_mime_type ?? '';
        @endphp

        @if ($xIsDuplicate == 1)
            @if ($existingDoc && in_array($mime, ['image/jpg', 'image/jpeg', 'image/png']))
                <div class="flex space-x-2">
                    <x-button.primary
                        @click="modalSrc='data:{{ $mime }};base64,{{ $existingDoc->attched_document }}'; modalDocName='{{ $docName }}'; modalOpen=true;"
                        class="bg-blue-600 text-white px-4 py-1 rounded hover:bg-blue-700 transition">
                        View
                    </x-button.primary>
                </div>
            @elseif ($existingDoc && $mime == 'application/pdf')
                <div class="flex space-x-2">
                    <x-button.success wire:click="downloadDocument({{ $existingDoc->id }})"
                        class="bg-green-600 text-white px-4 py-1 rounded hover:bg-green-700 transition">
                        Download
                    </x-button.success>
                </div>
            @endif

        @elseif ($existingDoc)
            @if (in_array($mime, ['image/jpg', 'image/jpeg', 'image/png']))
                <div class="flex space-x-2">
                    <x-button.primary @click="openModal({{ $docTypeId }}, '{{ $docName }}')"
                        class="bg-blue-600 text-white px-4 py-1 rounded hover:bg-blue-700 transition">
                        Upload
                    </x-button.primary>
                    <x-button.primary
                        @click="modalSrc='data:{{ $mime }};base64,{{ $existingDoc->attched_document }}'; modalDocName='{{ $docName }}'; modalOpen=true;"
                        class="bg-blue-600 text-white px-4 py-1 rounded hover:bg-blue-700 transition">
                        View
                    </x-button.primary>
                </div>
            @elseif ($mime == 'application/pdf')
                <div class="flex space-x-2">
                    <x-button.primary @click="openModal({{ $docTypeId }}, '{{ $docName }}')"
                        class="bg-blue-600 text-white px-4 py-1 rounded hover:bg-blue-700 transition">
                        Upload
                    </x-button.primary>
                    <x-button.success wire:click="downloadDocument({{ $existingDoc->id }})"
                        class="bg-green-600 text-white px-4 py-1 rounded hover:bg-green-700 transition">
                        Download
                    </x-button.success>
                </div>
            @else
                <div class="flex space-x-2">
                    <x-button.primary @click="openModal({{ $docTypeId }}, '{{ $docName }}')"
                        class="bg-blue-600 text-white px-4 py-1 rounded hover:bg-blue-700 transition">
                        Upload
                    </x-button.primary>
                </div>
            @endif
        @else
            <div class="flex space-x-2">
                <x-button.primary @click="openModal({{ $docTypeId }}, '{{ $docName }}')"
                    class="bg-blue-600 text-white px-4 py-1 rounded hover:bg-blue-700 transition">
                    Upload
                </x-button.primary>
            </div>
        @endif
         @if ($isRequired && empty($existingDoc) && $showErrors)
            <p class="text-red-500 text-sm mt-2">{{ $docName }} document is required.</p>
        @endif

    </div>

    <!-- popup view modal -->
    <div x-show="modalOpen" x-cloak x-transition class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="absolute inset-0 bg-black/30" @click="modalOpen=false"></div>
        <div class="bg-white p-6 max-w-3xl w-full rounded relative z-10 border shadow">
            <button @click="modalOpen=false"
                class="absolute right-2 top-2 text-gray-500 hover:text-red-500 text-xl">&times;</button>
            <h2 class="text-lg font-semibold mb-3" x-text="modalDocName"></h2>
            <iframe :src="modalSrc" class="w-full h-[70vh]" frameborder="0"></iframe>
        </div>
    </div>
</div>
