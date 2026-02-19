<div>
    @if ($open)
        <div class="fixed inset-0 backdrop-blur-sm flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded shadow max-w-md w-full">

                <h2 class="text-lg font-bold mb-4">
                    Confirm {{ ucfirst($action) }}
                </h2>

                <form wire:submit.prevent="confirm">

                    {{-- Cause only for Revert & Reject --}}
                    @if(in_array($action, ['revert','reject']))
                        <x-form.select 
                            name="cause" 
                            id="cause" 
                            label="{{ ucfirst($action) }} Cause:" 
                            wire:model="cause" 
                            required>

                            <option value="">Select</option>

                            @foreach ($revertrejectCauses as $causeItem)
                                <option value="{{ $causeItem['id'] }}">
                                    {{ $causeItem['name'] }}
                                </option>
                            @endforeach

                        </x-form.select>
                    @endif


                    {{-- Remark always visible --}}
                    <x-form.input 
                        type="textarea" 
                        wire:model.defer="remark" 
                        placeholder="Enter remark"
                        id="remark"
                        name="remark"
                        label="Remark"
                        required
                    />

                    <div class="flex space-x-2 mt-4 justify-end">

                        <button type="submit"
                            class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                            Yes, {{ ucfirst($action) }}
                        </button>

                        <button type="button"
                            wire:click="close"
                            class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400">
                            Cancel
                        </button>

                    </div>
                </form>

            </div>
        </div>
    @endif
</div>
