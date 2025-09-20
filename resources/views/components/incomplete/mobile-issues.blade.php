<div class="p-4 mb-4 border rounded-lg bg-gray-50 shadow-sm">
    <h2 class="font-semibold text-lg text-blue-700 mb-2">
        Mobile Related Issues
    </h2>

    <ul class="list-disc list-inside text-sm text-gray-700 mb-2">
        @foreach ($mobileIssues as $issueItem)
            <li>{{ $issueItem->incompletType->name }}</li>
        @endforeach
    </ul>

    <p class="text-sm text-gray-600">
        Old Mobile: {{ $issueItem->old_value['mobile_no'] ?? 'N/A' }}
    </p>

    {{-- New Mobile Input --}}
    <div class="mt-2">
        <x-form.input id="dup_mobile_{{ $mobileIssues[0]->application_id }}" name="dup_mobile" label="New Mobile Number"
            placeholder="Enter New Mobile" required value="{{ old('dup_mobile') }}"
            x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0,10)" />



        @if ($errors->has('mobile'))
            <span class="text-red-800 text-sm">
                <li>{{ $errors->first('mobile') }}</li>
        @endif

        @error('duplicate_check')
            <span class="text-red-600 text-sm">{{ $message }}</span>
        @enderror
    </div>
</div>
