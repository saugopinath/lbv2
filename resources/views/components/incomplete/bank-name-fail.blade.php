<p class="text-sm text-gray-600">Old Name: {{ $item->old_value ?? 'N/A' }}</p>

<x-form.input id="bank_name_{{ $item->application_id }}" name="bank_name[{{ $item->application_id }}]" label="Correct Name" required
    wire:model="formData.bank_name.{{ $item->application_id }}" placeholder="Enter Correct Name"
    x-on:input="$el.value = $el.value.replace(/[^A-Za-z\s]/g, '')" />

@error("formData.bank_name.$item->application_id")
    <span class="text-red-600 text-sm">{{ $message }}</span>
@enderror
