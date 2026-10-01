<div>
    <div class="space-y-4">
        <div>
            <x-form.select name="schemeId" label="Scheme" wire:model.live="schemeId" class="border rounded px-3 py-2 w-full"
                required>
                <option value="">-- Select Scheme --</option>
                @foreach ($schemes as $scheme)
                <option value="{{ $scheme->id }}">
                    {{ $scheme->name }}
                </option>
                @endforeach
            </x-form.select>
        </div>

        @if ($enableModuleSelection && $schemeId)
            <div class="pt-2">
                <x-form.select name="moduleId" label="Workflow Module" wire:model.live="moduleId" class="border rounded px-3 py-2 w-full"
                    required>
                    <option value="">-- Select Module --</option>
                    @foreach ($modules as $mod)
                    <option value="{{ $mod->id }}">
                        {{ $mod->module_name }} ({{ $mod->module_code }})
                    </option>
                    @endforeach
                </x-form.select>
            </div>
        @endif
    </div>

    <!-- Workflow Not Configured Modal -->
    <x-modal wire:model="showNotConfiguredModal">
        <x-slot name="body">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 w-12 h-12 rounded-full bg-amber-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900" id="modal-title">
                        Workflow Not Defined
                    </h3>
                    <p class="mt-2 text-sm text-gray-600 leading-relaxed">
                        {{ $notConfiguredMessage }}
                    </p>
                </div>
            </div>
        </x-slot>

        <x-slot name="actions">
            <div class="flex justify-end">
                <x-button.primary type="button" wire:click="closeNotConfiguredModal">
                    Understood
                </x-button.primary>
            </div>
        </x-slot>
    </x-modal>
</div>
