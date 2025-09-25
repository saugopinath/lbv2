<div>
    @if($show)
        <div style="position: fixed; top: 1rem; right: 1rem; z-index: 9999;"
             class="px-4 py-2 rounded shadow text-white
                    {{ $type === 'success' ? 'bg-green-600' : '' }}
                    {{ $type === 'error' ? 'bg-red-600' : '' }}
                    {{ $type === 'warning' ? 'bg-yellow-500' : '' }}">
            {{ $message }}
        </div>

        <script>
            setTimeout(() => {
                Livewire.dispatch('hideNotification');
            }, 3000);
        </script>
    @endif
</div>
