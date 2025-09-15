<div class="p-4 mb-4 border rounded-lg bg-gray-50 shadow-sm">
    <h2 class="font-semibold text-lg text-blue-700 mb-2">
        Mobile Related Issues
    </h2>

    <ul class="list-disc list-inside text-sm text-gray-700 mb-2">
        @foreach ($mobileIssues as $issueItem)
            <li>{{ $issueItem->incompletType->name }}</li>
        @endforeach
    </ul>
<p class="text-sm text-gray-600">Old Mobile: {{ $item->old_value ?? 'N/A' }}</p>
    <x-form.input id="dup_mobile_{{ $mobileIssues[0]->application_id }}"
        name="dup_mobile[{{ $mobileIssues[0]->application_id }}]" label="New Mobile Number" required
        wire:model="formData.new_mobile.{{ $mobileIssues[0]->application_id }}" placeholder="Enter New Mobile"
        x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0,10)" />


    @error("formData.new_mobile.{$mobileIssues[0]->application_id}")
        <span class="text-red-600 text-sm">{{ $message }}</span>
    @enderror
    
</div>
