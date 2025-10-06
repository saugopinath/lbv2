
<div class="p-4 mb-4 border rounded-lg bg-gray-50 shadow-sm">
    <h2 class="font-semibold text-lg text-blue-700 mb-2">
        Mobile Related Issues
    </h2>

    <ul class="list-disc list-inside text-sm text-gray-700 mb-2">
        @foreach ($mobileIssues as $issueItem)
            <li>{{ $issueItem->incompletType->name }}</li>
        @endforeach
    </ul>

    <p class="grid gap-6 mb-2 md:grid-cols-3 pl-4 pr-4">
        Old Mobile: {{ $issueItem->old_value['mobile_no'] ?? 'N/A' }}
    </p>

    <div class="mt-2">
        @if (!empty($stage) && in_array($stage, ['verifier', 'revert']))
            
        {{--  @dump('ok2');  --}}
            <div class="grid gap-6 mb-2 md:grid-cols-3 pl-4 pr-4">
                <x-form.input id="dup_mobile_{{ $mobileIssues[0]->application_id }}" name="dup_mobile"
                label="New Mobile Number" placeholder="Enter New Mobile" required
                wire:model.defer="formData.dup_mobile.{{ $mobileIssues[0]->application_id }}"
                x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0,10)" />
            </div>

        @elseif (!empty($stage) && $stage === 'approver')
            <p class="text-sm text-gray-700">
                <strong>New Mobile:</strong> {{ $issueItem->new_value['mobile_no'] ?? 'Not Provided' }}
            </p>
        @endif

        @if ($errors->has('mobile'))
            <span class="text-red-600 text-sm">
                <li>{{ $errors->first('mobile') }}</li>
            </span>
        @endif
    </div>
</div>
