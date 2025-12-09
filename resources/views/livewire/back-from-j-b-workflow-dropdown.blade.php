
        <x-form.select label="Application Type" required>
            @foreach ($types as $type)
            <option value="{{ $type->id }}">{{ $type->name }}</option>
            @endforeach
        </x-form.select>
