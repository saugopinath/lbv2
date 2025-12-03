<div
    x-data="{ open: false }"
    x-show="open"
    @show-modal.window="open = true"
    @keydown.escape.window="open = false"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
>
    <div class="bg-white rounded-lg p-6 w-1/2">
        <h2 class="text-xl font-bold mb-4">Applicant Details</h2>
        <div class="mt-4 text-right">
            <button @click="open = false" class="bg-red-500 text-white px-4 py-2 rounded">Close</button>
        </div>
    </div>
</div>
