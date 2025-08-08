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

            const reader = new FileReader();
            reader.onload = e => this.currentFilePreview = e.target.result;
            reader.readAsDataURL(file);
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
    class="grid grid-cols-1 md:grid-cols-2 gap-4"
>

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
                    class="bg-blue-600 text-white px-4 py-1 rounded hover:bg-blue-700 transition"
                >
                    Upload
                </button>

                @if(isset($existingDocuments[$doc->doc_type_id]))
                    <button
                        wire:click="downloadDocument({{ $existingDocuments[$doc->doc_type_id]->id }})"
                        class="bg-green-600 text-white px-4 py-1 rounded hover:bg-green-700 transition"
                    >
                        Download
                    </button>
                @endif
            </div>
        </div>
    @endforeach

    <!-- Upload Modal -->
    <div
        x-show="showUploadModal"
        x-cloak
        x-transition
        class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center"
        @click.outside="closeModal()"
    >
        <div class="bg-white rounded shadow p-6 w-full max-w-md" @click.stop>
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-800" x-text="'Upload ' + currentDocName"></h2>
                <button @click="closeModal()" class="text-gray-500 hover:text-red-500 text-xl">×</button>
            </div>

            <div class="flex w-full border border-gray-300 rounded overflow-hidden">
                <label
                    for="fileInput"
                    class="bg-blue-600 text-white px-4 py-2 cursor-pointer hover:bg-blue-700 text-sm flex items-center"
                >
                    Choose File
                </label>

                <span class="flex items-center px-4 text-gray-600 text-sm truncate flex-1 bg-white">
                    <span x-text="currentFileName || 'No file chosen'"></span>
                </span>

                <input
                    id="fileInput"
                    type="file"
                    class="hidden"
                    x-ref="fileInput"
                    wire:model="singleDocument"
                    @change="handleFileChange($event)"
                >
            </div>

            <!-- File info display -->
            <div class="mt-1 text-sm text-gray-700" x-show="currentFileName">
                <div><strong>File size:</strong> <span x-text="currentFileSize"></span></div>
                <div><strong>MIME type:</strong> <span x-text="currentFileMime"></span></div>
            </div>

            <!-- Allowed file types and max size -->
            <div class="mt-2 text-sm text-gray-700">
                <p>Allowed file types: <strong>{{ $currentDocExtensions }}</strong></p>
                <p>Max file size: <strong>{{ $currentDocMaxSize }}</strong></p>
            </div>

            <template x-if="currentFilePreview">
                <div class="mt-4">
                    <img :src="currentFilePreview" alt="Preview" class="max-w-full h-48 object-contain rounded border" />
                </div>
            </template>

            <div class="flex justify-end space-x-2 mt-4">
                <button @click="closeModal()" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">Cancel</button>
                <button @click="uploadFile()" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Upload</button>
            </div>
        </div>
    </div>
</div>
