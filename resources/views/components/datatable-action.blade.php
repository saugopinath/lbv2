<x-link-button href="{{ route('draft-application.edit', $row->id) }}" color="blue">
    Edit
</x-link-button>

<x-link-button href="{{ route('draft-application.view', $row->id) }}" color="pink">
    View
</x-link-button>