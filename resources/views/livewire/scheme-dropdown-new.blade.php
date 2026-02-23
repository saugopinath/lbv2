<div class="w-full space-y-6">
    <div class="max-w-3xl mx-auto bg-white border border-gray-200 rounded-xl shadow-sm p-6">
        <x-form.select name="schemeId" label="Scheme" wire:model.live="schemeId" class="border rounded px-3 py-2 w-full"
            required>
            <option value="">-- Select --</option>
            @foreach ($schemes as $scheme)
                <option value="{{ $scheme->id }}">
                    {{ $scheme->name }}
                </option>
            @endforeach
        </x-form.select>
    </div>
</div>
