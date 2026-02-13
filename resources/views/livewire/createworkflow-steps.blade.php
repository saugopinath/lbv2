<div>
    @if ($originalrolerank)
        <div class="bg-white shadow rounded-xl p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-xl font-bold text-indigo-700">
                    Create Work Flow Steps
                </h1>
            </div>
            <form wire:submit.prevent="save" class="space-y-4">
                <div class="grid gap-6 md:grid-cols-2 mb-2 pl-4 pr-4">
                    <div>
                        <x-form.input name="noofSteps" label="No of Steps" wire:model.live="noofSteps"
                            x-on:input="$event.target.value = $event.target.value.replace(/[^0-9]/g, '').slice(0,2)"
                            required />
                    </div>
                </div>
                @if ($noofSteps > 0)
                    <div class="mt-4">
                        @foreach ($labels as $index => $value)
                            <div class="grid gap-6 md:grid-cols-2 mb-2 pl-4 pr-4">
                                <div>
                                    <x-form.input id="labels.{{ $index }}" name="labels.{{ $index }}"
                                        label="Label Name {{ $index + 1 }}"
                                        placeholder="Label Name {{ $index + 1 }}"
                                        wire:model="labels.{{ $index }}" required />
                                </div>
                            </div>
                        @endforeach
                        @if ($already)
                            <p
                                class="text-sm font-semibold text-green-700 bg-green-100 px-3 py-1 rounded-lg inline-block">
                                Already Done
                            </p>
                        @else
                            <div class="mt-6 pl-4">
                                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg">
                                    Submit
                                </button>
                            </div>
                        @endif
                    </div>
                @endif
            </form>
        </div>
    @else
        <p class="text-sm font-semibold text-red-700 bg-red-100 px-3 py-1 rounded-lg inline-block">
            Please Set Original Role Rank Hierarchy
        </p>
    @endif
</div>