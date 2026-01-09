<div>
    @if ($show)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-6 relative">
                <h2 class="text-lg font-bold mb-4">Edit Incomplete Types (App ID: {{ $applicationId }})</h2>

                <div class="space-y-2">
                    @foreach ($data as $index => $item)
                        <div class="p-3 border rounded bg-gray-50">
                            <p class="font-semibold">{{ $item['incomplet_type']['name'] ?? 'N/A' }}</p>
                            <input type="text" class="w-full border px-2 py-1 rounded mt-1"
                                wire:model="data.{{ $index }}.corrected_value">

                        </div>
                    @endforeach

                </div>

                <div class="flex justify-end mt-4 space-x-2">
                    <button wire:click="close" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                        Cancel
                    </button>
                    <button wire:click="save" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
                        Save
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
