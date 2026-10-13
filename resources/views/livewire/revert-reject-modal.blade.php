<div>
    @if ($open)
        <div class="fixed inset-0 backdrop-blur-sm flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded shadow max-w-md w-full">

                <h2 class="text-lg font-bold mb-4">
                    Confirm {{ $action === 'approver' ? ($warningMessage ? 'Approval / Forward' : 'Approval') : ($action === 'verification' ? 'Verification / Forward' : ucfirst($action)) }}
                </h2>

                @if($warningMessage)
                    <div class="mb-4 p-3 bg-amber-50 border border-amber-300 text-amber-900 rounded-lg text-sm flex items-start gap-2.5">
                        <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <div>
                            <span class="font-semibold block text-amber-900 mb-0.5">Workflow Notice:</span>
                            <span>{{ $warningMessage }}</span>
                        </div>
                    </div>
                @endif

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
