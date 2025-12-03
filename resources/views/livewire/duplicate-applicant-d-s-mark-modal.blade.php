<div
    x-data="{ open: false }"
    x-show="open"
    x-cloak
    @show-modal.window="open = true"
    @keydown.escape.window="open = false"
    wire:ignore.self
    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
>
    <div 
        class="bg-white rounded-lg p-6 w-1/2 shadow-xl"
        @click.away="open = false"
    >
        <h2 class="text-xl font-bold mb-4">Applicant Details</h2>
        
        <p class="text-gray-700">
            Selected Applicant ID: 
            <span class="font-bold text-blue-600">{{ $applicantId ?? 'Loading...' }}</span>
        </p>

        <div class="mt-4 text-right">
            <button @click="open = false" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition">
                Close
            </button>
        </div>
    </div>
</div>