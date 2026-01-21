(function () {

    function clearSelect(select) {
        if (select) select.innerHTML = '<option value="">--Select--</option>';
    }

    function fillSelect(select, list) {
        clearSelect(select);
        list.forEach(row => {
            const opt = document.createElement('option');
            opt.value = row.id;
            opt.textContent = row.text;
            select.appendChild(opt);
        });
    }

    function syncLivewire(select) {
        const root = select.closest('[wire\\:id]');
        if (!root) return;

        const wireKey = select.dataset.wire;
        if (!wireKey) return;

        Livewire.find(root.getAttribute('wire:id'))
            ?.set('formData.' + wireKey, select.value);
    }

    function init() {
        if (!window.masterDataV2) return;

        const md = window.masterDataV2;

        document.querySelectorAll('[wire\\:id]').forEach(root => {

            const district  = root.querySelector('select[data-field="district"]');
            const assembly  = root.querySelector('select[data-field="assembly"]');
            const urban     = root.querySelector('select[data-field="rural_urban"]');
            const localbody = root.querySelector('select[data-field="block"]');
            const gpward    = root.querySelector('select[data-field="panchayat"]');

            if (!district) return;

            if (!district.dataset.loaded) {
                fillSelect(district, md.districts || []);
                district.dataset.loaded = '1';
            }

            district.onchange = () => {
                if (assembly && md.assemblies) {
                    fillSelect(
                        assembly,
                        md.assemblies.filter(a => a.district_code == district.value)
                    );
                }

                if (urban) urban.value = '';
                clearSelect(localbody);
                clearSelect(gpward);

                syncLivewire(district);
            };

            if (urban) {
                if (!urban.dataset.loaded) {
                    fillSelect(urban, md.rural_urban || []);
                    urban.dataset.loaded = '1';
                }

                urban.onchange = () => {
                    clearSelect(localbody);
                    clearSelect(gpward);

                    if (urban.value == 2 && md.blocks) {
                        fillSelect(
                            localbody,
                            md.blocks.filter(b => b.district_code == district.value)
                        );
                    }

                    if (urban.value == 1 && md.ulbs) {
                        fillSelect(
                            localbody,
                            md.ulbs.filter(u => u.district_code == district.value)
                        );
                    }

                    syncLivewire(urban);
                };
            }

            if (localbody) {
                localbody.onchange = () => {
                    clearSelect(gpward);

                    if (urban.value == 2 && md.gps) {
                        fillSelect(
                            gpward,
                            md.gps.filter(g =>
                                g.district_code == district.value &&
                                g.block_code == localbody.value
                            )
                        );
                    }

                    if (urban.value == 1 && md.ulb_wards) {
                        fillSelect(
                            gpward,
                            md.ulb_wards.filter(w =>
                                w.urban_body_code == localbody.value
                            )
                        );
                    }

                    syncLivewire(localbody);
                };
            }

            if (gpward) {
                gpward.onchange = () => syncLivewire(gpward);
            }
        });
    }

    window.addEventListener('masterdata:ready', init);

    document.addEventListener('livewire:load', () => {
        Livewire.hook('message.processed', init);
    });

})();
