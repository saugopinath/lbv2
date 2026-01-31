
(function () {

    let schemeJson = null;

    // Receive JSON from Livewire hydrate()
    document.addEventListener('scheme-json-loaded', e => {
        schemeJson = e.detail.json;
        applyDependencies();
    });

    function applyDependencies() {

        if (!schemeJson || !schemeJson.tabs) return;

        schemeJson.tabs.forEach(tab => {

            (tab.fields || []).forEach(field => {

                if (!field.dependent_on || !field.dependent_on_values) return;

                const targetName = field.field_name;
                const controllerName = field.dependent_on;
                const allowedValues = Object.values(field.dependent_on_values).map(String);

                const target = document.querySelector(`[name="${targetName}"]`);
                const controller = document.querySelector(`[name="${controllerName}"]`);

                if (!target || !controller) return;

                // 🔥 THIS is the main fix (x-form.input compatible)
                const wrapper = target.closest('div');
                if (!wrapper) return;

                const toggle = () => {
                    const val = String(controller.value || '');

                    if (allowedValues.includes(val)) {
                        wrapper.classList.remove('hidden');
                        target.setAttribute('required', 'required');
                    } else {
                        wrapper.classList.add('hidden');
                        target.removeAttribute('required');
                        target.value = '';

                        // clear Livewire model
                        const lw = target.closest('[wire\\:id]');
                        if (lw && window.Livewire) {
                            Livewire.find(lw.getAttribute('wire:id'))
                                ?.set(`formData.${targetName}`, null);
                        }
                    }
                };

                controller.removeEventListener('change', toggle);
                controller.addEventListener('change', toggle);

                toggle(); // initial state
            });
        });
    }

    // rerun after Livewire DOM updates
    document.addEventListener('livewire:load', () => {
        Livewire.hook('message.processed', applyDependencies);
    });

})();

