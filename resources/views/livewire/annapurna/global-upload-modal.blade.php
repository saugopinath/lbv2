    <div x-data="{
        showUploadModal: @entangle('showUploadModal'),
        currentDocId: @entangle('currentDocId'),
        currentDocName: '',
        currentFileName: '',
        currentFilePreview: null,
        errorMessage: '',

        openModal(event) {
            this.currentDocName = event.detail.docName || '';
            this.currentFilePreview = null;
            this.currentFileName = '';
            this.errorMessage = '';
            if (this.$refs.fileInput) this.$refs.fileInput.value = null;
            this.currentDocId = event.detail.docId;
            this.$wire.call('setCurrentDoc', event.detail.docId);
            this.showUploadModal = true;
        },

        closeModal() {
            this.showUploadModal = false;
            this.currentFilePreview = null;
            this.currentFileName = '';
            this.errorMessage = '';
            if (this.$refs.fileInput) this.$refs.fileInput.value = null;
            this.$wire.call('resetSingleDocumentErrors');
        },

        handleFileChange(event) {
            const file = event.target.files[0];
            if (!file) return;

            this.currentFileName = file.name;
            const mime = file.type;

            if (mime.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = e => this.currentFilePreview = e.target.result;
                reader.readAsDataURL(file);
            } else {
                this.currentFilePreview = null;
            }
        },

        async uploadFile() {
            if (!this.$refs.fileInput.files.length) {
                this.errorMessage = 'Please select a file to upload.';
                return;
            }
            this.errorMessage = '';
            try {
                await this.$wire.saveSingleDocument();
                if(this.$refs.fileInput) this.$refs.fileInput.value = null;
            } catch (e) {
                this.errorMessage = 'Something went wrong while uploading.';
            }
        }
    }" @open-upload-modal.window="openModal($event)">
    
    <!-- The Upload Modal -->
    <div x-show="showUploadModal" x-cloak x-transition class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center" @click.outside="closeModal()">
        <div class="bg-white rounded shadow-lg p-6 w-full max-w-md" @click.stop>
            <!-- Header -->
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-800" x-text="'Upload ' + currentDocName"></h2>
                <button type="button" @click="closeModal()" class="text-gray-500 hover:text-red-500 text-xl font-bold">&times;</button>
            </div>

            <!-- File Input -->
            <div class="flex w-full border border-gray-300 rounded overflow-hidden">
                <label @click="$refs.fileInput.click()" class="bg-blue-600 text-white px-4 py-2 cursor-pointer hover:bg-blue-700 text-sm flex items-center font-semibold">
                    Choose File
                </label>
                <span class="flex items-center px-4 text-gray-600 text-sm truncate flex-1 bg-gray-50">
                    <span x-text="currentFileName || 'No file chosen'"></span>
                </span>
                <input type="file" class="hidden" x-ref="fileInput" wire:model="singleDocument" @change="handleFileChange($event)">
            </div>

            <!-- Allowed Info -->
            <div class="mt-3 text-xs text-gray-600 space-y-1">
                <p>Allowed file types: <strong class="text-gray-800">{{ $currentDocExtensions }}</strong></p>
                <p>Max file size: <strong class="text-gray-800">{{ $currentDocMaxSize }}</strong></p>
            </div>

            @error('singleDocument')
                <div class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</div>
            @enderror
            <div x-show="errorMessage" x-text="errorMessage" class="mt-2 text-sm text-red-600 font-medium"></div>

            <!-- Image Preview -->
            <template x-if="currentFilePreview">
                <div class="mt-4">
                    <img :src="currentFilePreview" alt="Preview" class="max-w-full h-40 object-contain rounded border border-gray-200" />
                </div>
            </template>

            <!-- Buttons -->
            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" @click="closeModal()" class="px-4 py-2 bg-gray-100 text-gray-800 rounded hover:bg-gray-200 transition font-semibold text-sm">
                    Cancel
                </button>

                <button type="button" @click="uploadFile()" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition font-semibold text-sm flex items-center" wire:loading.attr="disabled" wire:target="saveSingleDocument">
                    <span wire:loading.remove wire:target="saveSingleDocument">Upload Document</span>
                    <span wire:loading wire:target="saveSingleDocument" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Uploading...
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>
