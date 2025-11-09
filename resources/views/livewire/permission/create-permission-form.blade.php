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
                <h2 class="text-lg font-semibold text-gray-800">Create Permission</h2>
                <button wire:click="cancel" class="text-gray-500 hover:text-red-500 text-xl">×</button>
            </div>
<<<<<<< HEAD
            @if($errors->has('checkname'))
            <div class="mb-4 p-3 rounded bg-red-600 text-white">
                {{ $errors->first('checkname') }}
            </div>
            @endif

=======
>>>>>>> d726694e2ff4cbf8a12d9642a72f953c3c34c7b5

            <form wire:submit.prevent="save" class="space-y-4">
                <!-- Permission Name -->
                <div>

                    <x-form.input
                        id="name"
                        name="name"
                        label="Permission Name"
                        placeholder="Enter Permission Name"
<<<<<<< HEAD
                        required wire:model="name" />
=======
                        required wire:model="name"/>
>>>>>>> d726694e2ff4cbf8a12d9642a72f953c3c34c7b5

                </div>

                <!-- Is Parent? -->
                <div>

                    <x-form.select name="is_parent" label="Is Parent?" wire:model.live="is_parent" required>
                        <option value="">--Select--</option>
                        <option value="0">Yes</option>
                        <option value="1">No</option>
                    </x-form.select>

                </div>

                @if($is_parent == 0)
                <div>
                    <x-form.select name="has_score" label="Has Score?" wire:model.live="has_score">
                        <option value="">--Select--</option>
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </x-form.select>
                </div>

                <!-- Select Parent -->
                @elseif($is_parent == 1)
                <div>
                    <x-form.select name="parent_id" label="Select Parent" wire:model="parent_id">
                        <option value="">--Select--</option>
                        @foreach($parents as $parent)
                        <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                        @endforeach
                    </x-form.select>

                </div>
                @endif
                <!-- Has Score? -->


                @if($has_score == 1)
                <div class="mt-3">
                    <x-form.input
                        id="min_score"
                        name="min_score"
                        label="Min Score"
                        placeholder="Enter Min Score"
<<<<<<< HEAD
                        wire:model="min_score" />
=======
                         wire:model="min_score" />
>>>>>>> d726694e2ff4cbf8a12d9642a72f953c3c34c7b5

                    <x-form.input
                        id="max_score"
                        name="max_score"
                        label="Max Score"
                        placeholder="Enter Max Score"
<<<<<<< HEAD
                        wire:model="max_score" />
=======
                         wire:model="max_score" />
>>>>>>> d726694e2ff4cbf8a12d9642a72f953c3c34c7b5
                </div>
                @endif


                <div class="flex justify-end space-x-2 mt-4">
<<<<<<< HEAD
                    <x-button.primary type="submit"
                        class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 cursor-pointer">
                        Save
                    </x-button.primary>
=======
                    <x-button.primary
    type="submit"
    class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 cursor-pointer"
    x-on:click="Livewire.dispatch('showLoader')"
>
    Save
</x-button.primary>

>>>>>>> d726694e2ff4cbf8a12d9642a72f953c3c34c7b5
                    <x-button.primary
                        wire:click="cancel"
                        class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 cursor-pointer">
                        Cancel
                    </x-button.primary>

                </div>
            </form>
        </div>
    </div>
<<<<<<< HEAD
</div>
=======
</div>
>>>>>>> d726694e2ff4cbf8a12d9642a72f953c3c34c7b5
