<div>
    <div
        x-data="{ 
            showUploadModal: false, 
            currentDocId: null, 
            currentDocName: '', 
            currentFilePreview: null,
            currentFileName: '',
            currentFileMime: '',
            
            handleFileChange(event) {
                const file = event.target.files[0];
                if (!file) {
                    this.resetFileData();
                    return;
                }

                this.currentFileName = file.name;
                this.currentFileMime = file.type || 'Unknown MIME type';

                if (this.currentFileMime.startsWith('image/')) {
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
                this.resetFileData();
                this.$refs.fileInput.value = null;
                this.$wire.setCurrentDoc(docId);
                this.$wire.set('singleDocument', null);
            },

            uploadFile() {
                if (!this.$refs.fileInput.files.length) {
                    alert('Please select a file to upload.');
                    return;
                }
                this.$wire.saveSingleDocument()
                    .then(() => {
                        this.closeModal();
                    });
            },

            closeModal() {
                this.showUploadModal = false;
                this.resetFileData();
                this.$wire.set('singleDocument', null);
                this.$wire.set('currentDocId', null);
                this.$refs.fileInput.value = null;
            },

            resetFileData() {
                this.currentFilePreview = null;
                this.currentFileName = '';
                this.currentFileMime = '';
            }
        }"
        class="grid grid-cols-1 md:grid-cols-2 gap-4"
    >
        @foreach ($doc_lists as $doc)
            <x-document-card
                :docName="$doc->codemaster->name"
                :isRequired="$doc->is_required"
                :docTypeId="$doc->doc_type_id"
                :existingDoc="$existingDocuments[$doc->doc_type_id] ?? null" />
        @endforeach

        <x-upload-modal
            :currentDocExtensions="$currentDocExtensions"
            :currentDocMaxSize="$currentDocMaxSize" />
    </div>

    <div class="flex justify-between mt-4 pl-6 pr-6">
        @if ($mode != '0')
            <x-button.danger>Previous</x-button.danger>
        @endif
        <x-button.primary type="submit">
            {{ $mode == '0' ? 'Save' : 'Save & Next' }}
        </x-button.primary>
    </div>
</div>
