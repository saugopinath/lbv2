@props(['file'])

@php
    $ext = strtolower($file->getClientOriginalExtension() ?? '');
@endphp

<div class="mt-2">
    @if (in_array($ext, ['jpg','jpeg','png']))
        <img src="{{ $file->temporaryUrl() }}" alt="Preview" class="w-32 h-32 rounded border object-cover">
    @elseif ($ext === 'pdf')
        <a href="{{ $file->temporaryUrl() }}" target="_blank" class="text-blue-600 underline">
            Download PDF
        </a>
    @else
        <p class="text-red-500">Unsupported file type</p>
    @endif
</div>
