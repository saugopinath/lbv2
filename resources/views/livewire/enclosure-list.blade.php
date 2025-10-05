<div>
    <div x-data="{
        {{--  showUploadModal: false,  --}}
        showUploadModal: @entangle('showUploadModal'),
            currentDocId: @entangle('currentDocId'),
            {{--  currentDocId: null,  --}}
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
                this.currentDocName = docName || '';
                this.resetFileData();
                if (this.$refs.fileInput) this.$refs.fileInput.value = null;
                this.$wire.call('resetSingleDocumentErrors');
                this.$wire.set('singleDocument', null);
                this.currentDocId = docId;
                this.$wire.call('setCurrentDoc', docId);
                this.showUploadModal = true;
    
            },
    
            async uploadFile() {
                    if (!this.$refs.fileInput.files.length) {
                        this.errorMessage = 'Please select a file to upload.';
                        return;
                    }
    
                    this.errorMessage = ''; // reset error if file selected
    
                    try {
                        // Livewire Method Call
                        await this.$wire.saveSingleDocument();
    
                        // Reset data after upload success
                        this.resetFileData();
                        if (this.$refs.fileInput) this.$refs.fileInput.value = null;
                    } catch (e) {
                        // Focus back on file input if error occurs
                        if (this.$refs.fileInput) this.$refs.fileInput.focus();
                        this.errorMessage = 'Something went wrong while uploading.';
                    }
                },
    
                closeModal() {
                    this.showUploadModal = false;
                    this.errorMessage = '';
                    this.currentFileName = '';
                    this.currentFilePreview = '';
                    this.resetFileData();
                    if (this.$refs.fileInput) this.$refs.fileInput.value = null;
                    this.$wire.set('singleDocument', null);
                    this.$wire.call('resetSingleDocumentErrors');
                },
    
                resetFileData() {
                    this.currentFilePreview = null;
                    this.currentFileName = '';
                    this.currentFileMime = '';
                    this.errorMessage = '';
                }
    
    }" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach ($doc_lists as $doc)
            <x-document-card :docName="$doc->codemaster->name" :isRequired="$doc->is_required" :docTypeId="$doc->doc_type_id" :existingDoc="$existingDocuments[$doc->doc_type_id] ?? null" :xIsDuplicate="$is_page == 1 ? 1 : 0"
                :showErrors="$showErrors ?? false" />
        @endforeach

        <x-upload-modal :currentDocExtensions="$currentDocExtensions" :currentDocMaxSize="$currentDocMaxSize" />
    </div>
    <div class="flex justify-between mt-4 pl-6 pr-6">
        @if (!$is_page && empty($doc_type_id_array_list))
            @if ($mode != '0')
                <x-button.danger wire:click="$dispatch('goPrevious')">Previous</x-button.danger>
            @endif
        @endif
        @php
            $missingRequired = collect($doc_lists)->filter(function ($doc) use ($existingDocuments) {
                return $doc->is_required && empty($existingDocuments[$doc->doc_type_id] ?? null);
            });
        @endphp
        @if (!$is_page && empty($doc_type_id_array_list))
            @if ($missingRequired->isNotEmpty())
                <x-button.primary type="button" wire:click="$set('showErrors', true)">
                    {{ $mode == '0' ? 'Save' : 'Save & Next' }}
                </x-button.primary>
            @else
                <x-button.primary type="button" wire:click="save">
                    {{ $mode == '0' ? 'Save' : 'Save & Next' }}
                </x-button.primary>
            @endif
        @endif
    </div>
</div>
