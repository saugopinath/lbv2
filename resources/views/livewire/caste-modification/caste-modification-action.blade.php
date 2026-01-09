<div>
    <div class="flex justify-center">
        <x-button.loading-button action="openModal" text="Take Action"></x-button.loading-button>
    </div>
    <!-- Modal -->
    @if($showModal)
        <div class="fixed inset-0 flex items-center justify-center bg-black/50 z-50">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg w-full max-w-md p-6">
                <!-- Dynamic Heading -->
                <h2 class="text-lg font-semibold mb-4 text-indigo-700 dark:text-indigo-300">
                    {{ $heading }}
                </h2>

                <!-- Dropdown -->
                {{-- <div class="mb-4">
                    <x-form.select name="action" id="action" label="Select Action" wire:model.live="action"
                        placeholder="-- Choose Action --" required>
                        <option value="">-- Choose Action --</option>
                        @foreach($availableActions as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </x-form.select>
                </div>
                <div class="mb-4">
                    <x-form.input type="textarea" wire:model.defer="remark" placeholder="Enter remark" id="remark"
                        name="remark" label="Remark" required />
                </div> --}}
                <!-- Dropdown -->
                <div class="mb-4">
                    <x-form.select name="action" id="action" label="Select Action" wire:model.live="action"
                        placeholder="-- Choose Action --" required>
                        <option value="">-- Choose Action --</option>
                        @foreach($availableActions as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </x-form.select>
                </div>

                <!-- Remark textarea only when revert selected -->
                @if($action == '2204')
                    <div class="mb-4">
                        <x-form.input type="textarea" wire:model.live="remark" placeholder="Enter remark" id="remark"
                            name="remark" label="Remark" required />
                    </div>
                @endif

                <!-- Buttons -->
                <div class="flex justify-end space-x-3">
                    <x-button.loading-button action="submit" text="Submit" color="green"></x-button.loading-button>
                    <x-button.loading-button action="closeModal" text="Cancel" color="orange"></x-button.loading-button>
                </div>
            </div>
        </div>
    @endif
</div>
