@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'inline-flex items-center rounded text-xs justify-center px-1.5 py-0.5 bg-danger/10 text-danger']) }}>
        @foreach ((array) $messages as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif
