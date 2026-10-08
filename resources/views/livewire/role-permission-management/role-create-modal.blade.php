<!-- Modal -->
<div x-data="{ open: false, message: '' }"
    @open-modal.window="open = true"
    @close-modal.window="open = false"
    @notify.window="message = $event.detail.message; setTimeout(() => message='', 3000)"
    x-cloak>

    <!-- Success Message -->
    <div x-show="message"
        class="fixed top-4 right-4 bg-green-600 text-white px-4 py-2 rounded shadow">
        <span x-text="message"></span>
    </div>

    <!-- Modal Box -->
    <div x-show="open"
        x-transition
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 bg-opacity-40">

        <div class="bg-white rounded-lg shadow-md w-full max-w-lg p-6">

            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Create Role</h2>
                <button wire:click="cancel" class="text-gray-500 hover:text-red-500 text-xl">×</button>
            </div>

            <form wire:submit.prevent="save" class="space-y-4">
                <!-- Role Name -->
                <div>
                    <x-form.input
                        id="name"
                        name="name"
                        label="Role Name"
                        placeholder="Enter Role Name"
                        required wire:model="name" />
                </div>

                <!-- Copy Permissions from Existing Role (Optional) -->
                <div>
                    <label for="copyRoleId" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Copy Permissions from existing role <span class="text-xs text-gray-500 font-normal">(Optional)</span>
                    </label>
                    <select
                        id="copyRoleId"
                        name="copyRoleId"
                        wire:model="copyRoleId"
                        class="w-full text-sm rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 bg-white dark:bg-slate-700 dark:border-slate-600 dark:text-white py-2 px-3"
                    >
                        <option value="">-- Do not copy (Empty Permissions) --</option>
                        @foreach ($rolesList as $rId => $rName)
                            <option value="{{ $rId }}">{{ $rName }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-end space-x-2 mt-4">
                    <x-button.primary
                        type="submit"
                        class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 cursor-pointer"
                        x-on:click="Livewire.dispatch('showLoader')">
                        Save
                    </x-button.primary>

                    <x-button.primary
                        wire:click="cancel"
                        class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 cursor-pointer">
                        Cancel
                    </x-button.primary>

                </div>
            </form>
        </div>
    </div>
</div>