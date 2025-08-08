<div
    x-data="{ 
        showUploadModal: false, 
        currentDocId: null, 
        currentDocName: '', 
        currentFilePreview: null,
        currentFileName: '',
        currentFileSize: '',
        currentFileMime: '',
        handleFileChange(event) {
            const file = event.target.files[0];
            if (!file) {
                this.currentFilePreview = null;
                this.currentFileName = '';
                this.currentFileSize = '';
                this.currentFileMime = '';
                return;
            }
            this.currentFileName = file.name;
            this.currentFileSize = (file.size / 1024).toFixed(2) + ' KB';
            this.currentFileMime = file.type || 'Unknown MIME type';

            if (this.currentFileMime === 'image/jpeg' || this.currentFileMime === 'image/png') {
                const reader = new FileReader();
                reader.onload = e => this.currentFilePreview = e.target.result;
                reader.readAsDataURL(file);
            } else {
                this.currentFilePreview = null;
            }
        },
        openModal(docId, docName) {
            this.showUploadModal = true;
            this.currentDocId = docId;
            this.currentDocName = docName;
            this.currentFilePreview = null;
            this.currentFileName = '';
            this.currentFileSize = '';
            this.currentFileMime = '';
            this.$refs.fileInput.value = null;

            this.$wire.setCurrentDoc(docId);
            this.$wire.set('singleDocument', null);
        },
        uploadFile() {
            if (!this.$wire.singleDocument) {
                alert('Please select a file to upload.');
                return;
            }
            this.$wire.saveSingleDocument();
            this.showUploadModal = false;
            this.currentFilePreview = null;
            this.currentFileName = '';
            this.currentFileSize = '';
            this.currentFileMime = '';
        },
        closeModal() {
            this.showUploadModal = false;
            this.currentFilePreview = null;
            this.currentFileName = '';
            this.currentFileSize = '';
            this.currentFileMime = '';
            this.$wire.set('singleDocument', null);
            this.$wire.set('currentDocId', null);
            this.$refs.fileInput.value = null;
        }
    }"
    class="grid grid-cols-1 md:grid-cols-2 gap-4">

    @foreach ($doc_lists as $doc)
    <div class="backdrop-blur-md bg-white/80 rounded-lg p-4 shadow-sm flex flex-col justify-between">
        <span class="text-gray-800 font-medium mb-2">
            {{ $doc->codemaster->name }}
            @if($doc->is_required)
            <span class="text-red-500">*</span>
            @endif
        </span>

        <div class="flex space-x-2">
            <button
                @click="openModal({{ $doc->doc_type_id }}, '{{ $doc->codemaster->name }}')"
                class="bg-blue-600 text-white px-4 py-1 rounded hover:bg-blue-700 transition">
                Upload
            </button>

            @if(isset($existingDocuments[$doc->doc_type_id]))
            <button
                wire:click="downloadDocument({{ $existingDocuments[$doc->doc_type_id]->id }})"
                class="bg-green-600 text-white px-4 py-1 rounded hover:bg-green-700 transition">
                Download
            </button>
            @endif
        </div>
    </div>
    @endforeach

    {{-- Include UploadModal Component --}}
    <x-upload-modal 
        :currentDocExtensions="$currentDocExtensions" 
        :currentDocMaxSize="$currentDocMaxSize" />
</div>
