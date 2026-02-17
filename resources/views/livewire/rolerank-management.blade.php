<div class="border-t pt-4 bg-gray-50 p-6 rounded-xl">

    <div class="mb-6 flex justify-between items-center">
        <h3 class="text-base font-bold text-gray-800">
            Role Hierarchy Management
        </h3>

        <button wire:click="saveMapping"
                wire:loading.attr="disabled"
                class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-8 py-2.5 rounded-lg shadow-lg">
            <span wire:loading.remove>Save Hierarchy</span>
            <span wire:loading>Saving...</span>
        </button>
    </div>

    <ul  class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4"
        x-data
        x-init="
            new Sortable($el, {
                animation: 150,
                handle: '.drag-handle',
                ghostClass: 'bg-blue-50',
                onEnd: (evt) => {
                    let orderedIds = Array.from(evt.to.children)
                        .map(el => el.dataset.id);

                    $wire.updateOrder(orderedIds);
                }
            });
        ">

        @foreach ($roles as $id => $role)
            <li wire:key="role-item-{{ $role['id'] }}"
                data-id="{{ $role['id'] }}"
                class="relative flex flex-col bg-white border 
                {{ ($role['same_as_prev'] ?? false) ? 'border-blue-500 ring-2 ring-blue-50' : 'border-gray-200' }}
                rounded-lg shadow-sm">

                <div class="flex items-center justify-between px-6 py-4">

                    <div class="flex items-center gap-4">
                        <span class="drag-handle text-gray-400 cursor-move hover:text-blue-500 text-xl">
                            ☰
                        </span>

                        <div>
                            <span class="text-sm font-bold text-gray-800">
                                {{ $role['name'] }}
                            </span>

                            <span class="text-[11px] block text-gray-400 mt-1">
                                Current Rank: {{ $role['rank'] }}
                            </span>
                        </div>
                    </div>

                    @if(!$loop->first)
                        <label class="flex items-center gap-2 cursor-pointer bg-gray-50 px-4 py-2 rounded-lg border border-gray-100">
                            <span class="text-[10px] font-black text-gray-400 uppercase">
                                Same rank as above?
                            </span>

                            <input type="checkbox"
                                   wire:model.defer="roles.{{ $role['id'] }}.same_as_prev"
                                   class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </label>
                    @endif

                </div>
            </li>
        @endforeach

    </ul>
</div>
