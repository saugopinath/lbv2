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
            <div class="p-5 sm:p-6 rounded-2xl bg-amber-50/70 border border-amber-200/80 flex flex-col sm:flex-row items-center sm:items-start gap-4 text-center sm:text-left transition-all">
                <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-amber-100 border border-amber-200/80 text-amber-600 flex items-center justify-center shadow-sm">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div class="space-y-1.5 flex-1">
                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2">
                        <h3 class="text-lg font-bold text-gray-900 tracking-tight" id="modal-title">
                            Workflow Not Defined
                        </h3>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                            Attention
                        </span>
                    </div>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        {{ $notConfiguredMessage }}
                    </p>
                </div>
            </div>
        </x-slot>

        <x-slot name="actions">
            <div class="flex justify-end w-full">
                <x-button.primary type="button" wire:click="closeNotConfiguredModal">
                    Understood
                </x-button.primary>
            </div>
        </x-slot>
    </x-modal>
</div>
