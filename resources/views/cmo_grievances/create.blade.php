<x-layouts.app>

    <form action="{{ route('cmo-grievances.store') }}" method="POST">
        @csrf

        {{--  @livewire('filter-lgd-master', ['login_type' => $login_type])  --}}
          <livewire:incomplete.mismatch-high :item="$item" :wire:key="'mismatch-high-'.$item->id" />

        <div class="flex justify-end mt-4">
            <x-button.primary type="submit" style="background: blue;">
                Submit
            </x-button.primary>
        </div>

    </form>

</x-layouts.app>
