<div class="p-4 mb-4 border rounded-lg bg-gray-50 shadow-sm">
    <h2 class="font-semibold text-lg text-blue-700 mb-2">
        Mobile Related Issues
    </h2>

    <ul class="list-disc list-inside text-sm text-gray-700 mb-2">
        @foreach ($mobileIssues as $issueItem)
            <li>{{ $issueItem->incompletType->name }}</li>
        @endforeach
    </ul>



    <div class="mt-2">
        <x-form.input id="dup_mobile_{{ $item->application_id }}"
            name="dup_mobile[{{ $item->application_id }}]" label="New Mobile Number"
            wire:model.live="formData.new_mobile.{{ $item->application_id }}" placeholder="Enter New Mobile" required
            x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0,10)" />

        {{-- Error Messages --}}
        @error('formData.new_mobile.' . $item->application_id)
            <span class="text-red-600 text-sm">{{ $message }}</span>
        @enderror

        @error('duplicate_check')
            <span class="text-red-600 text-sm">{{ $message }}</span>
        @enderror
    </div>

    <div class="mt-4">
        <x-button.primary wire:click="submit">
            Save Mobile
        </x-button.primary>
    </div>

    @if (session()->has('success'))
        <div class="text-green-600 mt-2">
            {{ session('success') }}
        </div>
    @endif
</div>
