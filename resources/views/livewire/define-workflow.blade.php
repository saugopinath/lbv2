<div class="bg-white shadow-xl rounded-2xl p-6 space-y-4">

    {{-- Step Navigation --}}
    @foreach ($steps as $index => $step)
        <div wire:click="$set('currentStep', {{ $index }})"
            class="cursor-pointer block border rounded-xl p-4 transition
            {{ $currentStep === $index ? 'border-purple-600 bg-purple-50' : 'border-gray-200 hover:bg-gray-50' }}">

            <div class="flex items-start space-x-4">

                <div
                    class="w-10 h-10 flex items-center justify-center rounded-full
                    {{ $currentStep === $index ? 'bg-purple-600 text-white' : 'bg-gray-300 text-white' }}">
                    {{ $step['step'] }}
                </div>

                <div>
                    <h3 class="font-semibold text-lg">
                        {{ $step['title'] }}
                    </h3>
                    <p class="text-sm text-gray-500">
                        {{ $step['description'] }}
                    </p>
                </div>

            </div>
        </div>
    @endforeach

    {{-- Render Selected Livewire Component --}}
    <div class="mt-6">
        <livewire:dynamic-component
            :component="$steps[$currentStep]['component']"
            :schemeId="$schemeId"
            :key="$currentStep"
        />
    </div>

</div>
