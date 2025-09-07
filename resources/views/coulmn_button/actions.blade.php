<div class="flex items-center space-x-2">
    {{-- Edit Button --}}
<button 
    wire:click="$dispatch('edit-record', { scheme_id: {{ $row->scheme_id }}, master_code: '{{ $row->master_code }}' })"
    class="p-2 rounded-lg bg-blue-500 hover:bg-blue-600 text-white transition"
    title="Edit"
>
        {{-- Heroicons Pencil SVG --}}
        <svg xmlns="http://www.w3.org/2000/svg" 
             class="w-5 h-5" 
             fill="none" 
             viewBox="0 0 24 24" 
             stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M15.232 5.232l3.536 3.536M9 11l6.586-6.586a2 2 0 012.828 0l1.172 1.172a2 2 0 010 2.828L13 15l-4 1 1-4z" />
        </svg>
    </button>
</div>
