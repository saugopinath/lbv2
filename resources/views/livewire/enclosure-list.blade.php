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