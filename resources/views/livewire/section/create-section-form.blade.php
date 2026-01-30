<!-- Modal -->
<div x-data="{ open: false, message: '' }" @open-modal.window="open = true" @close-modal.window="open = false"
    @notify.window="message = $event.detail.message; setTimeout(() => message='', 3000)" x-cloak>

    <!-- Success Message -->
    <div x-show="message" class="fixed top-4 right-4 bg-green-600 text-white px-4 py-2 rounded shadow">
        <span x-text="message"></span>
    </div>

    <!-- Modal Box -->
    <div x-show="open" x-transition
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 bg-opacity-40">

        <div class="bg-white rounded-lg shadow-md w-full max-w-lg p-6">

            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Create New Section</h2>
                <button wire:click="cancel" class="text-gray-500 hover:text-red-500 text-xl">×</button>
            </div>

            <form wire:submit.prevent="save" class="space-y-4">
                <!-- Permission Name -->
                <div>
                    <x-form.select name="scheme_id" label="Scheme" wire:model.live="scheme_id" :disabled="$lock" required>
                        <option value="">-- Select Scheme --</option>
                        @foreach ($schemes as $scheme)
                        <option value="{{ $scheme->id }}">
                            {{ $scheme->name }}
                        </option>
                        @endforeach
                    </x-form.select>
                    <!-- Section Name -->
                    <x-form.select label="Select Tab" wire:model.live="tab_code" name="tabId" :disabled="$lock">
                        <option value="">-- Select --</option>
                        @foreach($tabs as $tab)
                        <option value="{{ $tab->tab_code }}">{{ $tab->tab_name }}</option>
                        @endforeach
                    </x-form.select>

                    <!-- Section Short Name -->
                    <x-form.input name="section_level_name" label="Section Level Name" placeholder="Enter Level Name" wire:model.live="section_level_name" />

                    <x-form.input name="section_short_name" label="Section Short Name" placeholder="Enter Short Name"
                        required wire:model.live="section_short_name" readonly />

                </div>
                <div class="flex justify-end space-x-2 mt-4">
                    <x-button.primary type="submit"
                        class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 cursor-pointer"
                        x-on:click="Livewire.dispatch('showLoader')">
                        Save
                    </x-button.primary>

                    <x-button.primary wire:click="cancel"
                        class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 cursor-pointer">
                        Cancel
                    </x-button.primary>

                </div>
            </form>
        </div>
    </div>
</div>