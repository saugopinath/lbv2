{{--

<div x-data="{ modalOpen: false, modalSrc: '' }" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    @foreach ($decryptedEncloser as $doc)
        <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
            <p class="text-sm font-semibold text-gray-800">
                {{ $doc->documents->name ?? 'Document' }}
            </p>
            <button @click="modalSrc = '{{ $doc->attched_document }}'; modalOpen = true"
                class="mt-2 px-3 py-1 bg-blue-500 text-white text-xs rounded hover:bg-blue-600">
                View
            </button>
        </div>
    @endforeach

    <div x-show="modalOpen" x-cloak x-transition.opacity
        class="fixed inset-0 bg-gray-200 bg-opacity-10 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-3xl p-4 relative">
            <button @click="modalOpen = false"
                class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 text-2xl font-bold">&times;</button>
            <iframe :src="modalSrc" class="w-full h-[70vh] rounded" frameborder="0"></iframe>
        </div>
    </div>
</div>  --}}
<div x-data="{
    modalOpen: false,
    modalSrc: '',
    modalAppId: '',
    modalDocName: '',
}" class="relative">
    <!-- Document Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach ($decryptedEncloser as $doc)
            <div class="bg-white p-3 rounded-lg shadow hover:shadow-md transition">
                <p class="text-sm font-semibold text-gray-800">
                    {{ $doc->documents->name ?? 'Document' }}
                </p>
                <button
                    @click="
                        modalSrc = '{{ $doc->attched_document }}';
                        modalAppId = '{{ $doc->application_id ?? '' }}';
                        modalDocName = '{{ $doc->documents->name ?? 'Document' }}';
                        modalOpen = true;
                    "
                    class="mt-2 px-3 py-1 bg-blue-500 text-white text-xs rounded hover:bg-blue-600">
                    View
                </button>
            </div>
        @endforeach
    </div>

    <!-- Popup iframe -->
<div x-show="modalOpen" x-cloak x-transition class="fixed inset-0 z-50 flex items-center justify-center">

    <!-- Just semi-transparent overlay (no blur) -->
    <div class="absolute inset-0 bg-black/30" @click="modalOpen = false"></div>

    <div class="bg-white rounded-lg p-6 shadow-md w-full max-w-3xl relative border z-10">
        <button @click="modalOpen = false"
            class="absolute top-2 right-2 text-gray-500 hover:text-red-500 text-xl font-bold">&times;</button>

        <h2 class="text-lg font-semibold mb-3">
            <span x-text="modalDocName"></span>
            (Application ID : <span x-text="modalAppId"></span>)
        </h2>

        <iframe :src="modalSrc" class="w-full h-[70vh] rounded bg-white" frameborder="0"></iframe>
    </div>
</div>

</div>

