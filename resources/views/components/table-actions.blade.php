{{-- resources/views/components/table-actions.blade.php --}}
<div class="flex space-x-2">
    <button
        wire:click="$dispatch('openEditModal', { applicationId: {{ $row->application_id }} })"
        class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm"
    >
        Edit
    </button>
</div>
