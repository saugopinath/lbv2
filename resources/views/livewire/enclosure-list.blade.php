<div>
    @if ($mode != '0')
    <x-button.danger>Previous</x-button.danger>
    @endif
    <x-button.danger>
        {{ $mode == '0' ? 'Save' : 'Save & Next' }}
    </x-button.danger>
</div>