@props([
'title' => 'Upload Document',
])

<div
    x-show="showUploadModal"
    x-cloak
    x-transition
    class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center"
    @click.outside="showUploadModal = false">
    <div class="bg-white rounded shadow p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-800"
                x-text="'Upload ' + currentDocName"></h2>
            <button @click="showUploadModal = false"
                class="text-gray-500 hover:text-red-500 text-xl">×</button>
        </div>

        <!-- Custom File Input -->
        <div class="flex w-full border border-gray-300 rounded overflow-hidden">
            <!-- Custom Button -->
            <label
                for="fileInput"
                class="bg-blue-600 text-white px-4 py-2 cursor-pointer hover:bg-blue-700 text-sm flex items-center">
                Choose File
            </label>

            <!-- File Name Display -->
            <span class="flex items-center px-4 text-gray-600 text-sm truncate flex-1 bg-white">
                <span x-text="currentFilePreview ? $refs.fileInput.files[0].name : 'No file chosen'"></span>
            </span>

            <!-- Hidden Native Input -->
            <input
                id="fileInput"
                type="file"
                class="hidden"
                x-ref="fileInput"
                @change="handleFileChange($event)">
        </div>

        <!-- Preview -->
        <template x-if="currentFilePreview">
            <div class="mt-4">
                <img :src="currentFilePreview"
                    alt="Preview"
                    class="max-w-full h-48 object-contain rounded border">
            </div>
        </template>

        <!-- Actions -->
        <div class="flex justify-end space-x-2 mt-4">
            <button @click="showUploadModal = false"
                class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">Cancel</button>
            <button @click="uploadFile()"
                class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Upload</button>
        </div>
    </div>
</div>