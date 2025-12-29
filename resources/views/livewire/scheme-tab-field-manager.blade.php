<div class="max-w-6xl mx-auto bg-white rounded-xl shadow p-6 space-y-6">

    {{-- Scheme --}}
    <x-form.select label="Select Scheme" wire:model.live="schemeId" :disabled="$lockScheme">
        <option value="">-- Select --</option>
        @foreach($schemes as $scheme)
            <option value="{{ $scheme->id }}">{{ $scheme->name }}</option>
        @endforeach
    </x-form.select>

    @if($schemeId)
        <div class="grid grid-cols-12 gap-6">

            {{-- Tabs --}}
            <div class="col-span-3 border-r">
                <h3 class="font-semibold mb-3">Tabs</h3>

                <ul class="space-y-2">
                    @foreach($tabs as $tab)
                            <li>
                                <button wire:click="selectTab({{ $tab->tab_code }})" class="w-full text-left px-3 py-2 rounded
                                    {{ $activeTabCode === $tab->tab_code
                        ? 'bg-indigo-100 font-semibold'
                        : 'hover:bg-gray-100' }}">
                                    {{ $tab->masterTab->tab_name }}
                                </button>
                            </li>
                    @endforeach
                </ul>
            </div>

            {{-- Available Fields (Scrollable) --}}
            <div class="col-span-3">
                <h3 class="font-semibold mb-3">Available Fields</h3>

                <div class="max-h-96 overflow-y-auto border rounded p-2">
                    <ul class="space-y-2">
                        @forelse($availableFields as $field)
                            <li class="flex justify-between items-center bg-gray-50 p-2 rounded">
                                <span>{{ $field->field_name }}</span>
                                <button wire:click="addField('{{ $field->field_id }}')" class="text-green-600 font-bold">
                                    +
                                </button>
                            </li>
                        @empty
                            <li class="text-sm text-gray-400 text-center py-4">
                                No available fields
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>

            {{-- Selected Fields --}}
            <div class="col-span-3">
                <h3 class="font-semibold mb-3">Selected Fields</h3>

                <ul class="space-y-2">
                    @forelse($selectedFields as $fieldId => $fieldName)
                        <li class="flex justify-between items-center bg-green-50 p-2 rounded">
                            <span>{{ $fieldName }}</span>
                            <button wire:click="removeField('{{ $fieldId }}')" class="text-red-600 font-bold">
                                ✕
                            </button>
                        </li>
                    @empty
                        <li class="text-sm text-gray-400 text-center py-4">
                            No fields selected
                        </li>
                    @endforelse
                </ul>
            </div>

        </div>
    @endif
</div>